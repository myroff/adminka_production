<?php
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/BLogic/Kunde/CommentToolsHtml.php';
use Kunde\CommentToolsHtml as CmntTlsHtml;
require_once BASIS_DIR.'/Tools/TmplTools.php';
use Tools\TmplTools as TmplTls;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>SWIFF. Kunde bezahlen by id.</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

        <link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />

        <script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

        <script src="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="<?=BASIS_URL?>/Public/jquery-ui/jquery-ui.min.css">

        <script src="<?=BASIS_URL?>/Public/js/zebra_datepicker.js"></script>
        <link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/zebra_default.css">

        <style>
            #BezahlungsFormular, #editRnKomm_Table
            {
                display:none;
                border:3px solid #BBB;
                padding:10px;
                background:#EEE;
                width:600px;
                border-radius:10px;

                position:fixed;
                top:100px;
                left:15%;
                z-index:100;
            }
            .zebra_datepicker_my{width:50px;}

            table th, table td{border-right:1px solid black;} table th:last-child, td:last-child{border-right:none;}
            #Bezahlung_Form_Table th, td{border:none;}

            #QuitPanel
            {
                display:none;
                border:3px solid #BBB;
                padding:10px;
                background:#EEE;
                width:600px;
                border-radius:10px;

                position:fixed;
                top:80px;
                left:30%;
                z-index:200;
            }
            #RechnungBlock{width:800px;border:1px solid black;overflow:hidden;float:left;}
            .bzButton{width:100px;height:50px;}
            #rnUbersicht{float:left;width:400px;}
            .rechnung{border-top:1px solid black;cursor:pointer;clear:both;margin-top:30px;}
            #rnQuittung{float:left;width:360px;border:1px solid black;padding:5px;margin-left:20px;}
        </style>
    </head>
    <body>
        <div id="horizontalMenu">
            <?php

            TemplateTools\Menu::adminMenu();
            ?>
        </div>
        <!-- START OF CONTENT -->
        <div id="mainContent">
            <div id="meldung">
                <?php
                if(isset($meldung))
                {
                    echo $meldung;
                }
                ?>
            </div>

            <div id="privateDates">
                <table>
                    <tr>
                        <th>
                            Kunden-Nummer
                        </th>
                        <!--<th>
                            Eltern
                        </th>-->
                        <th class="itemName">
                            Anrede
                        </th>
                        <th class="itemName">
                            Vorname
                        </th>
                        <th class="itemName">
                            Name
                        </th>
                        <th class="itemName">
                            Geburtsdatum<br>
                            (dd.mm.yyyy)
                        </th>
                        <th class="itemName">
                            Telefon
                        </th>
                        <th class="itemName">
                            Handy
                        </th>
                        <th class="itemName">
                            Email
                        </th>
                        <th class="itemName">
                            Strasse
                        </th>
                        <th class="itemName">
                            Haus
                        </th>
                        <th class="itemName">
                            Stadt
                        </th>
                        <th class="itemName">
                            PLZ
                        </th>
                        <th class="itemName">
                            Empfohlen durch
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?=$res['kundenNummer']?>
                        </td>
                        <!--<td>
                             ElternInfo
                        </td>-->
                        <td class="itemValue">
                            <?=$res['anrede']?>
                        </td>
                        <td class="itemValue">
                            <?=$res['vorname']?>
                        </td>
                        <td class="itemValue">
                            <?=$res['name']?>
                        </td>
                        <td class="itemValue">
                            <?=Fltr::sqlDateToStr($res['geburtsdatum'])?>
                        </td>
                        <td class="itemValue">
                            <?=$res['telefon']?>
                        </td>
                        <td class="itemValue">
                            <?=$res['handy']?>
                        </td>
                        <td class="itemValue">
                            <?=$res['email']?>
                        </td>
                        <td class="itemValue">
                            <?=$res['strasse']?>
                        </td>
                        <td class="itemValue">
                            <?=$res['strNr']?>
                        </td>

                        <td class="itemValue">
                            <?=$res['stadt']?>
                        </td>
                        <td class="itemValue">
                            <?=$res['plz']?>
                        </td>
                        <td class="itemValue">
                            <?=$res['empfohlenDurch']?>
                        </td>
                    </tr>
                </table>
                <!--Konto-Daten-->
                <table>
                    <tr>
                        <th>
                            Zahlungsart
                        </th>
                        <th>
                            Kontoinhaber
                        </th>
                        <th>
                            Strasse
                        </th>
                        <th>
                            Haus-Nr.
                        </th>
                        <th>
                            PLZ
                        </th>
                        <th>
                            Ort
                        </th>
                        <th>
                            Bank
                        </th>
                        <th>
                            IBAN
                        </th>
                        <th>
                            BIC
                        </th>
                    </tr>
                    <tr>
                        <td <?=!$res['payment_id']? 'style="background:red;"' : ''?>>
                            <img style="max-height:45px" src="<?=BASIS_URL?>/Public/img/payment_logo/<?=$res['logo_file']?>">
                            <?=$res['payment_name']?>
                        </td>
                        <td>
                            <?=$res['kontoinhaber']?>
                        </td>
                        <td>
                            <?=$res['zdStrasse']?>
                        </td>
                        <td>
                            <?=$res['zdHausnummer']?>
                        </td>
                        <td>
                            <?=$res['zdPlz']?>
                        </td>
                        <td>
                            <?=$res['zdOrt']?>
                        </td>
                        <td>
                            <?=$res['bankName']?>
                        </td>
                        <td>
                            <?=$res['iban']?>
                        </td>
                        <td>
                            <?=$res['bic']?>
                        </td>
                    </tr>
                </table>
            </div>
            <!--Empfohlende Kunden -->
            <div style="margin:20px 0px;border:1px solid black;padding:10px;">
            <?php
                if(empty($empfRes))
                {
                    echo "Der Kunde hat noch keinen empfohlen :(";
                }
                else{
                    $str = "Der Kunde empfiehl:<br>";
                    foreach($empfRes as $r)
                    {
                        $str .= "<a href='".BASIS_URL."/admin/bezahlenById/".$r['kndId']."'>".$r['kundenNummer']." ".$r['vorname']." ".$r['name']."</a>, ";
                    }
                    $str = substr($str, 0, -2);
                    echo $str;
                }
            ?>
            </div>
            <div><button id="bezahlen">Bezahlen</button></div>
            <div id="kurListe">
                <table id="kundenResTbl">
                    <tr>
                        <td colspan="11">
                            <form method="POST">
                                <?= TmplTls::getSeasonsSelector("s_season", "s_season", $curSeason, "Schuljahr", 1); ?>
                                <button type="submit">Aktualisieren</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <input type="checkbox" id="markAlleKurse" />
                        </th>
                        <th>
                            Schuljahr
                        </th>
                        <th>
                            Unterricht
                        </th>
                        <th>
                            Preis
                        </th>
                        <th>
                            SonderPreis
                        </th>
                        <th>
                            Beschreibung
                        </th>
                        <th>
                            Kommentar
                        </th>
                        <th>
                            Alter
                        </th>
                        <th>
                            Klassen
                        </th>
                        <th>
                            Von
                        </th>
                        <th>
                            Bis
                        </th>
                    </tr>
                <?php
                if(empty($res))
                {
                ?>
                    <tr>
                        <td colspan="11">Nach Ihrer Anfrage wurden keine Daten gefunden.</td>
                    </tr>
                <?php
                }
                else
                {
                    foreach ($ures as $r)
                    {
                        $alter = $r['kurMinAlter'];
                        $alter .= $r['kurMinAlter'] < $r['kurMaxAlter'] ? " bis ".$r['kurMaxAlter'] : '';

                        $klasse = $r['kurMinKlasse'];
                        $klasse .= $r['kurMinKlasse'] < $r['kurMaxKlasse'] ? " bis ".$r['kurMaxKlasse'] : "";

                        $vonVal = Fltr::sqlDateToStr($r['von']);
                        $bisVal = Fltr::sqlDateToStr($r['bis']);

                        $today = strtotime('today');;
                        $endDate = strtotime($r['bis']);

                        if($today < $endDate)
                        {
                            echo "<tr>";
                            echo "<td><input type='checkbox' class='markedKurs' value='".$r['eintrId']."' /></td>";
                        }
                        else
                        {
                            echo "<tr style='background:yellow;'>";
/*Nachzahlen button*/		echo "<td><button class='nachzahlen' eintrId='".$r['eintrId']."' >Nachzahlen</button></td>";
                        }

                        echo "<td>".$r['season_name']."</td>";

                        echo "<td><i>".$r['vorname']." ".$r['name']."</i><br>";
                        echo $r['kurName']."<br>";
                        echo Fltr::printSqlTermin($r['termin']);
                        echo "</td>";

                        echo "<td>".$r['kurPreis'];
                        echo $r['kurIsStdPreis'] > 0 ? ' pro Stunde' : ' pro Monat';
                        echo "</td>";
                        echo "<td>";
                        if($r['sonderPreis'])
                        {
                            echo $r['sonderPreis'];
                            echo $r['khkIsStdPreis'] > 0 ? ' pro Stunde' : ' pro Monat';
                        }
                        echo "</td>";
                        echo "<td>".$r['kurBeschreibung']."</td>";
                        echo "<td>".$r['khkKomm']."</td>";
                        echo "<td>".$alter."</td>";
                        echo "<td>".$klasse."</td>";
                        echo "<td>". $vonVal."</td>";
                        echo "<td>".$bisVal."</td>";

                        echo "</tr>";
                    }
                }
                ?>
                </table>
            </div><!-- KurListe -->

            <div id="RechnungBlock">
                <h3>Rechnungen</h3>
                <div id="rnUbersicht">
                <?php
                foreach($rres as $r)
                {
                ?>
                    <div class="rechnung" rnNr="<?=$r['rnId']?>">
                        <button class="deleteButton deleteRn" style="float:right;" rnId="<?=$r['rnId']?>" ></button>
                        <p>für Monat: <?=  Fltr::sqlDateToMonatYear($r['rnMonat'])?>:  <?=$r['summe']?>€</p>
                        <p>Kurse: <?=$r['kurse']?></p>
                        <p><button class="editItemButton editRnKomm" rnId="<?=$r['rnId']?>" ></button>Kommentar:<br>
                            <?=$r['rnKomm']?>
                        </p>
                        <?php
                            if( !empty($r['rnPdfUrl']) ){
                                echo "<p><a href='".BASIS_RECHNUNG_URL.$r['rnPdfUrl']."' target='_blank' >PDF</a></p>";
                            }
                        ?>
                    </div>
                <?php
                }
                ?>
                </div>
                <div id="rnQuittung"></div>
            </div>
        <!-- Kommentaren -->
            <div id="Kommentaren">
                <p><b>Kommentaren</b> <button id="newKndCmntTable_Open">Neuen Kommentar hinzufügen</button></p>
                <div id='newKndCmntTable'>
                    <?php echo CmntTlsHtml::newCommentsForm($res['kndId']);?>
                    <div>
                        <button id="newKndCmntTable_Close">Schliessen</button>
                    </div>
                </div>
                <div>
                    <?php echo CmntTlsHtml::showComments($res['kndId']);?>
                </div>
            </div>
        </div><!--main content -->
        <!-- END OF CONTENT -->
        <div id="BezahlungsFormular" >
            <form id="Bezahlung_Form" method="POST" action="<?=BASIS_URL?>/admin/ajaxSaveRechnung" target="BezahlungsWindow">
                <fieldset>
                    <legend>Bezahlen</legend>
                    <input type='hidden' name='kndId' value='<?=$res['kndId']?>' />
                    <table id="Bezahlung_Form_Table">
                        <tr>
                            <th>Monat</th><td><input type="text" id="Bezahlung_Form_bzMonat" name="rnMonat" class="zebra_datepicker_my"/></td>
                        </tr>
                        <tr>
                            <td colspan="2" ><div id="Bezahlung_Form_Kurse"></div></td>
                        </tr>
                        <tr>
                            <th>Kommentar</th><td><textarea name="rnKomm"></textarea></td>
                        </tr>
                    </table>
                    <div style="margin-top:10px;">
                        <input type='submit' value='OK' class="bzButton">
                        <button id='Bezahlung_Form_Abbrechen' class="bzButton" >Abbrechen</button>
                    </div>
                </fieldset>
            </form>
        </div>
        <div id="QuitPanel">
            <div id="QuittungHolder" style="float:left;width:350px;"></div>
            <div id="QuitBut" style="float:left;margin-top:50px;margin-left:20px;">
                <button id='Quit_Speichern' class="bzButton" >Speichern</button>
                <button id='Quit_Abbrechen' class="bzButton" >Abbrechen</button>
            </div>
        </div>
    <!-- Rechnungskommentar bearbeiten -->
        <div id="editRnKomm_Table">
            <div id="editRnKomm_Titel"><b>Kommentar</b></div>
            <form id="editRnKomm_Form" method="post" action="<?=$_SERVER['REQUEST_URI']?>">
                <input type="hidden" id="editRnKomm_rnId" name="rnId"/>
                <div><textarea id="editRnKomm_Form_Textarea" name="rnKomm" cols="70" ></textarea></div>
                <div>
                    <input id="editRnKomm_Form_ButtonSpeichern" type='submit' value='Speichern' />
                    <button id="editRnKomm_Form_ButtonAbbrechen" >Abbrechen</button>
                </div>
            </form>
        </div>
