<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Allele Catalog Tool";

include '../config.php';
include './php/pdoResultFilter.php';
include './php/getTableNames.php';
include './php/getSummarizedDataQueryString.php';
include './php/getDataQueryString.php';
?>

<link rel="stylesheet" href="css/modal.css" />


<!-- Back button -->
<a href="/SoybeanAlleleCatalogTool/"><button> &lt; Back </button></a>

<br />
<br />


<!-- Modal -->
<div id="info-modal" class="info-modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <div id="modal-content-div" style='width:100%; height:auto; border:3px solid #000; overflow:scroll;max-height:1000px;'></div>
        <div id="modal-content-comment"></div>
    </div>
</div>


<!-- Get and process the variables -->
<?php
$gene = $_GET['gene_1'];
$dataset = $_GET['dataset_1'];
$improvement_status_array = $_GET['improvement_status_1'];

if (is_string($gene)) {
    $gene_array = preg_split("/[;, \n]+/", $gene);
    for ($i = 0; $i < count($gene_array); $i++) {
        $gene_array[$i] = trim($gene_array[$i]);
    }
} elseif (is_array($gene)) {
    $gene_array = $gene;
    for ($i = 0; $i < count($gene_array); $i++) {
        $gene_array[$i] = trim($gene_array[$i]);
    }
}

$db = "soykb";

// Table names and datasets
$table_names = getTableNames($dataset);
$key_column = $table_names["key_column"];
$gff_table = $table_names["gff_table"];
$accession_mapping_table = $table_names["accession_mapping_table"];
?>


<!-- Query data from database and render data-->
<?php
// Color for functional effects
$ref_color_code = "#D1D1D1";
$missense_variant_color_code = "#7FC8F5";
$frameshift_variant_color_code = "#F26A55";
$exon_loss_variant_color_code = "#F26A55";
$lost_color_code = "#F26A55";
$gain_color_code = "#F26A55";
$disruptive_color_code = "#F26A55";
$conservative_color_code = "#FF7F50";
$splice_color_code = "#9EE85C";

