<?php
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Lehrer-Verdienst.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		
		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>
		
		<script src="<?=BASIS_URL?>/Public/js/zebra_datepicker.js"></script>
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/zebra_default.css">
		<style>
			#resultsDiv{float:left;}
			.lehrer{cursor:pointer;color:blue;}
			#monatsBericht{float:left;margin-left:10px;}
		</style>
	</head>
	<body>
		<div id="horizontalMenu">
			<?php
			require_once BASIS_DIR.'/Templates/Menu.class.php';
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
			<div id="searchPanel">
				<form method="POST">
					<table>
						<tr>
							<th>
								Vorname
							</th>
							<th>
								Name
							</th>
							<th>
								Start Monat
							</th>
							<th>
								End Monat
							</th>
							<td rowspan="2">
								<input class="search" type='submit' value='' >
							</td>
						</tr>
						<tr>
							<td>
								<input name="vorname" type="text" value="<?=$sArr[':vorname']?>" />
							</td>
							<td>
								<input name="name" type="text" value="<?=$sArr[':name']?>" />
							</td>
							<td>
								<input name="startMnt" type="text" value="<?=$sArr[':startMnt']?>" class="zebra_datepicker_my" />
							</td>
							<td>
								<input name="endMnt" type="text" value="<?=$sArr[':endMnt']?>" class="zebra_datepicker_my" />
							</td>
						</tr>
					</table>
				</form>
			</div>
			
			<div id="resultsDiv">
				<table id="kundenResTbl">
					<tr>
						<th>
							Lehrer
						</th>
						<th>
							Summe
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
					$month = "";
					foreach ($res as $r)
					{
						if($month !== $r['rnMonat'])
						{
							$month = $r['rnMonat'];
							echo "<tr style='background:#575757;color:white;font-weight:bold;' ><td colspan='2'>"
									.strftime('%B %Y', strtotime($r['rnMonat']))."</td></tr>";;
						}
						echo "<tr>";
						
						echo "<td><span class='lehrer' lehrId='".$r['lehrId']."' rnMonat='".$r['rnMonat']."'>"
								.$r['anrede']." ".$r['vorname']." ".$r['name']."</span></td>";
						echo "<td>".$r['summe']."</td>";
						
						echo "</tr>";
					}
				}
				?>
				</table>
				
			</div>
			
			<div id="monatsBericht">
				
			</div>
		</div>
		<!-- END OF CONTENT -->
		
		<!--JavaScript -->
		<script>
		var meldung = $('#meldung');
		
		$(".zebra_datepicker_my").Zebra_DatePicker({
			offset: [10,200],
			format: 'Y-m',   //  note that becase there's no day in the format
							//  users will not be able to select a day!
		});
		
	/*zeige lehrers Kinder*/
		$('.lehrer').click(function(){
			lId = $(this).attr('lehrId');
			rnMnth = $(this).attr('rnMonat');
			
			$.ajax({
				url:'<?=BASIS_URL?>/admin/ajaxLehrerVerdienstKinder',
				type:'POST',
				data:{lehrId:lId,rnMonat:rnMnth},
				dataType:'JSON',
				success:function(response){
					if(response.status === 'ok')
					{
						$('#monatsBericht').html(response.message);
					}
					else
					{
						alert("Fehler: "+response.message);
					}
				},
				error:function(errorThrown){
					meldung.html(errorThrown);
				}
			});
		});
		</script>
	</body>
</html>