<!-- JavaScript -->
<script>
    meldung = $('#meldung');
    kndId = <?=$res['kndId']?>;
    bzForm = $('#Bezahlung_Form');
    quittung = $('#QuittungHolder');

//Monatspicker
    $(".zebra_datepicker_my").Zebra_DatePicker({
        format: 'm.Y',		//  note that becase there's no day in the format
        offset:	[0,200],	//  users will not be able to select a day!
    });

    $('#markAlleKurse').click(function(){
        check = $('#markAlleKurse').is(':checked');
        $('.markedKurs').prop('checked', check);
    });

//BezahlenButton
    $('#bezahlen').click(function(){
        t = [];

        $('.markedKurs:checked').each(function(){ t.push($(this).val()); });

        if(t.length < 1)
        {
            alert("Sie haben keine Kurse ausgewählt.");
            return;
        }

        $.ajax({
            url:'<?=BASIS_URL?>/admin/ajaxGetBezahlungsformular',
            type:'POST',
            data:{kndId:kndId,eIds:t},
            dataType:'JSON',
            success:function(response){
                if(response.status == 'ok')
                {
                    $('#Bezahlung_Form_Kurse').html(response.kurseTable);
                    $('#BezahlungsFormular').slideDown(1000);
                }
                else
                {
                    alert(response.formular);
                }
                meldung.html(response.info);
            },
            error:function(errorThrown){
                meldung.html(errorThrown);
            }
        });
    });
