<?php
$TITLE = "Soybean Allele Catalog Tool";
include '../header.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$dataset = $_GET['dataset2'];
$accession = $_GET['accession'];
$gene = $_GET['gene2'];

$accession_arr = preg_split("/[;,\n]+/", $accession);
for ($i = 0; $i < count($accession_arr); $i++) {
    $accession_arr[$i] = trim($accession_arr[$i]);
}
?>

<!-- Back button -->
<a href="search.php"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query data from database -->
<?php
$query_str = "
SELECT Classification, Improvement_Status, Maturity_Group, Country, State, Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation
FROM soykb." . $dataset . "
WHERE (Gene IN ( ? )) AND (Accession IN (" . str_repeat('?, ',  count($accession_arr) - 1) . '?' . "));
";

$stmt = $PDO->prepare($query_str);
$stmt->bindValue(1, trim($gene), PDO::PARAM_STR);
for ($i = 0; $i < count($accession_arr); $i++) {
    $stmt->bindValue(($i + 2), trim($accession_arr[$i]), PDO::PARAM_STR);
}
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    $result_arr = pdoResultFilter($result);
}
?>

<!-- Render the table -->
<?php
$ref_color_code = "#D1D1D1";
$missense_variant_color_code = "#7FC8F5";
$frameshift_variant_color_code = "#F26A55";
$exon_loss_variant_color_code = "#F26A55";
$lost_color_code = "#F26A55";
$gain_color_code = "#F26A55";
$disruptive_color_code = "#F26A55";
$splice_color_code = "#9EE85C";

if (count($result_arr) > 0) {
    echo "<div style='width:100%;height:100%; border:3px solid #000; overflow:scroll;max-height:1000px;'>";
    echo "<table style='text-align:center;'>";

    // Table header
    echo "<tr>";
    foreach ($result_arr[0] as $key => $value) {
        if (strval($key) != "Position" && strval($key) != "Genotype" && strval($key) != "Genotype_with_Description" && strval($key) != "Imputation") {
            echo "<th>" . strval($key) . "</th>";
        }
    }
    foreach ($result_arr[0] as $key => $value) {
        if (strval($key) == "Position") {
            $positionArray = preg_split("/[;, |\n]+/", $value);
            for ($i = 0; $i < count($positionArray); $i++) {
                echo "<th>" . $positionArray[$i] . "</th>";
            }
        }
    }
    foreach ($result_arr[0] as $key => $value) {
        if (strval($key) == "Imputation") {
            echo "<th>" . strval($key) . "</th>";
        }
    }
    echo "<tr>";

    // Table body
    for ($i = 0; $i < count($result_arr); $i++) {
        $tr_bgcolor = ($i % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";

        foreach ($result_arr[$i] as $key => $value) {
            if (strval($key) != "Position" && strval($key) != "Genotype" && strval($key) != "Genotype_with_Description" && strval($key) != "Imputation") {
                echo "<td style=\"min-width:80px\">" . $value . "</td>";
            }
        }
        foreach ($result_arr[$i] as $key => $value) {
            if (strval($key) == "Genotype_with_Description") {
                $genotypeWithDescriptionArray = preg_split("/[ ]+/", $value);
                for ($k = 0; $k < count($genotypeWithDescriptionArray); $k++) {
                    if (preg_match("/missense.variant/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"min-width:80px;background-color:" . $missense_variant_color_code . "\">" . $temp_value . "</td>";
                    } else if (preg_match("/frameshift/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"min-width:80px;background-color:" . $frameshift_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/exon.loss/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"min-width:80px;background-color:" . $exon_loss_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/lost/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"min-width:80px;background-color:" . $lost_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/gain/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"min-width:80px;background-color:" . $gain_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/disruptive/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"min-width:80px;background-color:" . $disruptive_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/splice/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"min-width:80px;background-color:" . $splice_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/ref/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"min-width:80px;background-color:" . $ref_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"min-width:80px;background-color:#FFFFFF\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    }
                }
            }
        }
        foreach ($result_arr[$i] as $key => $value) {
            if (strval($key) == "Imputation") {
                echo "<td style=\"min-width:80px;text-align:center\">" . $value . "</td>";
            }
        }

        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";

    echo "<div style='margin-top:10px;' align='right'>";
    echo "<button onclick=\"downloadAllByAccessionsAndGene('" . $dataset . "', '" . strval(implode(";", $accession_arr)) . "', '" . $gene . "')\"> Download</button>";
    echo "</div>";

    echo "<br />";
    echo "<br />";


    echo "<br/><br/>";
    echo "<div style='margin-top:10px;' align='center'>";
    echo "<button type=\"submit\" onclick=\"window.open('https://de.cyverse.org/dl/d/B0365415-CEF8-4F6C-A242-39C01198EC6F/Accession_Info.csv')\" style=\"margin-right:20px;\">Download Accession Information</button>";
    echo "</div>";
    echo "<br/><br/>";
}

?>

<script type="text/javascript" language="javascript" src="./js/download.js"></script>

<?php include '../footer.php'; ?>