<?php
namespace Kunde;
use PDO as PDO;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;

class Empfohlen
{
	public static function setButton($name)
	{
	?>
<style>
#empfohlenMessageBox
{
	display:none;
	border:2px solid #a1a1a1;
	padding:20px; 
	background:#dddddd;
	min-width:450px;
	border-radius:20px;

	position:fixed;
	top:200px;
	left:30%;
	z-index:100;
}
#empfohlenMessageBox_message {overflow:scroll;max-height:300px;}
</style>
	<button id="getEmpfohlenListe">Wählen</button><br>
	<input name='<?=$name?>' id="empfohlenKndIdInput" type='text' disabled/>	
<script>
$("body").append("<div id='empfohlenMessageBox'>"
			+"<div id='empfohlenMessageBox_message'></div>"
			+"<div class='buttonsOkCancelDiv'>"
				+"<button class='cancel' id='closeEmpfohlenMessageBox'></button>"
			+"</div>"
		+"</div>");

$('#closeEmpfohlenMessageBox').click(function(e){
	e.preventDefault();
	$('#empfohlenMessageBox').slideUp();
	$('#empfohlenMessageBox_message').html("");
});

$('#getEmpfohlenListe').click(function(e){
	e.preventDefault();
	$.ajax({
		url:'<?=BASIS_URL?>/admin/ajaxGetEmpfohlenGadget',
		//type:'POST',
		//data:postData,
		dataType:'HTML',
		success:function(response){
			$("#empfohlenMessageBox_message").html(response);
		},
		error:function(errorThrown){
		meldung.html(errorThrown);
		}
	});

	$('#empfohlenMessageBox').slideDown();
});
$(".setEmpfohlen").on("click", function(e){
	e.preventDefault();
	$("#empfohlenKndIdInput").val( $(this).attr('kndId') );
});
</script>
	<?php
	}
	
	public function getGadget()
	{
	?>
		<div id='searchEmpfohlenBlock' >
			<div id='searchEmpfPanel' >
				<form id='searchEmpfForm' method='POST'>
					<table>
						<tr>
							<th>Vorname</th>
							<th>Name</th>
							<td rowspan='2'>
								<input type='submit' value='Suchen' style='padding: 10px 10px;'>
							</td>
						</tr>
						<tr>
							<td><input name='vorname' type='text' value='' /></td>
							<td><input name='name' type='text' value='' /></td>
						</tr>
					</table>
				</form>
			</div>
			<div id='EmpfohlenResults' >
		<?php
			echo $this->getTable();
		?>
			</div>
		
		</div>
<script>
selector = $('#EmpfohlenResults');
$putKndIdIn = "";
$("#searchEmpfForm").submit(function(e){
	e.preventDefault();
	
	var postData = $(this).serializeArray();

	$.ajax({
		url:'<?=BASIS_URL?>/admin/ajaxGetEmpfohlenGadgetUpdate',
		type:'POST',
		data:postData,
		dataType:'html',
		success:function(response){
			selector.html(response);
		},
		error:function(errorThrown){
			selector.text(errorThrown);
		}
	});
});

</script>
		<?php
	}
	public function getTable()
	{
		$sArr = array();
		$sArr[':vorname'] = empty($_POST['vorname']) ? '' : Fltr::filterStr($_POST['vorname']);
		$sArr[':name'] = empty($_POST['name']) ? '' : Fltr::filterStr($_POST['name']);
		
		$res = $this->getDates($sArr);
		
		$op = "<table>";
		if(empty($res))
		{
			$op .= "<tr><td>Nach Ihren Angaben wurde keinen Kunden gefunden.</td></tr>";
		}
		else
		{
			$op .= "<tr><td><button class='setEmpfohlen' kndId='' >Wählen</button></td>"
						."<td>Zurücksetzen</td>"
						."</tr>";
			foreach($res as $r)
			{
				$op .= "<tr><td><button class='setEmpfohlen' kndId='".$r['kndId']."' >Wählen</button></td>"
						."<td>".$r['vorname']."</td><td>".$r['name']."</td><td>".$r['strasse']."</td><td>".$r['strNr']."</td>"
						."<td>".$r['plz']."</td><td>".$r['stadt']."</td>"
						."</tr>";
			}
		}
		
		$op .= "</table>";
		$op .= "<script>$('.setEmpfohlen').on('click', function(e){e.preventDefault();$('#empfohlenKndIdInput').val( $(this).attr('kndId') );});</script>";
		return $op;
	}//public function getList()
	
	private function getDates($searchArr)
	{
		$dbh = DBFactory::getDBH();
		if(!$dbh)
		{
			return FALSE;
		}
		
		$searchArr = array_filter($searchArr);
		
		$q = "SELECT kndId, vorname, name, strasse, strNr, plz, stadt FROM kunden";
		$where = "";
		
		if(!empty($searchArr))
		{
			if(isset($searchArr[':vorname']))
			{
				$where .= " vorname LIKE :vorname AND";
				$searchArr[':vorname'] .= "%";
			}
			if(isset($searchArr[':name']))
			{
				$where .= " name LIKE :name AND";
				$searchArr[':name'] .= "%";
			}
			
			$where = substr($where, 0, -4);
			$q .= empty($where) ? '' : " WHERE " . $where;
		}
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute($searchArr);
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $rs;
			
		} catch (Exception $ex) {
			//print $ex;
			return FALSE;
		}
	}//private function getDates($searchArr)
	
	public function updateTable()
	{
		exit($this->getTable());
	}
}
