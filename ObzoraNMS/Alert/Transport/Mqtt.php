<?php

namespace ObzoraNMS\Alert\Transport;

use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Exceptions\AlertTransportDeliveryException;
use ObzoraNMS\Config;

class Mqtt extends Transport
{
    protected string $name = 'MQTT';

    public function deliverAlert(array $alert_data): bool
    {
        // Debugging: Log the alert data to see what it contains
//        file_put_contents('/tmp/mqtt_debug.log', print_r($alert_data, true), FILE_APPEND);

        // Fetch configuration values
        $broker = $this->config['mqtt-broker'];
        $port = $this->config['mqtt-port'];
        $topic = $this->config['mqtt-topic'];
        $client_id = $this->config['mqtt-client-id'];
        $username = $this->config['mqtt-username'] ?? '';
        $password = $this->config['mqtt-password'] ?? '';

        // Generate the alert message (send as JSON)
        $alert_message = $this->generateAlertMessage($alert_data);

        // Escape double quotes in the message
        $escaped_alert_message = $this->escapeDoubleQuotes($alert_message);

        // Log the message to check if it's being generated correctly
//        file_put_contents('/tmp/mqtt_message.log', $escaped_alert_message, FILE_APPEND);

        // Build the mosquitto_pub command
        $cmd = "mosquitto_pub -h {$broker} -p {$port} -t {$topic} -m \"{$escaped_alert_message}\" -i {$client_id}";

        // Add authentication if username and password are provided
        if ($username && $password) {
            $cmd .= " -u {$username} -P {$password}";
        }

        // Log the full command for debugging
 //       file_put_contents('/tmp/mqtt_command.log', "Executing command: $cmd\n", FILE_APPEND);

        // Execute the command to send the alert
        exec($cmd, $output, $return_var);

        // Log the output and error code for debugging
  //      file_put_contents('/tmp/mqtt_exec_output.log', implode("\n", $output), FILE_APPEND);
  //      file_put_contents('/tmp/mqtt_exec_return.log', "Return code: $return_var\n", FILE_APPEND);

        // Check if the command was successful
        if ($return_var !== 0) {
            // Capture and log the error output for debugging
            file_put_contents('/tmp/mqtt_error.log', "Command failed with return code $return_var: " . implode("\n", $output), FILE_APPEND);
            throw new AlertTransportDeliveryException($alert_data, $return_var, implode("\n", $output));
        }

        return true;
    }

    // Generate the alert message in JSON format
    protected function generateAlertMessage(array $alert_data): string
    {
        // Extracting required fields from alert data, with fallback values if any key is missing
        $alert_name = $alert_data['name'] ?? 'N/A';  // Fallback to 'N/A' if missing
        $message = $alert_data['msg'] ?? 'No message provided';
        $device_name = $alert_data['hostname'] ?? 'Unknown device'; // Use 'hostname' for device name
        $severity = $alert_data['severity'] ?? 'Low'; // Default to 'Low' if severity is missing
        $timestamp = isset($alert_data['timestamp']) ? strtotime($alert_data['timestamp']) : time(); // Use provided timestamp or current time

        // Construct the message (in JSON format)
        $alert_message = [
            'alert_name' => $alert_name,
            'message' => $message,
            'device_name' => $device_name,
            'severity' => $severity,
            'timestamp' => $timestamp,
            // You can add other fields from $alert_data as needed
        ];

        // Convert the message array to JSON
        return json_encode($alert_message);
    }

    // Escape double quotes in the JSON message for proper shell usage
    protected function escapeDoubleQuotes(string $message): string
    {
        // Escape any double quotes inside the message for proper shell handling
        return str_replace('"', '\"', $message);
    }

    // Return the config template for the MQTT transport
    public static function configTemplate(): array
    {
        return [
            'config' => [
                [
                    'title' => 'MQTT Broker',
                    'name' => 'mqtt-broker',
                    'descr' => 'MQTT Broker hostname or IP',
                    'type' => 'text',
                ],
                [
                    'title' => 'MQTT Port',
                    'name' => 'mqtt-port',
                    'descr' => 'MQTT Broker port (default: 1883)',
                    'type' => 'text',
                    'default' => '1883',
                ],
                [
                    'title' => 'MQTT Topic',
                    'name' => 'mqtt-topic',
                    'descr' => 'MQTT Topic to publish to',
                    'type' => 'text',
                    'default' => 'obzora/alerts',
                ],
                [
                    'title' => 'MQTT Client ID',
                    'name' => 'mqtt-client-id',
                    'descr' => 'MQTT Client ID',
                    'type' => 'text',
                    'default' => 'obzora_alerts',
                ],
                [
                    'title' => 'MQTT Username',
                    'name' => 'mqtt-username',
                    'descr' => 'MQTT Username (optional)',
                    'type' => 'text',
                ],
                [
                    'title' => 'MQTT Password',
                    'name' => 'mqtt-password',
                    'descr' => 'MQTT Password (optional)',
                    'type' => 'password',
                ],
            ],
            'validation' => [
                'mqtt-broker' => 'required|string', // Validation as string instead of URL
                'mqtt-port' => 'required|integer',
                'mqtt-topic' => 'required|string',
            ],
        ];
    }
}
