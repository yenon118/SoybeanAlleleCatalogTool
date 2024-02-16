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


<!-- Get and process the variables -->
<?php
$gene = trim($_GET['gene_2']);
$dataset = trim($_GET['dataset_2']);
$accession = $_GET['accession_2'];

if (is_string($accession)) {
	$accession = trim($accession);
	$temp_accession_array = preg_split("/[;, \n]+/", $accession);
	$accession_array = array();
	for ($i = 0; $i < count($temp_accession_array); $i++) {
		if (!empty(trim($temp_accession_array[$i]))) {
			array_push($accession_array, trim($temp_accession_array[$i]));
		}
	}
} elseif (is_array($accession)) {
	$temp_accession_array = $accession;
	$accession_array = array();
	for ($i = 0; $i < count($temp_accession_array); $i++) {
		if (!empty(trim($temp_accession_array[$i]))) {
			array_push($accession_array, trim($temp_accession_array[$i]));
		}
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


// Generate SQL string
$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
$query_str = $query_str . "WHERE (Name = '" . $gene . "') OR (UPPER(Name) = UPPER('" . $gene . "'));";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$gene_result_arr = pdoResultFilter($result);


$query_str = "WHERE (ACD.Accession IN ('";
for ($i = 0; $i < count($accession_array); $i++) {
	if($i < (count($accession_array)-1)){
		$query_str = $query_str . trim($accession_array[$i]) . "', '";
	} elseif ($i == (count($accession_array)-1)) {
		$query_str = $query_str . trim($accession_array[$i]);
	}
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "OR (ACD.SoyKB_Accession IN ('";
for ($i = 0; $i < count($accession_array); $i++) {
	if($i < (count($accession_array)-1)){
		$query_str = $query_str . trim($accession_array[$i]) . "', '";
	} elseif ($i == (count($accession_array)-1)) {
		$query_str = $query_str . trim($accession_array[$i]);
	}
}
$query_str = $query_str . "')) ";
$query_str = $query_str . "OR (ACD.GRIN_Accession IN ('";
for ($i = 0; $i < count($accession_array); $i++) {
	if($i < (count($accession_array)-1)){
		$query_str = $query_str . trim($accession_array[$i]) . "', '";
	} elseif ($i == (count($accession_array)-1)) {
		$query_str = $query_str . trim($accession_array[$i]);
	}
}
$query_str = $query_str . "')) ";

// Generate query string
$query_str = getDataQueryString(
	$dataset,
	$db,
	$gff_table,
	$accession_mapping_table,
	$gene_result_arr[0]["Gene"],
	$gene_result_arr[0]["Chromosome"],
	$query_str
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
				echo "<th style=\"border:1px solid black; min-width:80px;\"><a href=\"/SoybeanAlleleCatalogTool/viewVariantAndPhenotype.php?dataset=" . $dataset . "&chromosome=" . $result_arr[0]["Chromosome"] . "&position=" . $position_array[$j] . "&gene=" . $result_arr[0]["Gene"] . "\" target=\"_blank\">" . $position_array[$j] . "</a></th>";
			}
		}
	}
	echo "</tr>";

	// Table body
	for ($j = 0; $j < count($result_arr); $j++) {
		$tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

		$row_id_prefix = $result_arr[$j]["Gene"] . "_" . $result_arr[$j]["Chromosome"] . "_" . $j;

		echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";

		foreach ($result_arr[$j] as $key => $value) {
			if ($key != "Position" && $key != "Genotype" && $key != "Genotype_Description" && $key != "Imputation") {
				if (intval($value) > 0) {
					echo "<td style=\"border:1px solid black;min-width:80px;\">";
					echo $value;
					echo "</td>";
				} else {
					echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
				}
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
						if (count($temp_value_arr) > 3) {
							$genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2] . "|" . $temp_value_arr[3];
						} elseif (count($temp_value_arr) > 2) {
							$genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2];
						}
					} else if (preg_match("/frameshift/i", $genotype_description_array[$k])) {
						$td_bg_color = $frameshift_variant_color_code;
					} else if (preg_match("/exon.loss/i", $genotype_description_array[$k])) {
						$td_bg_color = $exon_loss_variant_color_code;
					} else if (preg_match("/lost/i", $genotype_description_array[$k])) {
						$td_bg_color = $lost_color_code;
						$temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
						if (count($temp_value_arr) > 3) {
							$genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2] . "|" . $temp_value_arr[3];
						} elseif (count($temp_value_arr) > 2) {
							$genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2];
						}
					} else if (preg_match("/gain/i", $genotype_description_array[$k])) {
						$td_bg_color = $gain_color_code;
						$temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
						if (count($temp_value_arr) > 3) {
							$genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2] . "|" . $temp_value_arr[3];
						} elseif (count($temp_value_arr) > 2) {
							$genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2];
						}
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
			} elseif ($key == "Imputation") {
				if (preg_match("/\\+/i", $value)) {
					echo "<td style=\"border:1px solid black;min-width:80px;\">+</td>";
				} else {
					echo "<td style=\"border:1px solid black;min-width:80px;\">-</td>";
				}
			}
		}

		echo "</tr>";
	}

	echo "</table>";
	echo "</div>";

	echo "<div style='margin-top:10px;' align='right'>";
	echo "<button onclick=\"queryAllByAccessionsAndGene('" . $dataset . "', '" . $result_arr[0]["Gene"] . "', '" . implode(";", $accession_array) . "')\"> Download</button>";
	echo "</div>";

	echo "<br />";
	echo "<br />";

} else {
	echo "<p>No Allele Catalog data available for this gene!!!</p>";
}


echo "<br/><br/>";
echo "<div style='margin-top:10px;' align='center'>";
echo "<button onclick=\"queryAccessionInformation('" . $dataset . "')\" style=\"margin-right:20px;\">Download Accession Information</button>";
echo "</div>";
echo "<br/><br/>";

?>


<script type="text/javascript" language="javascript" src="./js/viewAllByAccessionsAndGene.js"></script>

<?php include '../footer.php'; ?>
