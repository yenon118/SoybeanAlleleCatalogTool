<?php

include '../../config.php';
include 'pdoResultFilter.php';

$genes = $_GET['Genes'];
$dataset = $_GET['Dataset'];

$query_str = "
SELECT COUNT(IF(Improvement_Status = 'G. soja', 1, null)) AS Soja, 
COUNT(IF(Improvement_Status IN ('Cultivar', 'Elite'), 1, null)) AS Cultivar, 
COUNT(IF(Improvement_Status = 'Landrace', 1, null)) AS Landrace, 
COUNT(IF(Improvement_Status IN ('G. soja', 'Cultivar', 'Elite', 'Landrace'), 1, null)) AS Total, 
COUNT(IF(Classification = 'NA Cultivar', 1, null)) AS NA_Cultivar, 
Gene, Position, Genotype, Genotype_with_Description 
FROM " . $dataset . " 
WHERE (Gene IN ('Glyma.01G049100', 'Glyma.01G049200', 'Glyma.01G049300')) 
GROUP BY Gene, Position, Genotype, Genotype_with_Description 
ORDER BY Gene, Position, Total DESC, Cultivar DESC, Landrace DESC, Soja DESC
";

$stmt = $PDO->prepare($query_str);
for ($i = 0; $i < count($genes); $i++) {
    $stmt->bindValue(($i + 1), trim($genes[$i]), PDO::PARAM_STR);
}
for ($i = 0; $i < count($genes); $i++) {
    $stmt->bindValue(($i + count($genes) + 1), trim($genes[$i]), PDO::PARAM_STR);
}
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>