//NachzahlenButton
    $('.nachzahlen').click(function(){
        var eintrId = Array( $(this).attr('eintrId') );

        $.ajax({
            url:'<?=BASIS_URL?>/admin/ajaxGetBezahlungsformular',
            type:'POST',
            data:{kndId:kndId,eIds:eintrId},
            dataType:'JSON',
            success:function(response){
                if(response.status == 'ok')
                {
                    $('#Bezahlung_Form_Kurse').html(response.kurseTable);
                    $('#BezahlungsFormular').slideDown(1000);
                }
                else
                {
                    alert(response.formular);
                }
                meldung.html(response.info);
            },
            error:function(errorThrown){
                meldung.html(errorThrown);
            }
        });
    });

    $('#Bezahlung_Form_Abbrechen').click(function(e){
        e.preventDefault();
        $('#BezahlungsFormular').slideUp(1000);
    });

//Rechnungsdaten bestätigen
    $('#Bezahlung_Form_Abbrechen').click(function(e){
        e.preventDefault();
    });

    $('#Bezahlung_Form').submit(function(e){
        e.preventDefault();

        postData = $(this).serializeArray();

        $.ajax({
            url:'<?=BASIS_URL?>/admin/ajaxConfirmRechnung',
            type:'POST',
            data:postData,
            dataType:'TEXT',
            success:function(response){
                quittung.html(response);
                $('#QuitPanel').slideDown(500);
            },
            error:function(errorThrown){
                meldung.html(errorThrown);
            }
        });

    });

