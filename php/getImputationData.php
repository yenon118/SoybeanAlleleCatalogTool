<?php

include '../../config.php';
include 'pdoResultFilter.php';

$dataset = $_GET['Dataset'];
$accessions = $_GET['Accession'];
$gene = $_GET['Gene'];
$position = $_GET['Position'];

$dataset = $dataset . "_Imputation";

if (is_string($accessions)) {
    $accession_arr = preg_split("/[;,\n]+/", trim($accessions));
} elseif (is_array($accessions)) {
    $accession_arr = $accessions;
} else {
    exit(0);
}

if (is_string($position)) {
    $position_arr = preg_split("/[;,\n ]+/", trim($position));
} elseif (is_array($position)) {
    $position_arr = $position;
} else {
    exit(0);
}


$query_str = "SELECT * FROM soykb." . $dataset;
$query_str = $query_str . " WHERE (Gene = '" . $gene . "')";

$query_str = $query_str . " AND (Accession IN ('";
for ($i = 0; $i < count($accession_arr); $i++) {
    if ($i < (count($accession_arr)-1)) {
        $query_str = $query_str . $accession_arr[$i] . "', '";
    } else {
        $query_str = $query_str . $accession_arr[$i];
    }
}
$query_str = $query_str . "'))";

$query_str = $query_str . " AND (Position IN (";
for ($i = 0; $i < count($position_arr); $i++) {
    if ($i < (count($position_arr)-1)) {
        $query_str = $query_str . $position_arr[$i] . ", ";
    } else {
        $query_str = $query_str . $position_arr[$i];
    }
}
$query_str = $query_str . "))";

$query_str = $query_str . " ORDER BY Accession, Position;";

// print_r($query_str);

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>