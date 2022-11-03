<?php
function jsonToCSV($jsonFile, $csvFile)
{
    if (($json = file_get_contents($jsonFile)) == false)
        die('Error reading json file...');
    $data = json_decode($json, true);
    $fp = fopen($csvFile, 'w+');
    $header = false;
    foreach ($data as $row) {
        if (empty($header)) {
            $header = array_keys($row);
            fputcsv($fp, $header);
            $header = array_flip($header);
        }
        fputcsv($fp, array_merge($header, $row));
    }
    fclose($fp);
    return;
}

?>