<?php

include '../../config.php';
include 'pdoResultFilter.php';


$chromosome = $_GET['Chromosome'];
$position = $_GET['Position'];
$gene = $_GET['Gene'];
$genotype = $_GET['Genotype'];
$phenotype = $_GET['Phenotype'];
$dataset = $_GET['Dataset'];


$chromosome = clean_malicious_input($chromosome);
$chromosome = preg_replace('/\s+/', '', $chromosome);

$position = clean_malicious_input($position);
$position = preg_replace('/\s+/', '', $position);

$gene = clean_malicious_input($gene);
$gene = preg_replace('/\s+/', '', $gene);

$genotype = clean_malicious_input($genotype);

$phenotype = clean_malicious_input($phenotype);

$dataset = clean_malicious_input($dataset);
$dataset = preg_replace('/\s+/', '', $dataset);

$position = abs(intval($position));


$db = "soykb";
$genotype_table = "act_" . $dataset . "_genotype_" . $chromosome;
$functional_effect_table = "act_" . $dataset . "_func_eff_" . $chromosome;
$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
$phenotype_table = "act_" . $dataset . "_Phenotype_Data";


if (isset($genotype)) {
	if (is_string($genotype)) {
		$genotype = trim($genotype);
		$temp_genotype_array = preg_split("/[;, \n]+/", $genotype);
		$genotype_array = array();
		for ($i = 0; $i < count($temp_genotype_array); $i++) {
			if (!empty(trim($temp_genotype_array[$i]))) {
				array_push($genotype_array, trim($temp_genotype_array[$i]));
			}
		}
	} elseif (is_array($genotype)) {
		$temp_genotype_array = $genotype;
		$genotype_array = array();
		for ($i = 0; $i < count($temp_genotype_array); $i++) {
			if (!empty(trim($temp_genotype_array[$i]))) {
				array_push($genotype_array, trim($temp_genotype_array[$i]));
			}
		}
	}
}


if (isset($phenotype)) {
	if (is_string($phenotype)) {
		$phenotype = trim($phenotype);
		$temp_phenotype_array = preg_split("/[;, \n]+/", $phenotype);
		$phenotype_array = array();
		for ($i = 0; $i < count($temp_phenotype_array); $i++) {
			if (!empty(trim($temp_phenotype_array[$i]))) {
				array_push($phenotype_array, trim($temp_phenotype_array[$i]));
			}
		}
	} elseif (is_array($phenotype)) {
		$temp_phenotype_array = $phenotype;
		$phenotype_array = array();
		for ($i = 0; $i < count($temp_phenotype_array); $i++) {
			if (!empty(trim($temp_phenotype_array[$i]))) {
				array_push($phenotype_array, trim($temp_phenotype_array[$i]));
			}
		}
	}
}


// Construct query string
$query_str = "SELECT GENO.Chromosome, GENO.Position, GENO.Accession, ";
$query_str = $query_str . "AM.SoyKB_Accession, AM.GRIN_Accession, AM.Improvement_Status, AM.Classification, ";
$query_str = $query_str . "GENO.Genotype, ";
$query_str = $query_str . "COALESCE( FUNC.Functional_Effect, GENO.Category ) AS Functional_Effect, ";
$query_str = $query_str . "GENO.Imputation ";

if (isset($phenotype_array)) {
	if (!empty($phenotype_array)) {
		if (is_array($phenotype_array)) {
			if (count($phenotype_array) > 0) {
				for ($i = 0; $i < count($phenotype_array); $i++) {
					$query_str = $query_str . ", PH." . $phenotype_array[$i] . " ";
				}
			}
		}
	}
}

$query_str = $query_str . "FROM ( ";
$query_str = $query_str . "    SELECT G.Chromosome, G.Position, G.Accession, G.Genotype, G.Category, G.Imputation ";
$query_str = $query_str . "    FROM " . $db . "." . $genotype_table . " AS G ";
$query_str = $query_str . "    WHERE (G.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "    AND (G.Position = " . $position . ") ";

if (isset($genotype_array)) {
	if (!empty($genotype_array)) {
		if (is_array($genotype_array)) {
			if (count($genotype_array) > 0) {
				$query_str = $query_str . "    AND (G.Genotype IN ('";
				for ($i = 0; $i < count($genotype_array); $i++) {
					if ($i < (count($genotype_array) - 1)) {
						$query_str = $query_str . trim($genotype_array[$i]) . "', '";
					} elseif ($i == (count($genotype_array) - 1)) {
						$query_str = $query_str . trim($genotype_array[$i]);
					}
				}
				$query_str = $query_str . "')) ";
			}
		}
	}
}

$query_str = $query_str . ") AS GENO ";
$query_str = $query_str . "LEFT JOIN ( ";
$query_str = $query_str . "    SELECT F.Chromosome, F.Position, F.Allele, F.Gene, F.Functional_Effect ";
$query_str = $query_str . "    FROM " . $db . "." . $functional_effect_table . " AS F ";
$query_str = $query_str . "    WHERE (F.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "    AND (F.Position = " . $position . ") ";

if (isset($genotype_array)) {
	if (!empty($genotype_array)) {
		if (is_array($genotype_array)) {
			if (count($genotype_array) > 0) {
				$query_str = $query_str . "    AND (F.Allele IN ('";
				for ($i = 0; $i < count($genotype_array); $i++) {
					if ($i < (count($genotype_array) - 1)) {
						$query_str = $query_str . trim($genotype_array[$i]) . "', '";
					} elseif ($i == (count($genotype_array) - 1)) {
						$query_str = $query_str . trim($genotype_array[$i]);
					}
				}
				$query_str = $query_str . "')) ";
			}
		}
	}
}

$query_str = $query_str . "    AND (F.Gene LIKE '%" . $gene . "%') ";
$query_str = $query_str . ") AS FUNC ";
$query_str = $query_str . "ON GENO.Chromosome = FUNC.Chromosome AND GENO.Position = FUNC.Position AND GENO.Genotype = FUNC.Allele ";
$query_str = $query_str . "LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
$query_str = $query_str . "ON CAST(GENO.Accession AS BINARY) = CAST(AM.Accession AS BINARY) ";

if (isset($phenotype_array)) {
	if (!empty($phenotype_array)) {
		if (is_array($phenotype_array)) {
			if (count($phenotype_array) > 0) {
				$query_str = $query_str . "LEFT JOIN " . $db . "." . $phenotype_table . " AS PH ";
				$query_str = $query_str . "ON CAST(AM.GRIN_Accession AS BINARY) = CAST(PH.ACNO AS BINARY) ";
			}
		}
	}
}

$query_str = $query_str . "ORDER BY GENO.Chromosome, GENO.Position, GENO.Genotype; ";


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
