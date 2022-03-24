<?php
$TITLE = "Soybean Allele Catalog Tool";
include '../header.php';
?>

<div>
    <table width="100%" cellspacing="14" cellpadding="14">
        <tr>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllByGenes.php" method="get" target="_blank">
                    <h2>Search By Gene Name</h2>
                    <br />
                    <label for="dataset1"><b>Dataset:</b></label>
                    <select name="dataset1" id="dataset1">
                        <option value="Soy775_Allele_Catalog">Soy775 Allele Catalog</option>
                        <option value="Soy1066_Allele_Catalog" selected>Soy1066 Allele Catalog</option>
                    </select>
                    <br />
                    <br />
                    <b>Gene name</b><span>&nbsp;(eg Glyma.01G049100 Glyma.01G049200 Glyma.01G049300)</span>
                    <br />
                    <textarea id="gene1" name="gene1" rows="12" cols="50" placeholder="&#10;Please separate each gene into a new line. &#10;&#10;Example:&#10;Glyma.01g049100&#10;Glyma.01g049200&#10;Glyma.01g049300"></textarea>
                    <br />
                    <br />
                    <table>
                        <tr>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Soja" name="Soja" value="Soja" checked>
                                <label for="Soja">Soja</label>
                            </td>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Elite" name="Elite" value="Elite" checked>
                                <label for="Elite">Elite</label>
                            </td>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Landrace" name="Landrace" value="Landrace" checked>
                                <label for="Landrace">Landrace</label>
                            </td>
                        </tr>
                        <tr>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Cultivar" name="Cultivar" value="Cultivar" checked>
                                <label for="Cultivar">Cultivar</label>
                            </td>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Imputed" name="Imputed" value="Imputed" checked>
                                <label for="Imputed">Imputed</label>
                            </td>
                            <td style="min-width:100px">
                                <input type="checkbox" id="Unimputed" name="Unimputed" value="Unimputed" checked>
                                <label for="Unimputed">Unimputed</label>
                            </td>
                        </tr>
                    </table>
                    <br />
                    <br /><br />
                    <input type="submit" value="Search">
                </form>
            </td>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllByAccessionAndGene.php" method="get" target="_blank">
                    <h2>Search By Accession and Gene Name</h2>
                    <br />
                    <label for="dataset2"><b>Dataset:</b></label>
                    <select name="dataset2" id="dataset2">
                        <option value="Soy775_Allele_Catalog">Soy775 Allele Catalog</option>
                        <option value="Soy1066_Allele_Catalog" selected>Soy1066 Allele Catalog</option>
                    </select>
                    <br />
                    <br />
                    <b>Accession</b><span>&nbsp;(eg HN052_PI424079 PI_479752)</span>
                    <br />
                    <textarea id="accession" name="accession" rows="12" cols="50" placeholder="&#10;Please separate each accession into a new line. &#10;&#10;Example:&#10;HN052_PI424079&#10;PI_479752"></textarea>
                    <br /><br />
                    <b>Gene name</b><span>&nbsp;(One gene name only; eg Glyma.01G049100)</span>
                    <br />
                    <input type="text" id="gene2" name="gene2" size="53"></input>
                    <br /><br />
                    <br /><br />
                    <input type="submit" value="Search">
                </form>
            </td>
        </tr>
        <tr>
        </tr>
    </table>
    <br />
    <br />
</div>

<div style='margin-top:10px;' align='center'>
    <button type="submit" onclick="window.open('https://data.cyverse.org/dav-anon/iplant/home/soykb/Soy1066/Accession_Info.csv')" style="margin-right:20px;">Download Accession Information</button>
</div>

<script type="text/javascript" language="javascript">
</script>

<?php include '../footer.php'; ?>