<?php

function getDataQueryString($dataset, $db, $gff_table, $accession_mapping_table, $gene, $chromosome, $where = ""){

	// Generate SQL string
	$query_str = "SELECT ACD.Classification, ACD.Improvement_Status, ";
	$query_str = $query_str . "ACD.Maturity_Group, ACD.Country, ACD.State, ";
	$query_str = $query_str . "ACD.Accession, ACD.SoyKB_Accession, ACD.GRIN_Accession, ";
	$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
	$query_str = $query_str . "FROM ( ";
	$query_str = $query_str . "	SELECT AM.Classification, AM.Improvement_Status, ";
	$query_str = $query_str . "	AM.Maturity_Group, AM.Country, AM.State, ";
	$query_str = $query_str . "	GENO.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, ";
	$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
	$query_str = $query_str . "	GROUP_CONCAT(GENO.Position SEPARATOR ' ') AS Position, ";
	$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype SEPARATOR ' ') AS Genotype, ";
	$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) SEPARATOR ' ') AS Genotype_Description, ";
	$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') SEPARATOR ' ') AS Imputation ";
	$query_str = $query_str . "	FROM ( ";
	$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
	$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
	$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
	$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
	$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
	$query_str = $query_str . "	) AS COMB1 ";
	$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
	$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
	$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
	$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) ";
	$query_str = $query_str . "	LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
	$query_str = $query_str . "	ON (AM.Accession = GENO.Accession) ";
	$query_str = $query_str . "	GROUP BY AM.Classification, AM.Improvement_Status, AM.Maturity_Group, AM.Country, AM.State, GENO.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, COMB1.Gene, GENO.Chromosome ";
	$query_str = $query_str . ") AS ACD ";
	$query_str = $query_str . $where . " ";
	$query_str = $query_str . "ORDER BY ACD.Gene, ACD.Improvement_Status; ";

	return $query_str;
}

?>