for ($i = 0; $i < count($gene_array); $i++) {

    // Generate SQL string
    $query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
    $query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
    $query_str = $query_str . "WHERE (Name = '" . $gene_array[$i] . "') OR (UPPER(Name) = UPPER('" . $gene_array[$i] . "'));";

    $stmt = $PDO->prepare($query_str);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $gene_result_arr = pdoResultFilter($result);

    // Generate query string
    $query_str = getSummarizedDataQueryString(
        $dataset,
        $db,
        $gff_table,
        $accession_mapping_table,
        $gene_result_arr[0]["Gene"],
        $gene_result_arr[0]["Chromosome"],
        $improvement_status_array,
        ""
    );

    $stmt = $PDO->prepare($query_str);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $result_arr = pdoResultFilter($result);

    // Render result to a table
    if(isset($result_arr) && is_array($result_arr) && !empty($result_arr)) {

        // Make table
        echo "<div style='width:100%; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px;'>";
        echo "<table style='text-align:center;'>";

        // Table header
        echo "<tr>";
        echo "<th></th>";
        foreach ($result_arr[0] as $key => $value) {
            if ($key != "Gene" && $key != "Chromosome" && $key != "Position" && $key != "Genotype" && $key != "Genotype_Description") {
                // Improvement status count section
                echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
            } elseif ($key == "Gene") {
                echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
            } elseif ($key == "Chromosome") {
                echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
            } elseif ($key == "Position") {
                // Position and genotype_description section
                $position_array = preg_split("/[;, \n]+/", $value);
                for ($j = 0; $j < count($position_array); $j++) {
                    // echo "<th style=\"border:1px solid black; min-width:80px;\">" . $position_array[$j] . "</th>";
                    echo "<th style=\"border:1px solid black; min-width:80px;\"><a href=\"/SoybeanAlleleCatalogTool2/viewVariantAndPhenotype.php?dataset=" . $dataset . "&chromosome=" . $result_arr[0]["Chromosome"] . "&position=" . $position_array[$j] . "\" target=\"_blank\">" . $position_array[$j] . "</a></th>";
                }
            }
        }
        echo "<th></th>";
        echo "</tr>";

        // Table body
        for ($j = 0; $j < count($result_arr); $j++) {
            $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

            $row_id_prefix = $result_arr[$j]["Gene"] . "_" . $result_arr[$j]["Chromosome"] . "_" . $j;

            echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
            echo "<td><input type=\"checkbox\" id=\"" . $row_id_prefix . "_l" . "\" name=\"" . $row_id_prefix . "_l" . "\" value=\"" . $row_id_prefix . "_l" . "\" onclick=\"checkHighlight(this)\"></td>";

            foreach ($result_arr[$j] as $key => $value) {
                if ($key != "Gene" && $key != "Chromosome" && $key != "Position" && $key != "Genotype" && $key != "Genotype_Description") {
                    // Improvement status count section
                    if (intval($value) > 0) {
                        echo "<td style=\"border:1px solid black;min-width:80px;\">";
                        echo "<a href=\"javascript:void(0);\" onclick=\"queryMetadataByImprovementStatusAndGenotypeCombination('" . strval($dataset) . "', '" . strval($key) . "', '" . $result_arr[$j]["Gene"] . "', '" . $result_arr[$j]["Chromosome"] . "', '" . $result_arr[$j]["Position"] . "', '" . $result_arr[$j]["Genotype"] . "', '" . $result_arr[$j]["Genotype_Description"] . "')\">";
                        echo $value;
                        echo "</a>";
                        echo "</td>";
                    } else {
                        echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
                    }
                } elseif ($key == "Gene") {
                    echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
                } elseif ($key == "Chromosome") {
                    echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
                } elseif ($key == "Genotype_Description") {
                    // Position and genotype_description section
                    $position_array = preg_split("/[;, \n]+/", $result_arr[$j]["Position"]);
                    $genotype_description_array = preg_split("/[;, \n]+/", $value);
                    for ($k = 0; $k < count($genotype_description_array); $k++) {

                        // Change genotype_description background color
                        $td_bg_color = "#FFFFFF";
                        if (preg_match("/missense.variant/i", $genotype_description_array[$k])) {
                            $td_bg_color = $missense_variant_color_code;
                            $temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
                            $genotype_description_array[$k] = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotype_description_array[$k]);
                        } else if (preg_match("/frameshift/i", $genotype_description_array[$k])) {
                            $td_bg_color = $frameshift_variant_color_code;
                        } else if (preg_match("/exon.loss/i", $genotype_description_array[$k])) {
                            $td_bg_color = $exon_loss_variant_color_code;
                        } else if (preg_match("/lost/i", $genotype_description_array[$k])) {
                            $td_bg_color = $lost_color_code;
                            $temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
                            $genotype_description_array[$k] = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotype_description_array[$k]);
                        } else if (preg_match("/gain/i", $genotype_description_array[$k])) {
                            $td_bg_color = $gain_color_code;
                            $temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
                            $genotype_description_array[$k] = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotype_description_array[$k]);
                        } else if (preg_match("/disruptive/i", $genotype_description_array[$k])) {
                            $td_bg_color = $disruptive_color_code;
                        } else if (preg_match("/conservative/i", $genotype_description_array[$k])) {
                            $td_bg_color = $conservative_color_code;
                        } else if (preg_match("/splice/i", $genotype_description_array[$k])) {
                            $td_bg_color = $splice_color_code;
                        } else if (preg_match("/ref/i", $genotype_description_array[$k])) {
                            $td_bg_color = $ref_color_code;
                        }

                        echo "<td id=\"" . $row_id_prefix . "_" . $position_array[$k] . "\" style=\"border:1px solid black;min-width:80px;background-color:" . $td_bg_color . "\">" . $genotype_description_array[$k] . "</td>";
                    }
                }
            }

            echo "<td><input type=\"checkbox\" id=\"" . $row_id_prefix . "_r" . "\" name=\"" . $row_id_prefix . "_r" . "\" value=\"" . $row_id_prefix . "_r" . "\" onclick=\"checkHighlight(this)\"></td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";

        echo "<div style='margin-top:10px;' align='right'>";
        echo "<button onclick=\"queryAllCountsByGene('" . $dataset . "', '" . $result_arr[0]["Gene"] . "', '" . implode(";", $improvement_status_array) . "')\" style=\"margin-right:20px;\"> Download (Accession Counts)</button>";
        echo "<button onclick=\"queryAllByGene('" . $dataset . "', '" . $result_arr[0]["Gene"] . "', '" . implode(";", $improvement_status_array) . "')\"> Download (All Accessions)</button>";
        echo "</div>";

        echo "<br />";
        echo "<br />";

    } else {
        echo "<p>No Allele Catalog data available for " . $gene_array[$i] . " gene!!!</p>";
    }
}


if (count($result_arr) > 0) {
    echo "<br/><br/>";
    echo "<div style='margin-top:10px;' align='center'>";
    echo "<button onclick=\"queryAccessionInformation('" . $dataset . "')\" style=\"margin-right:20px;\">Download Accession Information</button>";
    echo "<button onclick=\"queryAllCountsByMultipleGenes('" . $dataset . "', '" . implode(";", $gene_array) . "', '" . implode(";", $improvement_status_array) . "')\" style=\"margin-right:20px;\"> Download All (Accession Counts)</button>";
    echo "<button onclick=\"queryAllByMultipleGenes('" . $dataset . "', '" . implode(";", $gene_array) . "', '" . implode(";", $improvement_status_array) . "')\" style=\"margin-right:20px;\"> Download All (All Accessions)</button>";
    echo "</div>";
    echo "<br/><br/>";
}

?>


<script type="text/javascript" language="javascript" src="./js/modal.js"></script>
<script type="text/javascript" language="javascript" src="./js/viewAllByGenes.js"></script>

<?php include '../footer.php'; ?>
