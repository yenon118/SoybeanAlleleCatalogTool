<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css"></link>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<style>
    .ui-accordion-header.ui-state-active {
        background-color: green;
    }
</style>
<script>
    $(function() {
        $("#accordion").accordion({
            active: false,
            collapsible: true
        });
    });
</script>

<?php
$TITLE = "Soybean Allele Catalog Tool";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$chromosome = $_GET['chromosome'];
$position = $_GET['position'];
$dataset = $_GET['dataset'];

$chromosome = trim($chromosome);
$position = intval(trim($position));
$dataset = trim($dataset);
?>


<?php

echo "<div id=\"accordion\">";
echo "<h3>Coordinate</h3>";
echo "<div>";
echo "<label for=\"chromosome_1\">Chromosome:</label>";
echo "<input type=\"text\" id=\"chromosome_1\" name=\"chromosome_1\" size=\"30\" value=\"" . $chromosome . "\" style=\"margin-right:50px;\">";

echo "<label for=\"position_1\">Position:</label>";
echo "<input type=\"text\" id=\"position_1\" name=\"position_1\" size=\"30\" value=\"" . $position . "\" style=\"margin-right:50px;\">";

echo "</div>";

echo "<h3>Genotype</h3>";
echo "<div id=\"div_genotype_in_accordion\">";
echo "</div>";

