<?php
function run($cmd) {
    $output = shell_exec($cmd);
    return is_string($output) ? trim($output) : '';
}

function get_motherboard_serial() {
    return implode('-', array_filter([
        run('cat /var/lib/dbus/machine-id'),
        str_replace(' ', '_', run('cat /sys/class/dmi/id/board_name')),
        str_replace(' ', '_', run('cat /sys/class/dmi/id/bios_vendor')),
        str_replace(' ', '_', run('cat /sys/class/dmi/id/bios_version')),
        str_replace('/', '_', run('cat /sys/class/dmi/id/bios_date'))
    ]));
}

function get_disk_serial() {
    $serial = run("udevadm info --query=property --name=/dev/sda | grep ID_SERIAL_SHORT | cut -d= -f2");
    return $serial ?: run("blkid -s UUID -o value /dev/sda1") ?: 'UNKNOWN';
}

function get_db_value($pdo, $name) {
    if (stripos($name, 'select') === 0) {
        $stmt = $pdo->query($name);
        return $stmt ? $stmt->fetchColumn() : '';
    }
    $stmt = $pdo->prepare("SELECT config_value FROM config WHERE config_name = ?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn() ?: '';
}

function update_config($pdo, $name, $value) {
    $stmt = $pdo->prepare("UPDATE config SET config_value = ? WHERE config_name = ?");
    $stmt->execute([$value, $name]);
}

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); // Correct: /opt/obzora
$dotenv->load();

$logFile = '/opt/obzora/logs/check.log';
$timestamp = time();

try {
    $pdo = new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    file_put_contents($logFile, json_encode(['timestamp' => $timestamp, 'error' => $e->getMessage()]) . "\n", FILE_APPEND);
    exit;
}


//initial config
function config_entry_exists($pdo, $name) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM config WHERE config_name = ?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn() > 0;
}


if (!config_entry_exists($pdo, 'system_last_check')) {
    $stmt = $pdo->prepare("INSERT INTO config (config_name, config_value) VALUES (?, ?)");
    $stmt->execute(['system_last_check', $timestamp]);
}

if (!config_entry_exists($pdo, 'system_key')) {
    $stmt = $pdo->prepare("INSERT INTO config (config_name, config_value) VALUES (?, ?)");
    $stmt->execute(['system_key', '"ABC123"']);
}

if (!config_entry_exists($pdo, 'system_status')) {
    $stmt = $pdo->prepare("INSERT INTO config (config_name, config_value) VALUES (?, ?)");
    $stmt->execute(['system_status', 'valid']);
}




// Build JSON payload
$data = [
    "license_key"        => trim(get_db_value($pdo, "system_key"), '"'),
    "uid"                => run("cat /etc/machine-id"),
    "disk_serial"        => get_disk_serial(),
    "motherboard_serial" => get_motherboard_serial(),
    "install_date"       => run("stat -c %w /opt/obzora") ?: run("stat -c %z /opt/obzora"),
    "os_name"            => run("grep '^NAME=' /etc/os-release | cut -d= -f2 | tr -d '\"'"),
    "os_version"         => run("grep '^VERSION_ID=' /etc/os-release | cut -d= -f2 | tr -d '\"'"),
    "cpu"                => run("lscpu | grep 'Model name' | awk -F ':' '{print $2}' | sed 's/^ *//'"),
    "ram_mb"             => (int) run("free -m | awk '/Mem:/ {print $2}'"),
    "mac_addresses"      => array_filter(explode("\n", run("ip link | awk '/ether/ {print $2}'"))),
    "instance_uptime"    => (int) run("cat /proc/uptime | awk '{print int($1)}'"),
    "public_ip"          => run("curl -s http://ipinfo.io/ip"),
    "private_ip"         => run("hostname -I"),
    "hostname"           => run("hostname"),
    "monitored_devices"  => (int) get_db_value($pdo, "SELECT COUNT(*) FROM devices"),
    "obzota_dir_size_mb" => (int) run("du -sm /opt/obzora | awk '{print $1}'"),
    "notes"              => ""
];

// Send request
$ch = curl_init("https://licensing.obzora.net/license_check.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log to file
$logEntry = [
    'timestamp' => $timestamp,
    'request'   => $data,
    'response'  => json_decode($response, true),
    'http_code'=> $httpCode
];
file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND);

// Process response
$responseData = json_decode($response, true);

if ($httpCode === 200 && $responseData['status'] === 'valid' || $responseData['status'] === 'active') {
    update_config($pdo, 'system_last_check', $timestamp);
    update_config($pdo, 'system_status', 'valid');
} else {
    $lastCheck = (int) get_db_value($pdo, 'system_last_check');
    if (($timestamp - $lastCheck) > 21600) { // 6 hours
        update_config($pdo, 'system_status', 'warning');
    }
}

