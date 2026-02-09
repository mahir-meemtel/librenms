#!/usr/bin/env php
<?php
array_shift($argv); // remove script name

if (empty($argv)) {
    echo "You must specify one or more files or folders containing files to rename.\n";
}

$renamed_count = 0;

foreach ($argv as $item) {
    if (is_dir($item)) {
        foreach (scandir($item) as $file) {
            if ($file != '.' && $file != '..') {
                $renamed_count += (int) rename_mib_file($item . $file);
            }
        }
    } else {
        $renamed_count += (int) rename_mib_file($item);
    }
}

echo "Renamed $renamed_count files.\n";

function rename_mib_file($file)
{
    if (! is_file($file)) {
        echo "Not a file: $file\n";

        return false;
    }

    $mib_name = extract_mib_name($file);
    $filename = basename($file);
    if ($mib_name != $filename) {
        $new_file = dirname($file) . '/' . $mib_name;
        echo "$file -> $new_file\n";

        return rename($file, $new_file);
    }

    return false; // name already correct
}

function extract_mib_name($file)
{
    // extract the mib name (tried regex, but was too complex and I had to read the whole file)
    if ($handle = fopen($file, 'r')) {
        $header = '';
        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);

            if (empty($trimmed) || substr($trimmed, 0, 2) == '--') {
                continue;
            }

            $header .= " $trimmed";
            if (strpos($trimmed, 'DEFINITIONS') !== false) {
                preg_match('/(\S+)\s+(?=DEFINITIONS)/', $header, $matches);
                fclose($handle);

                return $matches[1];
            }
        }
        fclose($handle);
    }

    throw new Exception("Could not extract mib name from file ($file)");
}