echo "<h3>Chemical Descriptor</h3>";
echo "<div>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_0\" name=\"chemical_descriptor_0\" value=\"ARGININE\"><label for=\"chemical_descriptor_0\" style=\"margin-right:10px;\">ARGININE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_1\" name=\"chemical_descriptor_1\" value=\"CYSTEINE\"><label for=\"chemical_descriptor_1\" style=\"margin-right:10px;\">CYSTEINE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_2\" name=\"chemical_descriptor_2\" value=\"FATTYACIDS\"><label for=\"chemical_descriptor_2\" style=\"margin-right:10px;\">FATTYACIDS</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_3\" name=\"chemical_descriptor_3\" value=\"IODINENUM\"><label for=\"chemical_descriptor_3\" style=\"margin-right:10px;\">IODINENUM</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_4\" name=\"chemical_descriptor_4\" value=\"ISOLEUCINE\"><label for=\"chemical_descriptor_4\" style=\"margin-right:10px;\">ISOLEUCINE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_5\" name=\"chemical_descriptor_5\" value=\"LEUCINE\"><label for=\"chemical_descriptor_5\" style=\"margin-right:10px;\">LEUCINE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_6\" name=\"chemical_descriptor_6\" value=\"LINOLEIC\"><label for=\"chemical_descriptor_6\" style=\"margin-right:10px;\">LINOLEIC</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_7\" name=\"chemical_descriptor_7\" value=\"LINOLENIC\"><label for=\"chemical_descriptor_7\" style=\"margin-right:10px;\">LINOLENIC</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_8\" name=\"chemical_descriptor_8\" value=\"LYSINE\"><label for=\"chemical_descriptor_8\" style=\"margin-right:10px;\">LYSINE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_9\" name=\"chemical_descriptor_9\" value=\"METHIONINE\"><label for=\"chemical_descriptor_9\" style=\"margin-right:10px;\">METHIONINE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_10\" name=\"chemical_descriptor_10\" value=\"OIL\"><label for=\"chemical_descriptor_10\" style=\"margin-right:10px;\">OIL</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_11\" name=\"chemical_descriptor_11\" value=\"OLEIC\"><label for=\"chemical_descriptor_11\" style=\"margin-right:10px;\">OLEIC</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_12\" name=\"chemical_descriptor_12\" value=\"P34\"><label for=\"chemical_descriptor_12\" style=\"margin-right:10px;\">P34</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_13\" name=\"chemical_descriptor_13\" value=\"PALMITIC\"><label for=\"chemical_descriptor_13\" style=\"margin-right:10px;\">PALMITIC</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_14\" name=\"chemical_descriptor_14\" value=\"PETUREIDE\"><label for=\"chemical_descriptor_14\" style=\"margin-right:10px;\">PETUREIDE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_15\" name=\"chemical_descriptor_15\" value=\"PROTEIN\"><label for=\"chemical_descriptor_15\" style=\"margin-right:10px;\">PROTEIN</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_16\" name=\"chemical_descriptor_16\" value=\"STACHYOSE\"><label for=\"chemical_descriptor_16\" style=\"margin-right:10px;\">STACHYOSE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_17\" name=\"chemical_descriptor_17\" value=\"STEARIC\"><label for=\"chemical_descriptor_17\" style=\"margin-right:10px;\">STEARIC</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_18\" name=\"chemical_descriptor_18\" value=\"SUCROSE\"><label for=\"chemical_descriptor_18\" style=\"margin-right:10px;\">SUCROSE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_19\" name=\"chemical_descriptor_19\" value=\"THREONINE\"><label for=\"chemical_descriptor_19\" style=\"margin-right:10px;\">THREONINE</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_20\" name=\"chemical_descriptor_20\" value=\"TRYPTOPHAN\"><label for=\"chemical_descriptor_20\" style=\"margin-right:10px;\">TRYPTOPHAN</label>";
echo "<input type=\"checkbox\" id=\"chemical_descriptor_21\" name=\"chemical_descriptor_21\" value=\"VALINE\"><label for=\"chemical_descriptor_21\" style=\"margin-right:10px;\">VALINE</label>";
echo "</div>";
echo "<h3>Disease Descriptor</h3>";
echo "<div>";
echo "<input type=\"checkbox\" id=\"disease_descriptor_0\" name=\"disease_descriptor_0\" value=\"BP\"><label for=\"disease_descriptor_0\" style=\"margin-right:10px;\">BP</label>";
echo "<input type=\"checkbox\" id=\"disease_descriptor_1\" name=\"disease_descriptor_1\" value=\"BPMV\"><label for=\"disease_descriptor_1\" style=\"margin-right:10px;\">BPMV</label>";
echo "<input type=\"checkbox\" id=\"disease_descriptor_2\" name=\"disease_descriptor_2\" value=\"BRNSTEMROT\"><label for=\"disease_descriptor_2\" style=\"margin-right:10px;\">BRNSTEMROT</label>";
echo "<input type=\"checkbox\" id=\"disease_descriptor_3\" name=\"disease_descriptor_3\" value=\"FROGEYE\"><label for=\"disease_descriptor_3\" style=\"margin-right:10px;\">FROGEYE</label>";
echo "<input type=\"checkbox\" id=\"disease_descriptor_4\" name=\"disease_descriptor_4\" value=\"FROGEYE2\"><label for=\"disease_descriptor_4\" style=\"margin-right:10px;\">FROGEYE2</label>";
echo "<input type=\"checkbox\" id=\"disease_descriptor_5\" name=\"disease_descriptor_5\" value=\"FROGEYE3\"><label for=\"disease_descriptor_5\" style=\"margin-right:10px;\">FROGEYE3</label>";
echo "<input type=\"checkbox\" id=\"disease_descriptor_6\" name=\"disease_descriptor_6\" value=\"NSCANKER\"><label for=\"disease_descriptor_6\" style=\"margin-right:10px;\">NSCANKER</label>";
echo "<input type=\"checkbox\" id=\"disease_descriptor_7\" name=\"disease_descriptor_7\" value=\"PHYTOROT\"><label for=\"disease_descriptor_7\" style=\"margin-right:10px;\">PHYTOROT</label>";
echo "<input type=\"checkbox\" id=\"disease_descriptor_8\" name=\"disease_descriptor_8\" value=\"PMV\"><label for=\"disease_descriptor_8\" style=\"margin-right:10px;\">PMV</label>";
echo "</div>";
echo "<h3>Growth Descriptor</h3>";
echo "<div>";
echo "<input type=\"checkbox\" id=\"growth_descriptor_0\" name=\"growth_descriptor_0\" value=\"HEIGHT\"><label for=\"growth_descriptor_0\" style=\"margin-right:10px;\">HEIGHT</label>";
echo "<input type=\"checkbox\" id=\"growth_descriptor_1\" name=\"growth_descriptor_1\" value=\"STEMTERM\"><label for=\"growth_descriptor_1\" style=\"margin-right:10px;\">STEMTERM</label>";
echo "</div>";
echo "<h3>Insect Descriptor</h3>";
echo "<div>";
echo "<input type=\"checkbox\" id=\"insect_descriptor_0\" name=\"insect_descriptor_0\" value=\"APHID\"><label for=\"insect_descriptor_0\" style=\"margin-right:10px;\">APHID</label>";
echo "<input type=\"checkbox\" id=\"insect_descriptor_1\" name=\"insect_descriptor_1\" value=\"BAW\"><label for=\"insect_descriptor_1\" style=\"margin-right:10px;\">BAW</label>";
echo "<input type=\"checkbox\" id=\"insect_descriptor_2\" name=\"insect_descriptor_2\" value=\"EARWORM\"><label for=\"insect_descriptor_2\" style=\"margin-right:10px;\">EARWORM</label>";
echo "<input type=\"checkbox\" id=\"insect_descriptor_3\" name=\"insect_descriptor_3\" value=\"LOOPER\"><label for=\"insect_descriptor_3\" style=\"margin-right:10px;\">LOOPER</label>";
echo "<input type=\"checkbox\" id=\"insect_descriptor_4\" name=\"insect_descriptor_4\" value=\"MEXBEANBTL\"><label for=\"insect_descriptor_4\" style=\"margin-right:10px;\">MEXBEANBTL</label>";
echo "<input type=\"checkbox\" id=\"insect_descriptor_5\" name=\"insect_descriptor_5\" value=\"POTATOLHOP\"><label for=\"insect_descriptor_5\" style=\"margin-right:10px;\">POTATOLHOP</label>";
echo "<input type=\"checkbox\" id=\"insect_descriptor_6\" name=\"insect_descriptor_6\" value=\"VBC\"><label for=\"insect_descriptor_6\" style=\"margin-right:10px;\">VBC</label>";
echo "</div>";
echo "<h3>Morphology Descriptor</h3>";
echo "<div>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_0\" name=\"morphology_descriptor_0\" value=\"BRANCHING\"><label for=\"morphology_descriptor_0\" style=\"margin-right:10px;\">BRANCHING</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_1\" name=\"morphology_descriptor_1\" value=\"HILUMCOLOR\"><label for=\"morphology_descriptor_1\" style=\"margin-right:10px;\">HILUMCOLOR</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_2\" name=\"morphology_descriptor_2\" value=\"LEAFSHAPE\"><label for=\"morphology_descriptor_2\" style=\"margin-right:10px;\">LEAFSHAPE</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_3\" name=\"morphology_descriptor_3\" value=\"LEAFSIZE\"><label for=\"morphology_descriptor_3\" style=\"margin-right:10px;\">LEAFSIZE</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_4\" name=\"morphology_descriptor_4\" value=\"LODGING\"><label for=\"morphology_descriptor_4\" style=\"margin-right:10px;\">LODGING</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_5\" name=\"morphology_descriptor_5\" value=\"LOWERLFSHP\"><label for=\"morphology_descriptor_5\" style=\"margin-right:10px;\">LOWERLFSHP</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_6\" name=\"morphology_descriptor_6\" value=\"LOWLEAFAR\"><label for=\"morphology_descriptor_6\" style=\"margin-right:10px;\">LOWLEAFAR</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_7\" name=\"morphology_descriptor_7\" value=\"LOWLEAFASP\"><label for=\"morphology_descriptor_7\" style=\"margin-right:10px;\">LOWLEAFASP</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_8\" name=\"morphology_descriptor_8\" value=\"MOTTLING\"><label for=\"morphology_descriptor_8\" style=\"margin-right:10px;\">MOTTLING</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_9\" name=\"morphology_descriptor_9\" value=\"OTHERLEAF\"><label for=\"morphology_descriptor_9\" style=\"margin-right:10px;\">OTHERLEAF</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_10\" name=\"morphology_descriptor_10\" value=\"OTHERPLANT\"><label for=\"morphology_descriptor_10\" style=\"margin-right:10px;\">OTHERPLANT</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_11\" name=\"morphology_descriptor_11\" value=\"OTHERSEED\"><label for=\"morphology_descriptor_11\" style=\"margin-right:10px;\">OTHERSEED</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_12\" name=\"morphology_descriptor_12\" value=\"PODCOLOR\"><label for=\"morphology_descriptor_12\" style=\"margin-right:10px;\">PODCOLOR</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_13\" name=\"morphology_descriptor_13\" value=\"PODLENGTH\"><label for=\"morphology_descriptor_13\" style=\"margin-right:10px;\">PODLENGTH</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_14\" name=\"morphology_descriptor_14\" value=\"PUBCOLOR\"><label for=\"morphology_descriptor_14\" style=\"margin-right:10px;\">PUBCOLOR</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_15\" name=\"morphology_descriptor_15\" value=\"PUBDENSITY\"><label for=\"morphology_descriptor_15\" style=\"margin-right:10px;\">PUBDENSITY</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_16\" name=\"morphology_descriptor_16\" value=\"PUBFORM\"><label for=\"morphology_descriptor_16\" style=\"margin-right:10px;\">PUBFORM</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_17\" name=\"morphology_descriptor_17\" value=\"SCOATCOLOR\"><label for=\"morphology_descriptor_17\" style=\"margin-right:10px;\">SCOATCOLOR</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_18\" name=\"morphology_descriptor_18\" value=\"SCOATLUST\"><label for=\"morphology_descriptor_18\" style=\"margin-right:10px;\">SCOATLUST</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_19\" name=\"morphology_descriptor_19\" value=\"SEEDQUAL\"><label for=\"morphology_descriptor_19\" style=\"margin-right:10px;\">SEEDQUAL</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_20\" name=\"morphology_descriptor_20\" value=\"SEEDSHAPE1\"><label for=\"morphology_descriptor_20\" style=\"margin-right:10px;\">SEEDSHAPE1</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_21\" name=\"morphology_descriptor_21\" value=\"SEEDSHAPE2\"><label for=\"morphology_descriptor_21\" style=\"margin-right:10px;\">SEEDSHAPE2</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_22\" name=\"morphology_descriptor_22\" value=\"SEEDWEIGHT\"><label for=\"morphology_descriptor_22\" style=\"margin-right:10px;\">SEEDWEIGHT</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_23\" name=\"morphology_descriptor_23\" value=\"SHATEARLY\"><label for=\"morphology_descriptor_23\" style=\"margin-right:10px;\">SHATEARLY</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_24\" name=\"morphology_descriptor_24\" value=\"SHATLATE\"><label for=\"morphology_descriptor_24\" style=\"margin-right:10px;\">SHATLATE</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_25\" name=\"morphology_descriptor_25\" value=\"TERMSCORE\"><label for=\"morphology_descriptor_25\" style=\"margin-right:10px;\">TERMSCORE</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_26\" name=\"morphology_descriptor_26\" value=\"UPPERLFLEN\"><label for=\"morphology_descriptor_26\" style=\"margin-right:10px;\">UPPERLFLEN</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_27\" name=\"morphology_descriptor_27\" value=\"UPPERLFSHP\"><label for=\"morphology_descriptor_27\" style=\"margin-right:10px;\">UPPERLFSHP</label>";
echo "<input type=\"checkbox\" id=\"morphology_descriptor_28\" name=\"morphology_descriptor_28\" value=\"FLWRCOLOR\"><label for=\"morphology_descriptor_28\" style=\"margin-right:10px;\">FLWRCOLOR</label>";
echo "</div>";
echo "<h3>Other Descriptor</h3>";
echo "<div>";
echo "<input type=\"checkbox\" id=\"other_descriptor_0\" name=\"other_descriptor_0\" value=\"ACIMPT\"><label for=\"other_descriptor_0\" style=\"margin-right:10px;\">ACIMPT</label>";
echo "<input type=\"checkbox\" id=\"other_descriptor_1\" name=\"other_descriptor_1\" value=\"NEMATCYST\"><label for=\"other_descriptor_1\" style=\"margin-right:10px;\">NEMATCYST</label>";
echo "<input type=\"checkbox\" id=\"other_descriptor_2\" name=\"other_descriptor_2\" value=\"YIELD\"><label for=\"other_descriptor_2\" style=\"margin-right:10px;\">YIELD</label>";
echo "<input type=\"checkbox\" id=\"other_descriptor_3\" name=\"other_descriptor_3\" value=\"FLUORESCE\"><label for=\"other_descriptor_3\" style=\"margin-right:10px;\">FLUORESCE</label>";
echo "<input type=\"checkbox\" id=\"other_descriptor_4\" name=\"other_descriptor_4\" value=\"LATITUDE\"><label for=\"other_descriptor_4\" style=\"margin-right:10px;\">LATITUDE</label>";
echo "<input type=\"checkbox\" id=\"other_descriptor_5\" name=\"other_descriptor_5\" value=\"LONGITUDE\"><label for=\"other_descriptor_5\" style=\"margin-right:10px;\">LONGITUDE</label>";
echo "</div>";
echo "<h3>Phenology Descriptor</h3>";
echo "<div>";
echo "<input type=\"checkbox\" id=\"phenology_descriptor_0\" name=\"phenology_descriptor_0\" value=\"FLOWERDATE\"><label for=\"phenology_descriptor_0\" style=\"margin-right:10px;\">FLOWERDATE</label>";
echo "<input type=\"checkbox\" id=\"phenology_descriptor_1\" name=\"phenology_descriptor_1\" value=\"MATDATE\"><label for=\"phenology_descriptor_1\" style=\"margin-right:10px;\">MATDATE</label>";
echo "<input type=\"checkbox\" id=\"phenology_descriptor_2\" name=\"phenology_descriptor_2\" value=\"MATGROUP\"><label for=\"phenology_descriptor_2\" style=\"margin-right:10px;\">MATGROUP</label>";
echo "<input type=\"checkbox\" id=\"phenology_descriptor_3\" name=\"phenology_descriptor_3\" value=\"TWINING\"><label for=\"phenology_descriptor_3\" style=\"margin-right:10px;\">TWINING</label>";
echo "</div>";
echo "<h3>Qualifier</h3>";
echo "<div>";
echo "<input type=\"checkbox\" id=\"qualifier_0\" name=\"qualifier_0\" value=\"PYTHIUMROT\"><label for=\"qualifier_0\" style=\"margin-right:10px;\">PYTHIUMROT</label>";
echo "<input type=\"checkbox\" id=\"qualifier_1\" name=\"qualifier_1\" value=\"RUST\"><label for=\"qualifier_1\" style=\"margin-right:10px;\">RUST</label>";
echo "<input type=\"checkbox\" id=\"qualifier_2\" name=\"qualifier_2\" value=\"SDS\"><label for=\"qualifier_2\" style=\"margin-right:10px;\">SDS</label>";
echo "<input type=\"checkbox\" id=\"qualifier_3\" name=\"qualifier_3\" value=\"SMV\"><label for=\"qualifier_3\" style=\"margin-right:10px;\">SMV</label>";
echo "<input type=\"checkbox\" id=\"qualifier_4\" name=\"qualifier_4\" value=\"SSCANKER\"><label for=\"qualifier_4\" style=\"margin-right:10px;\">SSCANKER</label>";
echo "</div>";
echo "<h3>Stress Descriptor</h3>";
echo "<div>";
echo "<input type=\"checkbox\" id=\"stress_descriptor_0\" name=\"stress_descriptor_0\" value=\"CHLOROSIS\"><label for=\"stress_descriptor_0\" style=\"margin-right:10px;\">CHLOROSIS</label>";
echo "<input type=\"checkbox\" id=\"stress_descriptor_1\" name=\"stress_descriptor_1\" value=\"HIGHTEMP\"><label for=\"stress_descriptor_1\" style=\"margin-right:10px;\">HIGHTEMP</label>";
echo "<input type=\"checkbox\" id=\"stress_descriptor_2\" name=\"stress_descriptor_2\" value=\"SALTREACT\"><label for=\"stress_descriptor_2\" style=\"margin-right:10px;\">SALTREACT</label>";
echo "</div>";
echo "</div>";

