<?php

include '../../config.php';
include 'pdoResultFilter.php';

$gene = $_GET['Gene'];
$dataset = $_GET['Dataset'];

$result_arr = array();

if (isset($gene) && !empty($gene) && !is_null($gene) && isset($dataset) && !empty($dataset) && !is_null($dataset)) {
    if (is_string($gene)) {
        $gene_arr = preg_split("/[;, \n]+/", trim($gene));
    } elseif (is_array($gene)) {
        $gene_arr = $gene;
    } else {
        exit(0);
    }
    for ($i = 0; $i < count($gene_arr); $i++) {
        $gene_arr[$i] = trim($gene_arr[$i]);
    }

    $query_str = "
        SELECT COUNT(IF(Improvement_Status = 'G. soja', 1, null)) AS Soja, 
        COUNT(IF(Improvement_Status IN ('Cultivar', 'Elite'), 1, null)) AS Cultivar, 
        COUNT(IF(Improvement_Status = 'Landrace', 1, null)) AS Landrace, 
        COUNT(IF(Improvement_Status IN ('G. soja', 'Cultivar', 'Elite', 'Landrace', 'Genetic'), 1, null)) AS Total, 
        COUNT(IF(Classification = 'NA Cultivar', 1, null)) AS NA_Cultivar, 
        Gene, Position, Genotype, Genotype_with_Description 
        FROM " . $dataset . " 
        WHERE (Gene IN (" . str_repeat('?, ',  count($gene_arr) - 1) . '?' . ")) 
        GROUP BY Gene, Position, Genotype, Genotype_with_Description 
        ORDER BY Gene, Position, Total DESC, Cultivar DESC, Landrace DESC, Soja DESC;
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