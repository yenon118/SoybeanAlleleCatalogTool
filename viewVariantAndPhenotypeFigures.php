<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.plot.ly/plotly-2.12.1.min.js"></script>

<?php
$TITLE = "Soybean Genomic Variations Explorer";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$chromosome = $_GET['chromosome_1'];
$position = $_GET['position_1'];
$gene = $_GET['gene_1'];
$genotype = $_GET['genotype_1'];
$phenotype = $_GET['phenotype_1'];
$dataset = $_GET['dataset_1'];

$chromosome = trim($chromosome);
$position = intval(trim($position));
$gene = trim($gene);
$phenotype = trim($phenotype);

if (is_string($genotype)) {
	$genotype_array = preg_split("/[;, \n]+/", $genotype);
	for ($i = 0; $i < count($genotype_array); $i++) {
		$genotype_array[$i] = trim($genotype_array[$i]);
	}
} elseif (is_array($genotype)) {
	$genotype_array = $genotype;
	for ($i = 0; $i < count($genotype_array); $i++) {
		$genotype_array[$i] = trim($genotype_array[$i]);
	}
}

?>

<!-- Query information -->
<?php
echo "<h3>Queried Variant and Phenotype:</h3>";
echo "<div style='width:auto; height:auto; overflow:visible; max-height:1000px;'>";
echo "<table style='text-align:center; border:3px solid #000;'>";
echo "<tr>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Chromsome</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Position</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Genotype</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Phenotype</th>";
echo "</tr>";
echo "<tr bgcolor=\"#DDFFDD\">";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $chromosome . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $position . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . implode(',', $genotype_array) . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $phenotype . "</td>";
echo "</tr>";
echo "</table>";
echo "<br /><br />";
?>

<h3>Figures:</h3>
<div id="genotype_section_div">
	<div id="genotype_figure_div">Loading genotype plot...</div>
	<div id="genotype_summary_table_div">Loading genotype summary table...</div>
</div>
<hr />
<div id="improvement_status_summary_figure_div">Loading improvement status summary plot...</div>
<!-- <div id="improvement_status_figure_div">Loading improvement status plot...</div> -->
<!-- <div id="classification_figure_div">Loading classification plot...</div> -->


<script type="text/javascript" language="javascript" src="./js/viewVariantAndPhenotypeFigures.js"></script>

