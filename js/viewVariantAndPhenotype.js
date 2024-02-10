function updateGenotypeInAccordion(dataset) {
	let chromosome_1 = document.getElementById('chromosome_1').value;
	let position_1 = document.getElementById('position_1').value;
	var div_genotype_in_accordion = document.getElementById("div_genotype_in_accordion");
	div_genotype_in_accordion.innerHTML = '';

	if (chromosome_1 && position_1) {
		$.ajax({
			url: './php/queryDistinctGenotypesAtCoordinate.php',
			type: 'GET',
			contentType: 'application/json',
			data: {
				Chromosome: chromosome_1,
				Position: position_1,
				Dataset: dataset
			},
			success: function (response) {
				res = JSON.parse(response);
				res = res.data;

				if (res.length > 0) {
					for (let i = 0; i < res.length; i++) {
						var input_tag = document.createElement('input');
						input_tag.type = 'checkbox';
						input_tag.id = "genotype_" + res[i]["Genotype"];
						input_tag.name = "genotype_" + res[i]["Genotype"];
						input_tag.value = res[i]["Genotype"];

						var label_tag = document.createElement('label');
						label_tag.htmlFor = "genotype_" + res[i]["Genotype"];
						label_tag.setAttribute("style", "margin-right:10px;");
						label_tag.innerHTML = res[i]["Genotype"];
						div_genotype_in_accordion.appendChild(input_tag);
						div_genotype_in_accordion.appendChild(label_tag);
					}
				} else {
					alert("No data found in database!!!");
				}
			},
			error: function (xhr, status, error) {
				console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
			}
		});
	} else {
		alert("No data found in database!!!");
	}
}


function convertJsonToCsv(jsonObject) {
	let csvString = '';
	let th_keys = Object.keys(jsonObject[0]);
	for (let i = 0; i < th_keys.length; i++) {
		th_keys[i] = "\"" + th_keys[i] + "\"";
	}
	csvString += th_keys.join(',') + '\n';
	for (let i = 0; i < jsonObject.length; i++) {
		let tr_keys = Object.keys(jsonObject[i]);
		for (let j = 0; j < tr_keys.length; j++) {
			csvString += ((jsonObject[i][tr_keys[j]] === null) || (jsonObject[i][tr_keys[j]] === undefined)) ? '\"\"' : "\"" + jsonObject[i][tr_keys[j]] + "\"";
			if (j < (tr_keys.length - 1)) {
				csvString += ',';
			}
		}
		csvString += '\n';
	}
	return csvString;
}


function createAndDownloadCsvFile(csvString, filename) {
	let dataStr = "data:text/csv;charset=utf-8," + encodeURI(csvString);
	let downloadAnchorNode = document.createElement('a');
	downloadAnchorNode.setAttribute("href", dataStr);
	downloadAnchorNode.setAttribute("download", filename + ".csv");
	document.body.appendChild(downloadAnchorNode); // required for firefox
	downloadAnchorNode.click();
	downloadAnchorNode.remove();
}


function uncheck_all_genotype() {
	let ids = document.querySelectorAll('input[id^=genotype]');

	for (let i = 0; i < ids.length; i++) {
		if (ids[i].checked) {
			ids[i].checked = false;
		}
	}
}


function check_all_genotype() {
	let ids = document.querySelectorAll('input[id^=genotype]');

	for (let i = 0; i < ids.length; i++) {
		if (!ids[i].checked) {
			ids[i].checked = true;
		}
	}
}


function uncheck_all_phenotypes() {
	let ids = document.querySelectorAll('input[id^=chemical_descriptor_],input[id^=disease_descriptor_],input[id^=growth_descriptor_],input[id^=insect_descriptor_],input[id^=morphology_descriptor_],input[id^=other_descriptor_],input[id^=nematode_descriptor_],input[id^=phenology_descriptor_],input[id^=qualifier_],input[id^=stress_descriptor_]');

	for (let i = 0; i < ids.length; i++) {
		if (ids[i].checked) {
			ids[i].checked = false;
		}
	}
}


