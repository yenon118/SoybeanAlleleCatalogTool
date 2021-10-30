<?php

include '../../config.php';
include 'pdoResultFilter.php';

$gene = $_GET['Gene'];
$accessions = $_GET['Accessions'];
$dataset = $_GET['Dataset'];

$result_arr = array();

if (isset($gene) && !empty($gene) && !is_null($gene) && isset($accessions) && !empty($accessions) && !is_null($accessions) && isset($dataset) && !empty($dataset) && !is_null($dataset)) {
    if (is_string($accessions)) {
        $accession_arr = preg_split("/[;,\n]+/", trim($accessions));
    } elseif (is_array($accessions)) {
        $accession_arr = $accessions;
    } else {
        exit(0);
    }
    for ($i = 0; $i < count($accession_arr); $i++) {
        $accession_arr[$i] = trim($accession_arr[$i]);
    }

    $query_str = "
        SELECT Classification, Improvement_Status, Maturity_Group, Country, State, Accession, Gene, Position, Genotype, Genotype_with_Description
        FROM soykb." . $dataset . "
        WHERE ((Accession IN (" . str_repeat('?, ',  count($accession_arr) - 1) . '?' . ")) AND (Gene = '".$gene."'));
    ";

    $stmt = $PDO->prepare($query_str);
    for ($i = 0; $i < count($accession_arr); $i++) {
        $stmt->bindValue(($i + 1), trim($accession_arr[$i]), PDO::PARAM_STR);
    }
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (count($result) > 0) {
        $result_arr = pdoResultFilter($result);
    }
}

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>