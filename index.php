<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Allele Catalog Tool";

include '../header.php';
?>

<a id="demo" type="button" target="_blank" style="display: inline-block;
	font-size: 12px;
    text-align: center;
	height: auto;
    width: auto; 
	padding: 10px;
	border-radius: 5px;
	color: white;
    margin-left: 20px;
    margin-right: 10px;
	font-weight: bold;
	background-color: #4ca20b;
    margin-bottom: 10px;
	text-decoration: none;" href="assets/documents/SBW2024 Flash talk Bilyeu.pdf">Presentations - SBW2024 Flash Talk - Kristin Bilyeu</a>

<div>
	<table width="100%" cellspacing="14" cellpadding="14">
		<tr>
			<td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
				<form action="viewAllByGenes.php" method="get" target="_blank">
					<h2>Search by Gene IDs</h2>
					<br />
					<label for="dataset_1"><b>Dataset:</b></label>
					<select name="dataset_1" id="dataset_1" onchange="updateSearchByGeneIDs(event)">
						<option value="EU_Soy309">EU Soy309 Allele Catalog</option>
						<option value="Soy111">Soy111 Allele Catalog</option>
						<option value="Soy775">Soy775 Allele Catalog</option>
						<option value="Soy2939">Soy2939 Allele Catalog</option>
						<option value="Soy1066" selected>Soy1066 Allele Catalog</option>
					</select>
					<br />
					<br />
					<label><b>Gene IDs:</b></label>
					<span id="gene_examples_1" style="font-size:9pt">&nbsp;(eg Glyma.01G049100 Glyma.01G049200 Glyma.01G049300)</span>
					<br />
					<textarea id="gene_1" name="gene_1" rows="12" cols="50" placeholder="&#10;Please separate each gene into a new line. &#10;&#10;Example:&#10;Glyma.01G049100&#10;Glyma.01G049200&#10;Glyma.01G049300"></textarea>
					<br />
					<br />
					<div id="improvement_status_div_1">
						<label><b>Improvement Status:</b></label>
						<br />
						<input type="checkbox" id="Soja" name="improvement_status_1[]" value="Soja" checked><label style="margin-right:10px;"> Soja</label>
						<input type="checkbox" id="Elite" name="improvement_status_1[]" value="Elite" checked><label style="margin-right:10px;"> Elite</label>
						<input type="checkbox" id="Landrace" name="improvement_status_1[]" value="Landrace" checked><label style="margin-right:10px;"> Landrace</label>
						<input type="checkbox" id="Cultivar" name="improvement_status_1[]" value="Cultivar" checked><label style="margin-right:10px;"> Cultivar</label>
					</div>
					<br />
					<input type="submit" value="Search">
				</form>
			</td>
			<td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
				<form action="viewAllByAccessionsAndGene.php" method="get" target="_blank">
					<h2>Search by Accessions and Gene ID</h2>
					<br />
					<label for="dataset_2"><b>Dataset:</b></label>
					<select name="dataset_2" id="dataset_2" onchange="updateSearchByAccessionsandGeneID(event)">
						<option value="EU_Soy309">EU Soy309 Allele Catalog</option>
						<option value="Soy111">Soy111 Allele Catalog</option>
						<option value="Soy775">Soy775 Allele Catalog</option>
						<option value="Soy2939">Soy2939 Allele Catalog</option>
						<option value="Soy1066" selected>Soy1066 Allele Catalog</option>
					</select>
					<br />
					<br />
					<label><b>Accessions:</b></label>
					<span id="accession_examples_2" style="font-size:9pt">&nbsp;(eg HN005_PI404166 HN006_PI407788A)</span>
					<br />
					<textarea id="accession_2" name="accession_2" rows="12" cols="50" placeholder="&#10;Please separate each accession into a new line. &#10;&#10;Example:&#10;HN005_PI404166&#10;HN006_PI407788A"></textarea>
					<br /><br />
					<label><b>Gene ID:</b></label>
					<span id="gene_example_2" style="font-size:9pt">&nbsp;(One gene name only; eg Glyma.01G049100)</span>
					<br />
					<input type="text" id="gene_2" name="gene_2" size="55"></input>
					<br /><br />
					<input type="submit" value="Search">
				</form>
			</td>
		</tr>
	</table>
</div>

<br />
<br />

<div style='margin-top:10px;' align='center'>
	<button onclick="queryAccessionInformation()" style="min-width:250px;background-color:#FFFFFF;">Download Accession Information</button>
	<button onclick="viewDemo()" style="margin-right:20px;min-width:150px;background-color:#DDFFDD;">View Demo</button>
</div>

<br />
<br />

<hr />

<br />
<br />

<div>
	<table width="100%" cellspacing="14" cellpadding="14">
		<tr>
			<td align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
				<h2>If you use the Soybean Allele Catalog Tool in your work, please cite:</h2>
				<br />
				<p> Chan YO, Dietz N, Zeng S, Wang J, Flint-Garcia S, Salazar-Vidal MN, Škrabišová M, Bilyeu K, Joshi T: <b> The Allele Catalog Tool: a web-based interactive tool for allele discovery and analysis. </b> BMC Genomics 2023, 24(1):107. </p>
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript" language="javascript" src="./js/index.js"></script>

<script type="text/javascript" language="javascript">
</script>

<?php include '../footer.php'; ?>