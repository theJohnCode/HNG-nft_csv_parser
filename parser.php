<?php

// Import two required files
require_once 'SHA256.php';
require_once 'jsonToCSV.php';

$feed = '';

// Check how many arguments were passed in the terminal
if ($argc == 1  || $argv[1] == '--help') {
    // read all the file in the directory and extract the ones that ends in .csv
    $files = glob("*.csv");

    foreach ($files as $file) {
        // loop through the files and remove the ones ones with .ouput.csv because we wont be needing it again
        if (str_contains($file, 'output')) {
            $files = array_diff($files, array($file));
            // display it to the user on the terminal
            print_r($files);
        }
    }

    // get the user input from terminal
    $input = (string)readline("Please key in the number of the file to parse: \n");
    // extract the file from the files array using the user entered index
    $feed = $files[$input];
}

// if a a correct index is found
if (isset($feed)) {
    $file_array = explode(".", $feed);

    $file_name = $file_array[0];

    $extension = end($file_array);
    // for optimium security,check if the file has .csv as extension
    if ($extension == 'csv') {
        $column_name = array();

        $final_data = [];

        // read the content of the file
        $file_data = file_get_contents($feed);
        // parse the csv
        $data_array = array_map("str_getcsv", explode("\n", $file_data));
        // get the column name
        $labels = array_shift($data_array);
        // rename the filename column to filename because it is needed later but the naming in the csv files are not unique
        $labels[1] = 'Filename';

        foreach ($labels as $label) {
            $column_name[] = $label;
        }

        $count = count($data_array) - 1;

        for ($j = 0; $j < $count; $j++) {
            // combine the column name and the data
            $data = array_combine($column_name, $data_array[$j]);

            // hash the json file
            $hash = SHA256::digest($data);
            // add it to the data array
            $data['sha256`'] = $hash;
            $final_data[$j] = $data;
        }

        $file = fopen("$file_name.json", "w");
        // save it  the to json file
        fwrite($file, json_encode($final_data));
        fclose($file);

        // get the json file
        $json_filename = "$file_name.json";
        // create a new csv file with the same name
        $newfilename = $file_name . ".output.csv";
        // save the json file to new csv file
        jsonToCSV($json_filename, $newfilename);
        exit;
    }
}
