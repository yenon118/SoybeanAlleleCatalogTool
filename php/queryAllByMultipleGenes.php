<?php

include '../../config.php';
include 'pdoResultFilter.php';
include 'getTableNames.php';
include 'getSummarizedDataQueryString.php';
include 'getDataQueryString.php';

$dataset = trim($_GET['Dataset']);
$gene = $_GET['Gene_Array'];
$improvement_status = $_GET['Improvement_Status_Array'];

if (is_string($gene)) {
	$gene = trim($gene);
	$temp_gene_array = preg_split("/[;, \n]+/", $gene);
	$gene_array = array();
	for ($i = 0; $i < count($temp_gene_array); $i++) {
		if (!empty(trim($temp_gene_array[$i]))) {
			array_push($gene_array, trim($temp_gene_array[$i]));
		}
	}
} elseif (is_array($gene)) {
	$temp_gene_array = $gene;
	$gene_array = array();
	for ($i = 0; $i < count($temp_gene_array); $i++) {
		if (!empty(trim($temp_gene_array[$i]))) {
			array_push($gene_array, trim($temp_gene_array[$i]));
		}
	}
}

if (is_string($improvement_status)) {
	$improvement_status = trim($improvement_status);
	$temp_improvement_status_array = preg_split("/[;, \n]+/", $improvement_status);
	$improvement_status_array = array();
	for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
		if (!empty(trim($temp_improvement_status_array[$i]))) {
			array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
		}
	}
} elseif (is_array($improvement_status)) {
	$temp_improvement_status_array = $improvement_status;
	$improvement_status_array = array();
	for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
		if (!empty(trim($temp_improvement_status_array[$i]))) {
			array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
		}
	}
}

$db = "soykb";

// Table names and datasets
$table_names = getTableNames($dataset);
$key_column = $table_names["key_column"];
$gff_table = $table_names["gff_table"];
$accession_mapping_table = $table_names["accession_mapping_table"];


for ($i = 0; $i < count($gene_array); $i++) {

	// Generate SQL string
	$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
	$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
	$query_str = $query_str . "WHERE Name IN ('" . $gene_array[$i] . "');";

	$stmt = $PDO->prepare($query_str);
	$stmt->execute();
	$result = $stmt->fetchAll();

	$gene_result_arr = pdoResultFilter($result);

	// Generate query string
	$query_str = getDataQueryString(
		$dataset,
		$db,
		$gff_table,
		$accession_mapping_table,
		$gene_array[$i],
		$gene_result_arr[0]["Chromosome"],
		""
	);

	$stmt = $PDO->prepare($query_str);
	$stmt->execute();
	$result = $stmt->fetchAll();

	$result_arr = pdoResultFilter($result);

	if (!isset($all_counts_array)) {
		$all_counts_array = $result_arr;
	} else {
		$all_counts_array = array_merge((array) $all_counts_array, (array) $result_arr);
	}

}

for ($i = 0; $i < count($all_counts_array); $i++) {
	if (preg_match("/\+/i", $all_counts_array[$i]["Imputation"])) {
		$all_counts_array[$i]["Imputation"] = "+";
	} else{
		$all_counts_array[$i]["Imputation"] = "";
	}
}

echo json_encode(array("data" => $all_counts_array), JSON_INVALID_UTF8_IGNORE);

?>