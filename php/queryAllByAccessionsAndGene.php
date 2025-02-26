<?php

include '../../config.php';
include 'pdoResultFilter.php';
include 'getTableNames.php';
include 'getSummarizedDataQueryString.php';
include 'getDataQueryString.php';

$dataset = $_GET['Dataset'];
$gene = $_GET['Gene'];
$accession = $_GET['Accession_Array'];


$dataset = clean_malicious_input($dataset);
$dataset = preg_replace('/\s+/', '', $dataset);

$gene = clean_malicious_input($gene);
$gene = preg_replace('/\s+/', '', $gene);

$accession = clean_malicious_input($accession);


if (is_string($accession)) {
	$accession = trim($accession);
	$temp_accession_array = preg_split("/[;, \n]+/", $accession);
	$accession_array = array();
	for ($i = 0; $i < count($temp_accession_array); $i++) {
		if (!empty(trim($temp_accession_array[$i]))) {
			array_push($accession_array, trim($temp_accession_array[$i]));
		}
	}
} elseif (is_array($accession)) {
	$temp_accession_array = $accession;
	$accession_array = array();
	for ($i = 0; $i < count($temp_accession_array); $i++) {
		if (!empty(trim($temp_accession_array[$i]))) {
			array_push($accession_array, trim($temp_accession_array[$i]));
		}
	}
}

$db = "soykb";

// Table names and datasets
$table_names = getTableNames($dataset);
$key_column = $table_names["key_column"];
$gff_table = $table_names["gff_table"];
$accession_mapping_table = $table_names["accession_mapping_table"];

// Generate SQL string
$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
$query_str = $query_str . "WHERE Name IN ('" . $gene . "');";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$gene_result_arr = pdoResultFilter($result);

// Generate the where clause
$query_str = "WHERE (ACD.Accession IN ('";
for ($i = 0; $i < count($accession_array); $i++) {
	if ($i < (count($accession_array) - 1)) {
		$query_str = $query_str . trim($accession_array[$i]) . "', '";
	} elseif ($i == (count($accession_array) - 1)) {
		$query_str = $query_str . trim($accession_array[$i]);
	}
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "OR (ACD.SoyKB_Accession IN ('";
for ($i = 0; $i < count($accession_array); $i++) {
	if ($i < (count($accession_array) - 1)) {
		$query_str = $query_str . trim($accession_array[$i]) . "', '";
	} elseif ($i == (count($accession_array) - 1)) {
		$query_str = $query_str . trim($accession_array[$i]);
	}
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "OR (ACD.GRIN_Accession IN ('";
for ($i = 0; $i < count($accession_array); $i++) {
	if ($i < (count($accession_array) - 1)) {
		$query_str = $query_str . trim($accession_array[$i]) . "', '";
	} elseif ($i == (count($accession_array) - 1)) {
		$query_str = $query_str . trim($accession_array[$i]);
	}
}
$query_str = $query_str . "')) ";

// Generate query string
$query_str = getDataQueryString(
	$dataset,
	$db,
	$gff_table,
	$accession_mapping_table,
	$gene,
	$gene_result_arr[0]["Chromosome"],
	$query_str
);

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);


for ($i = 0; $i < count($result_arr); $i++) {
	if (preg_match("/\+/i", $result_arr[$i]["Imputation"])) {
		$result_arr[$i]["Imputation"] = "+";
	} else {
		$result_arr[$i]["Imputation"] = "";
	}
}

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
