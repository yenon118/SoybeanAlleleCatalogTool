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


function updateSearchByGeneIDs(event) {
	var dataset = event.target.value;

	if (dataset) {
		$.ajax({
			url: './php/updateSearchByGeneIDs.php',
			type: 'GET',
			contentType: 'application/json',
			data: {
				Dataset: dataset
			},
			success: function (response) {
				res = JSON.parse(response);
				res = res.data;

				if (res.hasOwnProperty('Gene')) {
					if (res['Gene'].length > 0) {
						document.getElementById('gene_examples_1').innerHTML = "";
						var gene_examples_1_str = "(eg ";
						for (let i = 0; i < res['Gene'].length; i++) {
							gene_examples_1_str += res['Gene'][i]['Gene'] + " ";
						}
						gene_examples_1_str += ")";
						document.getElementById('gene_examples_1').innerHTML = gene_examples_1_str;

						document.getElementById('gene_1').placeholder = "";
						var gene_1_str = "\nPlease separate each gene into a new line.\n\nExample:\n";
						for (let i = 0; i < res['Gene'].length; i++) {
							gene_1_str += res['Gene'][i]['Gene'] + "\n";
						}
						document.getElementById('gene_1').placeholder = gene_1_str;
					}
				}

				if (res.hasOwnProperty('Improvement_Status')) {
					document.getElementById('improvement_status_div_1').innerHTML = "";
					if (res['Improvement_Status'].length > 0) {
						if (res['Improvement_Status'][0]['Key'] != null) {

							// If there are improvement status, then add the "Improvement_Status" label
							var label = document.createElement("label");
							label.innerHTML = res['Key_Column'];
							label.style.fontWeight = "bold";
							document.getElementById('improvement_status_div_1').appendChild(label);

							document.getElementById('improvement_status_div_1').appendChild(document.createElement("br"));

							// Choose what improvement status to include for showing in the web page
							let improvement_status_array = [];
							for (let i = 0; i < res['Improvement_Status'].length; i++) {
								if (res['Improvement_Status'][i]['Key'] != null) {
									if (res['Improvement_Status'][i]['Key'] == "G. soja") {
										improvement_status_array.push("Soja");
									} else if (res['Improvement_Status'][i]['Key'] == "Genetic") {
										continue;
									} else {
										improvement_status_array.push(res['Improvement_Status'][i]['Key']);
									}
								}
							}
							improvement_status_array.push("Cultivar");

							for (let i = 0; i < improvement_status_array.length; i++) {
								var input_box = document.createElement("input");
								input_box.type = "checkbox";
								input_box.id = improvement_status_array[i];
								input_box.name = "improvement_status_1[]";
								input_box.value = improvement_status_array[i];
								input_box.style.marginRight = "5px";
								input_box.checked = true;

								document.getElementById('improvement_status_div_1').appendChild(input_box);

								var label = document.createElement("label");
								label.innerHTML = improvement_status_array[i];
								label.style.fontWeight = "normal";
								label.style.marginRight = "10px";
								document.getElementById('improvement_status_div_1').appendChild(label);

								if (i != 0 && i % 4 == 0) {
									document.getElementById('improvement_status_div_1').appendChild(document.createElement("br"));
								}
							}
						}
					}
				}
			},
			error: function (xhr, status, error) {
				console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
				alert("Unable to fetch data to update the Search by Gene IDs!!!");
			}
		});
	} else {
		alert("Unable to fetch data to update the Search by Gene IDs!!!");
	}
}


function updateSearchByAccessionsandGeneID(event) {
	var dataset = event.target.value;

	if (dataset) {
		$.ajax({
			url: './php/updateSearchByAccessionsandGeneID.php',
			type: 'GET',
			contentType: 'application/json',
			data: {
				Dataset: dataset
			},
			success: function (response) {
				res = JSON.parse(response);
				res = res.data;

				if (res.hasOwnProperty('Accession')) {
					if (res['Accession'].length > 0) {
						document.getElementById('accession_examples_2').innerHTML = "";
						var accession_examples_2_str = "(eg ";
						for (let i = 0; i < res['Accession'].length; i++) {
							accession_examples_2_str += res['Accession'][i]['Accession'] + " ";
						}
						accession_examples_2_str += ")";
						document.getElementById('accession_examples_2').innerHTML = accession_examples_2_str;

						document.getElementById('accession_2').placeholder = "";
						var accession_2_str = "\nPlease separate each accession into a new line.\n\nExample:\n";
						for (let i = 0; i < res['Accession'].length; i++) {
							accession_2_str += res['Accession'][i]['Accession'] + "\n";
						}
						document.getElementById('accession_2').placeholder = accession_2_str;
					}

					if (res.hasOwnProperty('Gene')) {
						if (res['Gene'].length > 0) {
							document.getElementById('gene_example_2').innerHTML = "(One gene ID only; eg " + res['Gene'][0]['Gene'] + ")";
						}
					}
				}

			},
			error: function (xhr, status, error) {
				console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
				alert("Unable to fetch data to update the Search by Accessions and Gene ID!!!");
			}
		});
	} else {
		alert("Unable to fetch data to update the Search by Accessions and Gene ID!!!");
	}
}


function queryAccessionInformation() {
	var dataset = "Soy1066";

	if (dataset) {
		$.ajax({
			url: './php/queryAccessionInformation.php',
			type: 'GET',
			contentType: 'application/json',
			data: {
				Dataset: dataset
			},
			success: function (response) {
				res = JSON.parse(response);
				res = res.data;

				if (res.length > 0) {
					let csvString = convertJsonToCsv(res);
					createAndDownloadCsvFile(csvString, String(dataset) + "_Accession_Information");

				} else {
					alert("Accession information of the " + dataset + " dataset is not available!!!");
				}

			},
			error: function (xhr, status, error) {
				console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
				alert("Accession information of the " + dataset + " dataset is not available!!!");
			}
		});
	} else {
		alert("Accession information of the " + dataset + " dataset is not available!!!");
	}
}
