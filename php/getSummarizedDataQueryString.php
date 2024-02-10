<?php

function getSummarizedDataQueryString($dataset, $db, $gff_table, $accession_mapping_table, $gene, $chromosome, $improvement_status_array, $having = ""){

	// Generate SQL string
	$query_str = "SELECT ";
	if (!empty($improvement_status_array)) {
		if (in_array("Soja", $improvement_status_array)) {
			$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'G. soja', 1, null)) AS Soja, ";
		}
	}
	if (!empty($improvement_status_array)) {
		if (in_array("Landrace", $improvement_status_array)) {
			$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Landrace', 1, null)) AS Landrace, ";
		}
	}
	if (!empty($improvement_status_array)) {
		if (in_array("Elite", $improvement_status_array)) {
			$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status IN ('Cultivar', 'Elite'), 1, null)) AS Elite, ";
		}
	}
	$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
	if (!empty($improvement_status_array)) {
		if (in_array("Cultivar", $improvement_status_array)) {
			$query_str = $query_str . "COUNT(IF(ACD.Classification = 'NA Cultivar', 1, null)) AS Cultivar, ";
		}
	}

	$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
	$query_str = $query_str . "FROM ( ";
	$query_str = $query_str . "	SELECT AM.Classification, AM.Improvement_Status, ";
	$query_str = $query_str . "	GENO.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, ";
	$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
	$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
	$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
	$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
	$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
	$query_str = $query_str . "	FROM ( ";
	$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
	$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
	$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
	$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
	$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
	$query_str = $query_str . "		ORDER BY FUNC.Chromosome, FUNC.Position ";
	$query_str = $query_str . "	) AS COMB1 ";
	$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
	$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
	$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
	$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
	$query_str = $query_str . "	LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
	$query_str = $query_str . "	ON (AM.Accession = GENO.Accession) ";
	$query_str = $query_str . "	GROUP BY AM.Classification, AM.Improvement_Status, GENO.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, COMB1.Gene, GENO.Chromosome ";
	$query_str = $query_str . ") AS ACD ";
	$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
	$query_str = $query_str . $having . " ";
	$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";

	return $query_str;
}

?>