function check_all_phenotypes() {
	let ids = document.querySelectorAll('input[id^=chemical_descriptor_],input[id^=disease_descriptor_],input[id^=growth_descriptor_],input[id^=insect_descriptor_],input[id^=morphology_descriptor_],input[id^=other_descriptor_],input[id^=nematode_descriptor_],input[id^=phenology_descriptor_],input[id^=qualifier_],input[id^=stress_descriptor_]');

	for (let i = 0; i < ids.length; i++) {
		if (!ids[i].checked) {
			ids[i].checked = true;
		}
	}
}


function constructInfoTable(res, dataset, chromosome, position, gene, genotype_array) {

	// Create table
	let detail_table = document.createElement("table");
	detail_table.setAttribute("style", "text-align:center; border:3px solid #000;");
	let detail_header_tr = document.createElement("tr");

	let header_array = Object.keys(res[0]);
	for (let i = 0; i < header_array.length; i++) {

		if (header_array[i] == "Chromosome" || header_array[i] == "Position" || header_array[i] == "Accession" || header_array[i] == "SoyKB_Accession" || header_array[i] == "GRIN_Accession" || header_array[i] == "Genotype" || header_array[i] == "Functional_Effect" || header_array[i] == "Category" || header_array[i] == "Imputation" || header_array[i] == "Improvement_Status" || header_array[i] == "Classification") {
			var detail_th = document.createElement("th");
			detail_th.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
			detail_th.innerHTML = header_array[i];
			detail_header_tr.appendChild(detail_th);
		} else {
			// Create clickable links in the header
			var detail_th = document.createElement("th");
			detail_th.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
			var detail_a = document.createElement('a');
			detail_a.target = "_blank";
			detail_a.href = "/SoybeanAlleleCatalogTool/viewVariantAndPhenotypeFigures.php?dataset_1=" + dataset + "&chromosome_1=" + chromosome + "&position_1=" + position + "&gene_1=" + gene + "&phenotype_1=" + header_array[i] + "&genotype_1=" + genotype_array.join("%0D%0A");
			detail_a.innerHTML = header_array[i];
			detail_th.appendChild(detail_a);
			detail_header_tr.appendChild(detail_th);
		}

	}

	detail_table.appendChild(detail_header_tr);

	for (let i = 0; i < res.length; i++) {
		var detail_tr = document.createElement("tr");
		detail_tr.style.backgroundColor = ((i % 2) ? "#FFFFFF" : "#DDFFDD");
		for (let j = 0; j < header_array.length; j++) {
			var detail_td = document.createElement("td");
			detail_td.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
			detail_td.innerHTML = res[i][header_array[j]];
			detail_tr.appendChild(detail_td);
		}
		detail_table.appendChild(detail_tr);
	}

	return detail_table;
}


