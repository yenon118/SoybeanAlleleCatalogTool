<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Allele Catalog Tool";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
include './php/dataProcessor.php';

echo '<link rel="stylesheet" href="css/modal.css">';
?>

<!-- Get and process the variables -->
<?php
$gene = $_GET['gene1'];
$dataset = $_GET['dataset1'];

$soja = $_GET['Soja'];
$elite = $_GET['Elite'];
$landrace = $_GET['Landrace'];
$cultivar = $_GET['Cultivar'];
$imputed = $_GET['Imputed'];
$unimputed = $_GET['Unimputed'];


$gene_arr = preg_split("/[;, \n]+/", $gene);
for ($i = 0; $i < count($gene_arr); $i++) {
    $gene_arr[$i] = trim($gene_arr[$i]);
}
$checkboxes = array();
if(isset($soja)) {
    array_push($checkboxes, $soja);
}
if(isset($elite)) {
    array_push($checkboxes, $elite);
}
if(isset($landrace)) {
    array_push($checkboxes, $landrace);
}
if(isset($cultivar)) {
    array_push($checkboxes, $cultivar);
}
if(isset($imputed)) {
    array_push($checkboxes, $imputed);
}
if(isset($unimputed)) {
    array_push($checkboxes, $unimputed);
}
?>

<!-- Back button -->
<a href="/SoybeanAlleleCatalogTool/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query data from database -->
<?php
$query_str = "SELECT ";

