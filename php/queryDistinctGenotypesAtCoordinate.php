<?php

include '../../config.php';
include 'pdoResultFilter.php';

$chromosome = $_GET['Chromosome'];
$position = $_GET['Position'];
$dataset = $_GET['Dataset'];


$chromosome = clean_malicious_input($chromosome);
$chromosome = preg_replace('/\s+/', '', $chromosome);

$position = clean_malicious_input($position);
$position = preg_replace('/\s+/', '', $position);

$dataset = clean_malicious_input($dataset);
$dataset = preg_replace('/\s+/', '', $dataset);

$position = abs(intval($position));


// Construct query string
$query_str = "SELECT DISTINCT G.Genotype FROM soykb.act_" . $dataset . "_genotype_" . $chromosome . " AS G ";
$query_str = $query_str . "WHERE ((G.Chromosome = '" . $chromosome . "') AND (G.Position = " . $position . ")); ";


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
