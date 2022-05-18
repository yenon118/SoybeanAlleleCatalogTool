<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Allele Catalog Tool";

// include '../header.php';
include '../config.php';
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
<a href="/SoybeanAlleleCatalogTool/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query data from database -->
<?php

// Check GRIN accession mapping
$query_str = "
SELECT Accession, GRIN_Accession FROM soykb.Soybean_Allele_Catalog_Accession_Mapping
WHERE (GRIN_Accession IN (" . str_repeat('?, ',  count($accession_arr) - 1) . '?' . "));
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

if (count($result_arr) > 0) {
    for ($i = 0; $i < count($result_arr); $i++) {
        if (!in_array($result_arr[$i]['Accession'], $accession_arr)) {
            array_push($accession_arr, $result_arr[$i]['Accession']);
        }

    }
}

// Query data
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


// Add imputation information
$imputation_dataset = $dataset . "_Imputation";

if(isset($result_arr) && !empty($result_arr)) {
    if (count($result_arr) > 0) {
        $position = $result_arr[0]['Position'];
        if (is_string($position)) {
            $position_arr = preg_split("/[;,\n ]+/", trim($position));
        } elseif (is_array($position)) {
            $position_arr = $position;
        } else {
            exit(0);
        }
    }
}

$query_str = "SELECT * FROM soykb." . $imputation_dataset;
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

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    $imp_result_arr = pdoResultFilter($result);
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
    echo "<div style='width:100%; height:auto; border:3px solid #000; overflow:scroll;max-height:1000px;'>";
    echo "<table style='text-align:center;'>";

    // Table header
    echo "<tr>";
    foreach ($result_arr[0] as $key => $value) {
        if (strval($key) != "Position" && strval($key) != "Genotype" && strval($key) != "Genotype_with_Description" && strval($key) != "Imputation") {
            echo "<th style=\"border:1px solid black;\">" . strval($key) . "</th>";
        }
    }
    foreach ($result_arr[0] as $key => $value) {
        if (strval($key) == "Position") {
            $positionArray = preg_split("/[;, |\n]+/", $value);
            for ($i = 0; $i < count($positionArray); $i++) {
                echo "<th style=\"border:1px solid black;\">" . $positionArray[$i] . "</th>";
            }
        }
    }
    foreach ($result_arr[0] as $key => $value) {
        if (strval($key) == "Imputation") {
            echo "<th style=\"border:1px solid black;\">" . strval($key) . "</th>";
        }
    }
    echo "<tr>";

    // Table body
    for ($i = 0; $i < count($result_arr); $i++) {
        $tr_bgcolor = ($i % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";

        foreach ($result_arr[$i] as $key => $value) {
            if (strval($key) != "Position" && strval($key) != "Genotype" && strval($key) != "Genotype_with_Description" && strval($key) != "Imputation") {
                echo "<td style=\"border:1px solid black;min-width:120px;\">" . $value . "</td>";
            }
        }
        foreach ($result_arr[$i] as $key => $value) {
            if (strval($key) == "Genotype_with_Description") {
                $genotypeWithDescriptionArray = preg_split("/[ ]+/", $value);
                for ($k = 0; $k < count($genotypeWithDescriptionArray); $k++) {

                    // Add imputation information
                    if (isset($imp_result_arr) && !empty($imp_result_arr)){
                        if (count($imp_result_arr) > 0) {
                            for ($m = 0; $m < count($imp_result_arr); $m++) {
                                if($imp_result_arr[$m]['Accession'] == $result_arr[$i]['Accession'] && $imp_result_arr[$m]['Gene'] == $result_arr[$i]['Gene'] && intval($imp_result_arr[$m]['Position']) === intval($positionArray[$k])) {
                                    $genotypeWithDescriptionArray[$k] = strval($genotypeWithDescriptionArray[$k]) . "|+";
                                }
                            }
                        }
                    }

                    if (preg_match("/missense.variant/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = "";
                        if (count($temp_value_arr) > 2) {
                            $temp_value = $temp_value_arr[0];
                            for ($m = 2; $m < count($temp_value_arr); $m++) {
                                $temp_value = $temp_value . "|" . $temp_value_arr[$m];
                            }
                        } else {
                            $temp_value = $genotypeWithDescriptionArray[$k];
                        }
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $missense_variant_color_code . "\">" . $temp_value . "</td>";
                    } else if (preg_match("/frameshift/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $frameshift_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/exon.loss/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $exon_loss_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/lost/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = "";
                        if (count($temp_value_arr) > 2) {
                            $temp_value = $temp_value_arr[0];
                            for ($m = 2; $m < count($temp_value_arr); $m++) {
                                $temp_value = $temp_value . "|" . $temp_value_arr[$m];
                            }
                        } else {
                            $temp_value = $genotypeWithDescriptionArray[$k];
                        }
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $lost_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/gain/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = "";
                        if (count($temp_value_arr) > 2) {
                            $temp_value = $temp_value_arr[0];
                            for ($m = 2; $m < count($temp_value_arr); $m++) {
                                $temp_value = $temp_value . "|" . $temp_value_arr[$m];
                            }
                        } else {
                            $temp_value = $genotypeWithDescriptionArray[$k];
                        }
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $gain_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/disruptive/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $disruptive_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/splice/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $splice_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/ref/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"border:1px solid black;min-width:120px;background-color:" . $ref_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else {
                        echo "<td id=\"pos__" . $result_arr[$i]["Gene"] . "__" . $key . "__" . $i . "\" style=\"border:1px solid black;min-width:120px;background-color:#FFFFFF\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    }
                }
            }
        }
        foreach ($result_arr[$i] as $key => $value) {
            if (strval($key) == "Imputation") {
                echo "<td style=\"border:1px solid black;min-width:120px;text-align:center\">" . $value . "</td>";
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
    echo "<button type=\"submit\" onclick=\"window.open('https://data.cyverse.org/dav-anon/iplant/home/soykb/Soy1066/Accession_Info.csv')\" style=\"margin-right:20px;\">Download Accession Information</button>";
    echo "</div>";
    echo "<br/><br/>";
}

?>

<script type="text/javascript" language="javascript" src="./js/download.js"></script>

<?php include '../footer.php'; ?>