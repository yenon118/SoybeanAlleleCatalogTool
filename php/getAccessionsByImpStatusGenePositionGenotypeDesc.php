<?php

include '../../config.php';
include 'pdoResultFilter.php';

$dataset = $_GET['Dataset'];
$key = $_GET['Key'];
$gene = $_GET['Gene'];
$position = $_GET['Position'];
$genotypeWithDescription = $_GET['GenotypeWithDescription'];

if(preg_match("/na.cultivar/i", strval($key))){
    $query_str = "
        SELECT Classification, Improvement_Status, Maturity_Group, Country, State, Accession, Gene, Position, Genotype, Genotype_with_Description 
        FROM soykb." . $dataset . "
        WHERE (Gene IN (?))
        AND Position = ?
        AND Genotype_with_Description = ?
        AND Classification = 'NA Cultivar'
        ORDER BY Accession;
    ";
} else if(preg_match("/soja/i", strval($key))){
    $query_str = "
        SELECT Classification, Improvement_Status, Maturity_Group, Country, State, Accession, Gene, Position, Genotype, Genotype_with_Description 
        FROM soykb." . $dataset . "
        WHERE (Gene IN (?))
        AND Position = ?
        AND Genotype_with_Description = ?
        AND Improvement_Status LIKE '%soja%' ORDER BY Accession;
    ";
} else if(preg_match("/cultivar/i", strval($key))){
    $query_str = "
        SELECT Classification, Improvement_Status, Maturity_Group, Country, State, Accession, Gene, Position, Genotype, Genotype_with_Description 
        FROM soykb." . $dataset . "
        WHERE (Gene IN (?))
        AND Position = ?
        AND Genotype_with_Description = ?
        AND (Improvement_Status LIKE '%cultivar%' OR Improvement_Status LIKE '%elite%')
        ORDER BY Accession;
    ";
} else if(preg_match("/landrace/i", strval($key))){
    $query_str = "
        SELECT Classification, Improvement_Status, Maturity_Group, Country, State, Accession, Gene, Position, Genotype, Genotype_with_Description 
        FROM soykb." . $dataset . "
        WHERE (Gene IN (?))
        AND Position = ?
        AND Genotype_with_Description = ?
        AND Improvement_Status LIKE '%landrace%'
        ORDER BY Accession;
    ";
} else if(preg_match("/total/i", strval($key))){
    $query_str = "
        SELECT Classification, Improvement_Status, Maturity_Group, Country, State, Accession, Gene, Position, Genotype, Genotype_with_Description 
        FROM soykb." . $dataset . "
        WHERE (Gene IN (?))
        AND Position = ?
        AND Genotype_with_Description = ?
        ORDER BY Accession;
    ";
}

$stmt = $PDO->prepare($query_str);
$stmt->bindValue(1, trim($gene), PDO::PARAM_STR);
$stmt->bindValue(2, trim($position), PDO::PARAM_STR);
$stmt->bindValue(3, trim($genotypeWithDescription), PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>