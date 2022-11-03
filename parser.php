<?php

// Import two required files
require_once 'SHA256.php';
require_once 'jsonToCSV.php';

$feed = '';

if ($argc == 1  || $argv[1] == '--help') {
    $files = glob("*.csv");
    // print_r(");

    foreach ($files as $file) {
        if (str_contains($file, 'output')) {
            $files = array_diff($files, array($file));
            print_r($files);
        }

        // if (fopen($file, "r") !== FALSE) {
        //     print_r("Enter the corresponding file  number below to parse \n");
        // } else {
        //     echo "Could not open file: " . $file;
        // }
    }

    $input = (string)readline("Please key in the number of the file to parse: \n");
    $feed = $files[$input];
    var_dump($feed);

}

if (isset($feed)) {
    $file_array = explode(".", $feed);

    $file_name = $file_array[0];

    $extension = end($file_array);

    if ($extension == 'csv') {
        $column_name = array();

        $final_data = [];

        $file_data = file_get_contents($feed);

        $data_array = array_map("str_getcsv", explode("\n", $file_data));

        $labels = array_shift($data_array);
        $labels[1] = 'Filename';

        foreach ($labels as $label) {
            $column_name[] = $label;
        }

        $count = count($data_array) - 1;

        for ($j = 0; $j < $count; $j++) {
            $data = array_combine($column_name, $data_array[$j]);

            $hash = SHA256::digest($data);
            $data['sha256`'] = $hash;
            $final_data[$j] = $data;
        }

        $file = fopen("$file_name.json", "w");
        fwrite($file, json_encode($final_data));
        fclose($file);


        $json_filename = "$file_name.json";
        $newfilename = $file_name . ".output.csv";

        jsonToCSV($json_filename, $newfilename);
        exit;
    }
}