echo "<br/><br/>";
echo "<div style='margin-top:10px;' align='center'>";
echo "<button onclick=\"uncheck_all_genotype()\" style=\"margin-right:20px;\">Uncheck All Genotypes</button>";
echo "<button onclick=\"check_all_genotype()\" style=\"margin-right:20px;\">Check All Genotypes</button>";
echo "<button onclick=\"uncheck_all_phenotypes()\" style=\"margin-right:20px;\">Uncheck All Phenotypes</button>";
echo "<button onclick=\"check_all_phenotypes()\" style=\"margin-right:20px;\">Check All Phenotypes</button>";
echo "<button onclick=\"queryVariantAndPhenotype()\" style=\"margin-right:20px;\">View Data</button>";
echo "<button onclick=\"downloadVariantAndPhenotype()\" style=\"margin-right:20px;\">Download Data</button>";
echo "</div>";
echo "<br/><br/>";

echo "<div id=\"Variant_and_Phenotye_detail_table\" style='width:auto; height:auto; overflow:scroll; max-height:1000px;'></div>";

?>

<script type="text/javascript" language="javascript" src="./js/viewVariantAndPhenotype.js"></script>

<script type="text/javascript" language="javascript">
    var chromosome = <?php if(isset($chromosome)) {echo json_encode($chromosome, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var position = <?php if(isset($position)) {echo json_encode($position, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var dataset = <?php if(isset($dataset)) {echo json_encode($dataset, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;

    updateGenotypeInAccordion(dataset);
</script>

<?php include '../footer.php'; ?>