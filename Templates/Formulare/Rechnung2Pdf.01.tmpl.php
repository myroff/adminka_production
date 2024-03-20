<!DOCTYPE html>
<html>
    <head>
        <style>
            body{width:100%;height:100%;
            /*font-family:Arial;*/
            font-size:10pt;
            margin:0;
            padding:0;}
            .page{
                width: 21cm;
                height: 29.7cm;
                display: block;
                margin: 0 auto;
            }
            .headerFirma{text-align:center;padding-top:10mm;}
            .clearSpace{clear:both;}
            hr{clear:both;width:100%;height:1px;background-color: grey;border:none;margin:20px 0px;}
            .textField {
                width: 21cm;
                min-height: 29.7cm;
                padding: 2cm;
                margin: 1cm auto;
                border: none;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }
            .adressFirma{text-align:center;font-size:8pt;padding-bottom:15mm;}
            .adressKunde{float:left;clear:both;font-size:10pt;}
            .uberschrift{clear:both;margin-top:20px;overflow:hidden;}
            .rTable{border-collapse:collapse;}
            .rTable tr{}
            .rTable th, .rTable td{text-align:left;padding:7px 10px;}
            .rTable th{font-weight:bold;border:1px solid black;}
            .rTable td{border:1px solid black;}
        </style>
    </head>
    <body>
    <!--mpdf <div class="page"> mpdf-->
        <!--mpdf <div class="textField"> mpdf-->
        <htmlpageheader name="header">
            <div class="headerFirma" >
                <span style="font-size:18pt;">SWIFF e.V.</span><br>
                <span style="font-size:8pt;">Sprachen und Wissen integrativ und freundlich fördern</span>
            </div>
        </htmlpageheader>
        <sethtmlpageheader name="header" value="on" show-this-page="1"/>
            <div class="clearSpace"></div>
            <br>
            <br>
            <br>
            <div class="adressKunde">
                <?=$rn['anrede']?> <?=$rn['vorname']?> <?=$rn['name']?><br>
                <?=$rn['strasse']?> <?=$rn['strNr']?><br>
                <?=$rn['plz']?> <?=$rn['stadt']?>
            </div>

            <div class="clearSpace"></div>
            <!--mpdf<div class="uberschrift">mpdf-->
                <div style="font-size:22px;margin:10px 0px;width:100%;">Quittung</div>
                <div class="clearSpace"></div>
                <div style="float:right;width:200px;">Quittung Nr.: <?=$rn['rnId']?></div>
                <div class="clearSpace"></div>
                <div style="float:right;width:200px;">Datum: <?=date("d.m.Y H:i", strtotime($rn['rnErstelltAm']) )?></div>
                <div class="clearSpace"></div>
                <div style="float:right;width:200px;">Mitarbeiter: <?php echo $mt['anrede']." ".$mt['name']?></div>
            <!--mpdf</div>mpdf-->
            <br>
            <br>
            Für den Monat <?=date("m.Y", strtotime($rn['rnMonat']) )?>
            <hr>
            <table class="rTable">
                <tr>
                    <th>
                        Schuljahr
                    </th>
                    <th>
                        Kurs
                    </th>
                    <th>
                        Betrag
                    </th>
                    <?php
                    $summe = (float) 0;
                    foreach($rd as $r)
                    {
                        $betrag = (float) $r['rndBetrag'];
                        $summe += $betrag;
                    ?>
                    <tr>
                        <td>
                            <?=$r['season_name']?>
                        </td>
                        <td>
                            <?=$r['kurName']?>
                        </td>
                        <td>
                            <?= number_format($betrag, 2, ',', ' ' ) ?> €
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td><b>Gesamtbetrag</b></td>
                        <td><b><?= number_format($summe, 2, ',', ' ' ) ?> €</b></td>
                    </tr>
                </tr>
            </table>
            <br>
            <br><br>
            <?php
            if(!empty($rn['rnKomm']))
            {
            ?>
                <br>
                <br>
                <br>
                <div class="Kommentar">
                    Bemerkung:<br>
                    <?= $rn['rnKomm']?>
                </div>
            <?php
            }
            ?>
        <htmlpagefooter name="footer">
            <div class="adressFirma" >
                SWIFF e.V. &bull; Gemeinnütziger Verein &bull; VR 2775<br>
                Verwaltung &bull; Schellbergstr. 27 &bull; 41469 Neuss<br>
                Telefon: +49 (0)2131 - 4746515<br>
                E-Mail: info@swiff-online.de &bull; Internet: www.swiff-online.de
            </div>
        </htmlpagefooter>
        <sethtmlpagefooter name="footer" value="on" />
        <!--mpdf </div> mpdf-->
    <!--mpdf </div> mpdf-->
    </body>
</html>