<script type="text/javascript" language="javascript">
	var chromosome = <?php if(isset($chromosome)) {echo json_encode($chromosome, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
	var position = <?php if(isset($position)) {echo json_encode($position, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
	var gene = <?php if(isset($gene)) {echo json_encode($gene, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
	var phenotype = <?php if(isset($phenotype)) {echo json_encode($phenotype, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
	var genotype_array = <?php if(isset($genotype_array) && is_array($genotype_array) && !empty($genotype_array)) {echo json_encode($genotype_array, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
	var dataset = <?php if(isset($dataset)) {echo json_encode($dataset, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;

	if (chromosome && position && genotype_array.length > 0 && phenotype) {
		$.ajax({
			url: './php/queryVariantAndPhenotypeFigures.php',
			type: 'GET',
			contentType: 'application/json',
			data: {
				Chromosome: chromosome,
				Position: position,
				Gene: gene,
				Genotype: genotype_array,
				Phenotype: phenotype,
				Dataset: dataset
			},
			success: function (response) {
				res = JSON.parse(response);

				if (res && phenotype) {

					document.getElementById("improvement_status_summary_figure_div").style.minHeight = "800px";
					document.getElementById("genotype_figure_div").style.minHeight = "800px";
					// document.getElementById("improvement_status_figure_div").style.minHeight = "800px";
					// document.getElementById("classification_figure_div").style.minHeight = "800px";

					// Summarize data
					var result_dict = summarizeQueriedData(
						JSON.parse(JSON.stringify(res['data'])),
						phenotype,
						'Genotype'
					);

					var result_arr = result_dict['Data'];
					var summary_array = result_dict['Summary'];

					var genotypeAndImprovementStatusData = collectDataForFigure(result_arr, 'Improvement_Status', 'Genotype');
					var genotypeData = collectDataForFigure(result_arr, phenotype, 'Genotype');
					// var improvementStatusData = collectDataForFigure(result_arr, phenotype, 'Improvement_Status');
					// var classificationData = collectDataForFigure(result_arr, phenotype, 'Classification');

					plotFigure(genotypeAndImprovementStatusData, 'Genotype', 'Improvement_Status_Summary', 'improvement_status_summary_figure_div');
					plotFigure(genotypeData, 'Genotype', 'Genotype', 'genotype_figure_div');
					// plotFigure(improvementStatusData, 'Improvement_Status', 'Improvement_Status', 'improvement_status_figure_div');
					// plotFigure(classificationData, 'Classification', 'Classification', 'classification_figure_div');

					// Render summarized data
					document.getElementById('genotype_summary_table_div').innerText = "";
					document.getElementById('genotype_summary_table_div').innerHTML = "";
					document.getElementById('genotype_summary_table_div').appendChild(
						constructInfoTable(summary_array)
					);
					document.getElementById('genotype_summary_table_div').style.overflow = 'scroll';

				}
			},
			error: function (xhr, status, error) {
				console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
				document.getElementById('genotype_figure_div').innerText="";
				document.getElementById('genotype_summary_table_div').innerHTML="";
				document.getElementById('improvement_status_summary_figure_div').innerHTML="";
				// document.getElementById('improvement_status_figure_div').innerHTML="";
				// document.getElementById('classification_figure_div').innerHTML="";
				var p_tag = document.createElement('p');
				p_tag.innerHTML = "Genotype distribution figure is not available due to lack of data!!!";
				document.getElementById('genotype_figure_div').appendChild(p_tag);
				var p_tag = document.createElement('p');
				p_tag.innerHTML = "Genotype summary table is not available due to lack of data!!!";
				document.getElementById('genotype_summary_table_div').appendChild(p_tag);
				var p_tag = document.createElement('p');
				p_tag.innerHTML = "Improvement status summary figure is not available due to lack of data!!!";
				document.getElementById('improvement_status_summary_figure_div').appendChild(p_tag);
				// var p_tag = document.createElement('p');
				// p_tag.innerHTML = "Improvement status distribution figure is not available due to lack of data!!!";
				// document.getElementById('improvement_status_figure_div').appendChild(p_tag);
				// var p_tag = document.createElement('p');
				// p_tag.innerHTML = "Classification distribution figure is not available due to lack of data!!!";
				// document.getElementById('classification_figure_div').appendChild(p_tag);
			}
		});
	} else {
		document.getElementById('genotype_figure_div').innerText="";
		document.getElementById('genotype_summary_table_div').innerHTML="";
		document.getElementById('improvement_status_summary_figure_div').innerHTML="";
		// document.getElementById('improvement_status_figure_div').innerHTML="";
		// document.getElementById('classification_figure_div').innerHTML="";
		var p_tag = document.createElement('p');
		p_tag.innerHTML = "Genotype distribution figure is not available due to lack of data!!!";
		document.getElementById('genotype_figure_div').appendChild(p_tag);
		var p_tag = document.createElement('p');
		p_tag.innerHTML = "Genotype summary table is not available due to lack of data!!!";
		document.getElementById('genotype_summary_table_div').appendChild(p_tag);
		var p_tag = document.createElement('p');
		p_tag.innerHTML = "Improvement status summary figure is not available due to lack of data!!!";
		document.getElementById('improvement_status_summary_figure_div').appendChild(p_tag);
		// var p_tag = document.createElement('p');
		// p_tag.innerHTML = "Improvement status distribution figure is not available due to lack of data!!!";
		// document.getElementById('improvement_status_figure_div').appendChild(p_tag);
		// var p_tag = document.createElement('p');
		// p_tag.innerHTML = "Classification distribution figure is not available due to lack of data!!!";
		// document.getElementById('classification_figure_div').appendChild(p_tag);
	}

</script>