//Rechnungsdaten speichern
    $('#Quit_Speichern').click(function(){
        postData = bzForm.serializeArray();
        meldung.text("Quit_Speichern is clicked.");
        $.ajax({
            url:'<?=BASIS_URL?>/admin/ajaxSaveRechnung',
            type:'POST',
            data:postData,
            dataType:'JSON',
            success:function(response){
                if(response.status == 'ok')
                {
                    window.open('<?=BASIS_RECHNUNG_URL?>'+response.pdfUrl, '_blank');
                    alert(response.info+'\n'+response.pdfUrl);
                    window.location.reload(true);
                }
                else
                {
                    alert(response.status+'\n'+response.info);
                }
            },
            error:function(errorThrown){
                meldung.html(errorThrown);
            }
        });
    });

    $('#Quit_Abbrechen').click(function(){
        $('#QuitPanel').slideUp(500);
    });

//get Quittung
    $('.rechnung').click(function(){
        rnNr = $(this).attr('rnNr');

        $.ajax({
            url:'<?=BASIS_URL?>/admin/ajaxShowRechnung',
            type:'POST',
            data:{rnId:rnNr},
            dataType:'HTML',
            success:function(response){
                $('#rnQuittung').html(response);
            },
            error:function(errorThrown){
                meldung.html(errorThrown);
            }
        });
    });

