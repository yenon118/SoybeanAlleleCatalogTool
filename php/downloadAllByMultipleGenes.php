<?php

include '../../config.php';
include 'pdoResultFilter.php';

$genes = $_GET['Genes'];
$dataset = $_GET['Dataset'];

$result_arr = array();

if (isset($genes) && !empty($genes) && !is_null($genes) && isset($dataset) && !empty($dataset) && !is_null($dataset)) {
    if (is_string($genes)) {
        $gene_arr = preg_split("/[;, \n]+/", trim($genes));
    } elseif (is_array($genes)) {
        $gene_arr = $genes;
    } else {
        exit(0);
    }
    for ($i = 0; $i < count($gene_arr); $i++) {
        $gene_arr[$i] = trim($gene_arr[$i]);
    }

    $query_str = "
        SELECT Classification, Improvement_Status, Maturity_Group, Country, State, Accession, Gene, Position, Genotype, Genotype_with_Description
        FROM soykb." . $dataset . "
        WHERE (Gene IN (" . str_repeat('?, ',  count($gene_arr) - 1) . '?' . "));
    ";

    $stmt = $PDO->prepare($query_str);
    for ($i = 0; $i < count($gene_arr); $i++) {
        $stmt->bindValue(($i + 1), trim($gene_arr[$i]), PDO::PARAM_STR);
    }
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (count($result) > 0) {
        $result_arr = pdoResultFilter($result);
    }
}

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>