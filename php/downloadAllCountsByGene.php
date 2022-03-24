<?php

include '../../config.php';
include 'pdoResultFilter.php';

$gene = $_GET['Gene'];
$dataset = $_GET['Dataset'];
$checkboxes = $_GET['Checkboxes'];

$result_arr = array();

if (isset($gene) && !empty($gene) && !is_null($gene) && isset($dataset) && !empty($dataset) && !is_null($dataset)) {
    if (is_string($gene)) {
        $gene_arr = preg_split("/[;, \n]+/", trim($gene));
    } elseif (is_array($gene)) {
        $gene_arr = $gene;
    } else {
        exit(0);
    }
    if (is_string($checkboxes)) {
        $checkboxes = preg_split("/[;, \n]+/", trim($checkboxes));
    } elseif (is_array($checkboxes)) {
        $checkboxes = $checkboxes;
    } else {
        exit(0);
    }
    for ($i = 0; $i < count($gene_arr); $i++) {
        $gene_arr[$i] = trim($gene_arr[$i]);
    }
    for ($i = 0; $i < count($checkboxes); $i++) {
        $checkboxes[$i] = trim($checkboxes[$i]);
    }

    $query_str = "SELECT ";

    if(in_array("Soja", $checkboxes)) {
        $query_str = $query_str . "COUNT(IF(Improvement_Status = 'G. soja', 1, null)) AS Soja, ";
    }
    if(in_array("Elite", $checkboxes)) {
        $query_str = $query_str . "COUNT(IF(Improvement_Status IN ('Cultivar', 'Elite'), 1, null)) AS Elite, ";
    }
    if(in_array("Landrace", $checkboxes)) {
        $query_str = $query_str . "COUNT(IF(Improvement_Status = 'Landrace', 1, null)) AS Landrace, ";
    }
    $query_str = $query_str . "COUNT(IF(Improvement_Status IN ('G. soja', 'Cultivar', 'Elite', 'Landrace', 'Genetic'), 1, null)) AS Total, ";
    if(in_array("Cultivar", $checkboxes)) {
        $query_str = $query_str . "COUNT(IF(Classification = 'NA Cultivar', 1, null)) AS Cultivar, ";
    }
    if(in_array("Imputed", $checkboxes)) {
        $query_str = $query_str . "COUNT(IF(Imputation = '+', 1, null)) AS Imputed, ";
    }
    if(in_array("Unimputed", $checkboxes)) {
        $query_str = $query_str . "COUNT(IF(Imputation = '-', 1, null)) AS Unimputed, ";
    }

    $query_str = $query_str . "
        Gene, Position, Genotype, Genotype_with_Description 
        FROM " . $dataset . " 
        WHERE (Gene IN (" . str_repeat('?, ',  count($gene_arr) - 1) . '?' . ")) 
        GROUP BY Gene, Position, Genotype, Genotype_with_Description 
        ORDER BY Gene, Position, Total DESC;
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