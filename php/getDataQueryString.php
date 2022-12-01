<?php

function getDataQueryString($dataset, $db, $gff_table, $accession_mapping_table, $gene, $chromosome, $where = ""){

    // Generate SQL string
    $query_str = "SELECT ";
    $query_str = $query_str . "ACD.Classification, ACD.Improvement_Status, ";
    $query_str = $query_str . "ACD.Maturity_Group, ACD.Country, ACD.State, ";
    $query_str = $query_str . "ACD.Accession, ACD.SoyKB_Accession, ACD.GRIN_Accession, ";
    $query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
    $query_str = $query_str . "FROM ( ";
    $query_str = $query_str . "    SELECT AM.Classification, AM.Improvement_Status, ";
    $query_str = $query_str . "    AM.Maturity_Group, AM.Country, AM.State, ";
    $query_str = $query_str . "    GD.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, ";
    $query_str = $query_str . "    GD.Gene, GD.Chromosome, ";
    $query_str = $query_str . "    GROUP_CONCAT(GD.Position SEPARATOR ' ') AS Position, ";
    $query_str = $query_str . "    GROUP_CONCAT(GD.Genotype SEPARATOR ' ') AS Genotype, ";
    $query_str = $query_str . "    GROUP_CONCAT(GD.Genotype_Description SEPARATOR ' ') AS Genotype_Description, ";
    $query_str = $query_str . "    GROUP_CONCAT(GD.Imputation SEPARATOR ' ') AS Imputation ";
    $query_str = $query_str . "    FROM ( ";
    $query_str = $query_str . "        SELECT G.Chromosome, G.Position, G.Accession, GFF.Gene, G.Genotype, ";
    $query_str = $query_str . "        CONCAT_WS('|', G.Genotype, G.Functional_Effect, G.Amino_Acid_Change, G.Imputation) AS Genotype_Description, ";
    $query_str = $query_str . "        G.Imputation ";
    $query_str = $query_str . "        FROM ( ";
    $query_str = $query_str . "            SELECT Chromosome, Start, End, Name AS Gene ";
    $query_str = $query_str . "            FROM " . $db . "." . $gff_table . " ";
    $query_str = $query_str . "            WHERE Name IN ('" . $gene . "') ";
    $query_str = $query_str . "        ) AS GFF ";
    $query_str = $query_str . "        INNER JOIN " . $db . ".act_" . $dataset . "_" . $chromosome . " AS G ";
    $query_str = $query_str . "        ON (G.Chromosome = GFF.Chromosome) AND (G.Position >= GFF.Start) AND (G.Position <= GFF.End) ";
    $query_str = $query_str . "        ORDER BY G.Position ";
    $query_str = $query_str . "    ) AS GD ";
    $query_str = $query_str . "    LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
    $query_str = $query_str . "    ON AM.Accession = GD.Accession ";
    $query_str = $query_str . "    GROUP BY AM.Classification, AM.Improvement_Status, AM.Maturity_Group, AM.Country, AM.State, GD.Accession, AM.SoyKB_Accession, AM.GRIN_Accession, GD.Gene, GD.Chromosome ";
    $query_str = $query_str . ") AS ACD ";
    $query_str = $query_str . $where;
    $query_str = $query_str . "ORDER BY ACD.Gene, ACD.Improvement_Status; ";

    return $query_str;
}

?>