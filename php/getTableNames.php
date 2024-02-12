<?php

function getTableNames($dataset){

	// Table names and datasets
	if ($dataset == "Soy775") {
		$key_column = "Improvement_Status";
		$gff_table = "act_Soybean_Wm82a2v1_GFF";
		$accession_mapping_table = "act_Soy775_Accession_Mapping";
	} elseif ($dataset == "Soy1066") {
		$key_column = "Improvement_Status";
		$gff_table = "act_Soybean_Wm82a2v1_GFF";
		$accession_mapping_table = "act_Soy1066_Accession_Mapping";
	} elseif ($dataset == "Soy2939") {
		$key_column = "Improvement_Status";
		$gff_table = "act_Soybean_Wm82a2v1_GFF";
		$accession_mapping_table = "act_Soy2939_Accession_Mapping";
	} elseif ($dataset == "Soy111") {
		$key_column = "";
		$gff_table = "act_Soybean_Wm82a2v1_GFF";
		$accession_mapping_table = "act_Soy111_Accession_Mapping";
	} elseif ($dataset == "EU_Soy309") {
		$key_column = "";
		$gff_table = "act_Soybean_Wm82a2v1_GFF";
		$accession_mapping_table = "act_EU_Soy309_Accession_Mapping";
	} else {
		$key_column = "";
		$gff_table = "";
		$accession_mapping_table = $dataset;
	}

	return array(
		"key_column" => $key_column,
		"gff_table" => $gff_table,
		"accession_mapping_table" => $accession_mapping_table
	);
}

?>