<?php

include '../../config.php';
include 'pdoResultFilter.php';
include 'getTableNames.php';

$dataset = trim($_GET['Dataset']);

$dataset = clean_malicious_input($dataset);
$dataset = preg_replace('/\s+/', '', $dataset);

$db = "soykb";

// Table names and datasets
$table_names = getTableNames($dataset);
$key_column = $table_names["key_column"];
$gff_table = $table_names["gff_table"];
$accession_mapping_table = $table_names["accession_mapping_table"];

// Query gene from database
if ($dataset == "Soy1066") {
	$query_str = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table;
	$query_str = $query_str . " WHERE (Name IS NOT NULL) AND (Name LIKE 'Glyma.01G049%') LIMIT 3;";
} else {
	$query_str = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE (Name IS NOT NULL) LIMIT 3;";
}

// Perform query
$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$gene_result_arr = pdoResultFilter($result);

// Query improvement status from database
$query_str = "SELECT DISTINCT Improvement_Status AS `Key` FROM " . $db . "." . $accession_mapping_table . ";";

// Perform query
$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$improvement_status_result_arr = pdoResultFilter($result);

$result_arr = [
	"Gene" => $gene_result_arr,
	"Key_Column" => $key_column,
	"Improvement_Status" => $improvement_status_result_arr,
];

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