//Rechnungkommentar bearbeiten
    $('.editRnKomm').click(function(){
        rnId = $(this).attr('rnId');
        $('#editRnKomm_rnId').val(rnId);
        $('#editRnKomm_Table').slideDown(500);
    });

    $('#editRnKomm_Form_ButtonAbbrechen').click(function(e){
        e.preventDefault();
        $('#editRnKomm_Table').slideUp(500);
    });

    $('#editRnKomm_Form').submit(function(e){
        e.preventDefault();

        postData = $(this).serializeArray();

        $.ajax({
            url:'<?=BASIS_URL?>/admin/ajaxEditRechnungsKommentar',
            type:'POST',
            data:postData,
            dataType:'JSON',
            success:function(response){
                if(response.status == 'ok')
                {
                    alert(response.info);
                    window.location.reload(true);
                }
                else
                {
                    alert(response.status+'\n'+response.info);
                }
            },
            error:function(errorThrown){
                meldung.html(errorThrown);
            }
        });

    });

    $('.deleteRn').click(function(){
        rnId = $(this).attr('rnId').trim();

        if(confirm("Wollen Sie wirklich diese Rechnung Löschen?? :-0") == true)
        {
            $.ajax({
                url:'<?=BASIS_URL?>/admin/ajaxDeleteRechnung',
                type:'POST',
                data:{rnId:rnId},
                dataType:'JSON',
                success:function(response){
                    if(response.status == 'ok')
                    {
                        alert(response.status);
                        window.location.reload(true);
                    }
                    else
                    {
                        alert(response.status);
                    }
                    meldung.html(response.info);//response.dataPost
                },
                error:function(errorThrown){
                    meldung.html(errorThrown);
                }
            });
        }
        else{
        }
    });
    //Kommentaren newKndCmntTable
        $('#newKndCmntTable_Open').click(function(){
            $('#newKndCmntTable').slideToggle(1000);
        });
        $('#newKndCmntTable_Close').click(function(){
            $('#newKndCmntTable').slideUp(1000);
        });
    //KommentarFunktionen
<?php echo CmntTlsHtml::newCommentsJsFnct($res['kndId']);?>
</script>
    </body>
</html>