if (in_array("Soja", $checkboxes)) {
    $query_str = $query_str . "COUNT(IF(Improvement_Status = 'G. soja', 1, null)) AS Soja, ";
}
if (in_array("Landrace", $checkboxes)) {
    $query_str = $query_str . "COUNT(IF(Improvement_Status = 'Landrace', 1, null)) AS Landrace, ";
}
if (in_array("Elite", $checkboxes)) {
    $query_str = $query_str . "COUNT(IF(Improvement_Status IN ('Cultivar', 'Elite'), 1, null)) AS Elite, ";
}
$query_str = $query_str . "COUNT(IF(Improvement_Status IN ('G. soja', 'Cultivar', 'Elite', 'Landrace', 'Genetic'), 1, null)) AS Total, ";
if (in_array("Cultivar", $checkboxes)) {
    $query_str = $query_str . "COUNT(IF(Classification = 'NA Cultivar', 1, null)) AS Cultivar, ";
}
if (in_array("Imputed", $checkboxes)) {
    $query_str = $query_str . "COUNT(IF(Imputation = '+', 1, null)) AS Imputed, ";
}
if (in_array("Unimputed", $checkboxes)) {
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

$result_arr = pdoResultFilter($result);
$result_arr = processAccessionCounts($result_arr);
?>

<!-- Modal -->
<div id="info-modal" class="info-modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <div id="modal-content-div" style='width:100%; height:auto; border:3px solid #000; overflow:scroll;max-height:1000px;'></div>
        <div id="modal-content-comment"></div>
    </div>
</div>

<!-- Render the table -->
<?php
$ref_color_code = "#D1D1D1";
$missense_variant_color_code = "#7FC8F5";
$frameshift_variant_color_code = "#F26A55";
$exon_loss_variant_color_code = "#F26A55";
$lost_color_code = "#F26A55";
$gain_color_code = "#F26A55";
$disruptive_color_code = "#F26A55";
$conservative_color_code = "#FF7F50";
$splice_color_code = "#9EE85C";

if (!isset($result_arr) || is_null($result_arr) || empty($result_arr)) {
    echo "<p>No record found!!!</p>";
}

for ($i = 0; $i < count($result_arr); $i++) {
    $segment_arr = $result_arr[array_keys($result_arr)[$i]];

    echo "<div style='width:100%; height:auto; border:3px solid #000; overflow:scroll;max-height:1000px;'>";
    echo "<table style='text-align:center;'>";

    // Table header
    echo "<tr>";
    echo "<th></th>";
    foreach ($segment_arr[0] as $key => $value) {
        if ($key != "Position" && $key != "Genotype" && $key != "Genotype_with_Description") {
            echo "<th style=\"border:1px solid black;min-width:80px;\">" . $key . "</th>";
        }
    }
    foreach ($segment_arr[0] as $key => $value) {
        if ($key == "Position") {
            $positionArray = preg_split("/[;, |\n]+/", $value);
            for ($j = 0; $j < count($positionArray); $j++) {
                echo "<th style=\"border:1px solid black;min-width:120px;\">" . $positionArray[$j] . "</th>";
            }
        }
    }
    echo "<th></th>";
    echo "</tr>";

    // Table body
    for ($j = 0; $j < count($segment_arr); $j++) {
        $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
        echo "<td><input type=\"checkbox\" id=\"no__" . $segment_arr[$j]["Gene"] . "__" . $j . "__front\" onclick=\"checkbox_highlights(this)\"></td>";

        foreach ($segment_arr[$j] as $key => $value) {
            if (strval($key) != "Position" && strval($key) != "Genotype" && strval($key) != "Genotype_with_Description") {
                if (!is_numeric($key) && intval($key) == 0 && is_numeric($value) && intval($value) > 0 && strval($key) != "Gene") {
                    echo "<td style=\"border:1px solid black;min-width:80px;\"><a href=\"javascript:void(0);\" onclick=\"getAccessionsByImpStatusGenePositionGenotypeDesc('" . strval($dataset) . "', '" . strval($key) . "', '" . $segment_arr[$j]["Gene"] . "', '" . $segment_arr[$j]["Position"] . "', '" . $segment_arr[$j]["Genotype_with_Description"] . "');\">" . $value . "</a></td>";
                } else if (!is_numeric($key) && intval($key) == 0 && is_numeric($value) && intval($value) == 0 && strval($key) != "Gene") {
                    echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
                } else if (!is_numeric($key) && intval($key) == 0 && !is_numeric($value) && intval($value) == 0 && strval($key) == "Gene") {
                    echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
                }
            }
        }
        foreach ($segment_arr[$j] as $key => $value) {
            if ($key == "Genotype_with_Description") {
                $genotypeWithDescriptionArray = preg_split("/[ ]+/", $value);
                for ($k = 0; $k < count($genotypeWithDescriptionArray); $k++) {
                    if (preg_match("/missense.variant/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $missense_variant_color_code . "\">" . $temp_value . "</td>";
                    } else if (preg_match("/frameshift/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $frameshift_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/exon.loss/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $exon_loss_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/lost/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $lost_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/gain/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $gain_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/disruptive/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $disruptive_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/conservative/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $conservative_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/splice/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $splice_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/ref/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $ref_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else {
                        echo "<td id=\"pos__" . $segment_arr[$j]["Gene"] . "__" . $key . "__" . $j . "\" style=\"border:1px solid black;min-width:120px;background-color:#FFFFFF\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    }
                }
            }
        }

        echo "<td><input type=\"checkbox\" id=\"no__" . $segment_arr[$j]["Gene"] . "__" . $j . "__back\" onclick=\"checkbox_highlights(this)\"></td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";

    echo "<div style='margin-top:10px;' align='right'>";
    echo "<button onclick=\"downloadAllCountsByGene('" . $dataset . "', '" . $segment_arr[0]["Gene"] . "', '" . implode(";", $checkboxes) . "')\" style=\"margin-right:20px;\"> Download (Accession Counts)</button>";
    echo "<button onclick=\"downloadAllByGene('" . $dataset . "', '" . $segment_arr[0]["Gene"] . "', '" . implode(";", $checkboxes) . "')\"> Download (All Accessions)</button>";
    echo "</div>";

    echo "<br />";
    echo "<br />";
}

if (count($result_arr) > 0) {
    echo "<br/><br/>";
    echo "<div style='margin-top:10px;' align='center'>";
    echo "<button type=\"submit\" onclick=\"window.open('https://data.cyverse.org/dav-anon/iplant/home/soykb/Soy1066/Accession_Info.csv')\" style=\"margin-right:20px;\">Download Accession Information</button>";
    echo "<button onclick=\"downloadAllCountsByMultipleGenes('" . $dataset . "', '" . implode(";", $gene_arr) . "', '" . implode(";", $checkboxes) . "')\" style=\"margin-right:20px;\"> Download All (Accession Counts)</button>";
    echo "<button onclick=\"downloadAllByMultipleGenes('" . $dataset . "', '" . implode(";", $gene_arr) . "', '" . implode(";", $checkboxes) . "')\" style=\"margin-right:20px;\"> Download All (All Accessions)</button>";
    echo "</div>";
    echo "<br/><br/>";
}
?>

<script type="text/javascript" language="javascript" src="./js/dataProcessor.js"></script>
<script type="text/javascript" language="javascript" src="./js/getAccessionsByImpStatusGenePositionGenotypeDesc.js"></script>
<script type="text/javascript" language="javascript" src="./js/download.js"></script>
<script type="text/javascript" language="javascript" src="./js/modal.js"></script>
<script type="text/javascript" language="javascript" src="./js/checkboxHighlight.js"></script>

<?php include '../footer.php'; ?>