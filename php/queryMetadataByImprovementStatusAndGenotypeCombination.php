<?php

include '../../config.php';
include 'pdoResultFilter.php';
include 'getTableNames.php';
include 'getSummarizedDataQueryString.php';
include 'getDataQueryString.php';

$dataset = trim($_GET['Dataset']);
$key = trim($_GET['Key']);
$gene = trim($_GET['Gene']);
$chromosome = trim($_GET['Chromosome']);
$position = trim($_GET['Position']);
$genotype = trim($_GET['Genotype']);
$genotype_description = trim($_GET['Genotype_Description']);

$db = "soykb";

// Table names and datasets
$table_names = getTableNames($dataset);
$key_column = $table_names["key_column"];
$gff_table = $table_names["gff_table"];
$accession_mapping_table = $table_names["accession_mapping_table"];

// Where clause of query string
if ($key == "Total") {
	$query_str = "WHERE (ACD.Position = '" . $position . "') AND (ACD.Genotype = '" . $genotype . "') ";
} elseif ($key == "Cultivar"){
	$query_str = "WHERE (ACD.Classification = 'NA Cultivar') AND (ACD.Position = '" . $position . "') AND (ACD.Genotype = '" . $genotype . "') ";
} elseif ($key == "G. soja" || $key == "Soja"){
	$query_str = "WHERE ";
	$query_str = $query_str . "((ACD." . $key_column . " = 'G. soja') OR (ACD." . $key_column . " = 'Soja')) AND ";
	$query_str = $query_str . "(ACD.Position = '" . $position . "') AND (ACD.Genotype = '" . $genotype . "') ";
} else {
	$query_str = "WHERE ";
	$query_str = $query_str . "(ACD." . $key_column . " = '" . $key . "') AND ";
	$query_str = $query_str . "(ACD.Position = '" . $position . "') AND ";
	$query_str = $query_str . "(ACD.Genotype = '" . $genotype . "') ";
}

// Generate query string
$query_str = getDataQueryString(
	$dataset,
	$db,
	$gff_table,
	$accession_mapping_table,
	$gene,
	$chromosome,
	$query_str
);

// Make query
$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>