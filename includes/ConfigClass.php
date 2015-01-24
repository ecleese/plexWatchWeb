<?php

ini_set("auto_detect_line_endings", true);

class ConfigClass {

    public static function read($filename)
    {
        $config_array = array();
        $handle = fopen($filename, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (substr($line, 0, 1) === '$') {
                    $config_line = explode(" = ", $line);
                    preg_match("/\[\'([^\]]*)\'\]/", $config_line[0], $matches);
                    preg_match("/\'(.*)\'/", $config_line[1], $matches2);
                    $config_array[$matches[1]] = $matches2[1];
                }
            }
            fclose($handle);
        } else {
            error_log("ConfigClass :: Error reading config file.");
            return false;
        }

        return $config_array;
    }
}