function getAccessionCounts(genes, dataset) {

    return new Promise((resolve, reject) => {
        $.ajax({
            url: './php/getAccessionCounts.php',
            type: 'GET',
            contentType: 'application/json',
            data: {
                Genes: genes,
                Dataset: dataset
            },
            success: function (response) {
                let res = JSON.parse(response);
                res = res.data;
                res = processAccessionCounts(res);

                resolve(res);
            },
            error: function (xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
            }
        })
    })

}