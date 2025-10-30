<?php

include '../../config.php';
include 'pdoResultFilter.php';
include 'getTableNames.php';

$dataset = $_GET['Dataset'];

$dataset = clean_malicious_input($dataset);
$dataset = preg_replace('/\s+/', '', $dataset);

$db = "soykb";

// Table names and datasets
$table_names = getTableNames($dataset);
$accession_mapping_table = $table_names["accession_mapping_table"];

// Generate query string
$query_str = "SELECT * FROM " . $db . "." . $accession_mapping_table;

// Perform query
$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