function queryVariantAndPhenotype() {

	// Clear data appended to the div tags, if there is any
	if (document.getElementById('Variant_and_Phenotye_detail_table').innerHTML) {
		document.getElementById('Variant_and_Phenotye_detail_table').innerHTML = null;
	}

	let chromosome_1 = document.getElementById('chromosome_1').value;
	let position_1 = document.getElementById('position_1').value;
	let gene_1 = document.getElementById('gene_1').value;

	let genotype_ids = document.querySelectorAll('input[id^=genotype]');
	let genotype_array = [];

	let phenotype_ids = document.querySelectorAll('input[id^=chemical_descriptor_],input[id^=disease_descriptor_],input[id^=growth_descriptor_],input[id^=insect_descriptor_],input[id^=morphology_descriptor_],input[id^=other_descriptor_],input[id^=nematode_descriptor_],input[id^=phenology_descriptor_],input[id^=qualifier_],input[id^=stress_descriptor_]');
	let phenotype_array = [];

	for (let i = 0; i < genotype_ids.length; i++) {
		if (genotype_ids[i].checked) {
			genotype_array.push(genotype_ids[i].value);
		}
	}

	for (let i = 0; i < phenotype_ids.length; i++) {
		if (phenotype_ids[i].checked) {
			phenotype_array.push(phenotype_ids[i].value);
		}
	}

	if (chromosome_1 && position_1 && genotype_array.length > 0) {
		$.ajax({
			url: './php/queryVariantAndPhenotype.php',
			type: 'GET',
			contentType: 'application/json',
			data: {
				Chromosome: chromosome_1,
				Position: position_1,
				Gene: gene_1,
				Genotype: genotype_array,
				Phenotype: phenotype_array,
				Dataset: dataset
			},
			success: function (response) {
				res = JSON.parse(response);
				res = res.data;

				if (res.length > 0) {
					document.getElementById('Variant_and_Phenotye_detail_table').appendChild(
						constructInfoTable(res, dataset, chromosome_1, position_1, gene_1, genotype_array)
					);
					document.getElementById('Variant_and_Phenotye_detail_table').style.overflow = 'scroll';
				} else {
					let error_message = document.createElement("p");
					error_message.innerHTML = "Please select genotype to view data!!!";
					document.getElementById('Variant_and_Phenotye_detail_table').appendChild(error_message);
					document.getElementById('Variant_and_Phenotye_detail_table').style.overflow = 'visible';
				}

			},
			error: function (xhr, status, error) {
				console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
			}
		});
	} else {
		let error_message = document.createElement("p");
		error_message.innerHTML = "Please select genotype to view data!!!";
		document.getElementById('Variant_and_Phenotye_detail_table').appendChild(error_message);
		document.getElementById('Variant_and_Phenotye_detail_table').style.overflow = 'visible';
	}

}


function downloadVariantAndPhenotype() {

	let chromosome_1 = document.getElementById('chromosome_1').value;
	let position_1 = document.getElementById('position_1').value;
	let gene_1 = document.getElementById('gene_1').value;

	let genotype_ids = document.querySelectorAll('input[id^=genotype]');
	let genotype_array = [];

	let phenotype_ids = document.querySelectorAll('input[id^=chemical_descriptor_],input[id^=disease_descriptor_],input[id^=growth_descriptor_],input[id^=insect_descriptor_],input[id^=morphology_descriptor_],input[id^=other_descriptor_],input[id^=nematode_descriptor_],input[id^=phenology_descriptor_],input[id^=qualifier_],input[id^=stress_descriptor_]');
	let phenotype_array = [];

	for (let i = 0; i < genotype_ids.length; i++) {
		if (genotype_ids[i].checked) {
			genotype_array.push(genotype_ids[i].value);
		}
	}

	for (let i = 0; i < phenotype_ids.length; i++) {
		if (phenotype_ids[i].checked) {
			phenotype_array.push(phenotype_ids[i].value);
		}
	}

	if (chromosome_1 && position_1 && genotype_array.length > 0) {
		$.ajax({
			url: './php/queryVariantAndPhenotype.php',
			type: 'GET',
			contentType: 'application/json',
			data: {
				Chromosome: chromosome_1,
				Position: position_1,
				Gene: gene_1,
				Genotype: genotype_array,
				Phenotype: phenotype_array,
				Dataset: dataset
			},
			success: function (response) {
				res = JSON.parse(response);
				res = res.data;

				if (res.length > 0) {
					let csvString = convertJsonToCsv(res);
					createAndDownloadCsvFile(csvString, String(chromosome_1) + "_" + String(position_1) + "_data");
				} else {
					alert("Please select genotype to download data!!!");
				}
			},
			error: function (xhr, status, error) {
				console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
			}
		});
	} else {
		alert("Please select genotype to download data!!!");
	}
}