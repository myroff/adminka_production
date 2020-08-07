<?php
namespace Rechnung;
use PDO as PDO;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;
require_once BASIS_DIR.'/BLogic/mpdf/mpdf.php';
use mPDF as mPdf;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;

class Rechnung2Pdf {
	private static $rechnungDir = BASIS_DIR."/Public/Rechnungen";
	private static $rechnungUrl = BASIS_URL."/Public/Rechnungen";
	//user folder: name.vorname.kndNummer
	public static function saveRechnungToPdf($rnNr){
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
//from Rechung->ajaxShowRechnung()
		$qrn = "SELECT r.*, k.kundenNummer, k.anrede, k.vorname, k.name, k.strasse, k.strNr, k.plz, k.stadt"
			." FROM rechnungen as r LEFT JOIN kunden as k USING(kndId)"
			." WHERE r.rnId=:rnId";
		$qrd = "SELECT season.season_name, r.*, k.* FROM rechnungsdaten as r LEFT JOIN kurse as k USING(kurId) LEFT JOIN seasons as season USING(season_id) WHERE rnId=:rnId";
		$qm = "SELECT anrede, vorname, name FROM mitarbeiter WHERE mtId=:mtId";
		
		$rn =array();
		$rd =array();
		$mt = array();
		
		try
		{
			$sth = $dbh->prepare($qrn);
			$sth->execute(array(':rnId' => $rnNr));
			$rn = $sth->fetch(PDO::FETCH_ASSOC,1);
			
			$sth = $dbh->prepare($qrd);
			$sth->execute(array(':rnId' => $rnNr));
			$rd = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			$sth = $dbh->prepare($qm);
			$sth->execute(array(':mtId' => $rn['mtId']));
			$mt = $sth->fetch(PDO::FETCH_ASSOC, 1);
			
		} catch (Exception $ex){
			return false;
		}
//create folder for Rechnung
		$pdfPath = self::$rechnungDir."/".$rn['name'].".".$rn['vorname'].".".$rn['kundenNummer']."/";
		$relativePath = "/".$rn['name'].".".$rn['vorname'].".".$rn['kundenNummer']."/";
		
		if(!file_exists($pdfPath)){
			if( !mkdir($pdfPath, 0777, true) )
				//echo "Ordner konnte nicht erstellt werden.".$pdfPath;
				return false;
		}
		
//fileName: name.vorname.kundenNummer.[rechnungNummer][rechnungsMonat][erstellungsDatum]
		$fileName = $rn['name'].".".$rn['vorname'].".".$rn['kundenNummer']
				."[".$rn['rnId']."][".Fltr::sqlDateToMonatYear($rn['rnMonat'])."][".date("d.m.Y[H_i_s]", strtotime($rn['rnErstelltAm']) )."].pdf";
		$PathFile = $pdfPath.$fileName;
		$relativePath .= $fileName;
		
		ob_start();
		include_once BASIS_DIR .'/Templates/Formulare/Rechnung2Pdf.01.tmpl.php';
		$html = ob_get_contents();
		ob_end_clean();
		
/*
string Output ([ string $filename , string $dest ])
I: send the file inline to the browser. The plug-in is used if available. The name given by filename is used when one selects the "Save as" option on the link generating the PDF.
D: send to the browser and force a file download with the name given by filename.
F: save to a local file with the name given by filename (may include a path).
S: return the document as a string. filename is ignored.
Note: You can use the 'S' option to e-mail a PDF file
*/
		$mpdf = new mPDF('utf-8', 'A4','','' , 0 , 0 , 0 , 0 , 0 , 0);
		//$mpdf->debug = true;
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->list_indent_first_level = 0;
		$mpdf->WriteHTML($html);
		$mpdf->Output($PathFile, 'F');
		
		if(file_exists($PathFile)){
			try{
				$sth = $dbh->prepare("UPDATE rechnungen SET rnPdfUrl=:rnPdfUrl WHERE rnId=:rnId");
				$sth->execute(array(':rnPdfUrl'=>$relativePath,':rnId' => $rnNr));
			} catch (Exception $ex) {

			}
			//echo $pdfUrl.$fileName;
			return $relativePath;
		}
		else{
			echo $PathFile;
			return false;
		}
		
		//echo $html;
	}
	
	public function testPdfAjax(){
	?>
<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<button id='testPdf' >Test mPDF</button>
	
		<script>
		$("#testPdf").click(function(){
			alert("online");
			/*
			$.ajax({
					url:'<?=BASIS_URL?>/admin/ajaxSaveRechnung2Pdf',
					type:'POST',
					data:postData,
					dataType:'JSON',
					success:function(response){
						if(typeof response.fehler === 'undefined')
						{
							var url = result['url'];
							window.location = url;
						}
						else
						{
							alert(response.fehler);
						}
					},
					error:function(errorThrown){
						meldung.html(errorThrown);
					}
				});
			var url = result['url'];
			window.location = url;
			 */
		});
		</script>
	<?php
		return;
	}
}
