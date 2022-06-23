<?php
use Tools\TmplTools as TmplTls;
use Tools\Filter as Fltr;
?>
<!DOCTYPE html>
<html>
	<head>
		<title>SWIFF: Warteliste.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style-print.css" type="text/css" media="print" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<style>

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
			<div id="searchPanel" class="dont-print">
				<form id="searchFrom" method="GET">
					<table>
						<tr>
							<th>
								Kurs
							</th>
							<td rowspan="2">
								<input class="search" type='submit' value='' style="padding: 10px 10px;">
							</td>
						</tr>
						<tr>
							<td>
								<input type="text" id="s_kurs" name="s_kurs" />
							</td>
						</tr>
					</table>
				</form>
			</div>

			<div id="kursListe">
			<?php
			if( isset($kurse) && !empty($kurse) ){
				foreach ($kurse as $k){
				?>
				<div class="kursItem">
					<p class="title"><?=$k['title']?></p>
					<p class="to_start">ab <?=$k['members_to_start']?></p>
				</div>
				<?php
				}
			}
			?>
			</div>
		</div>
		<!-- END OF CONTENT -->
<!--JavaScript -->
<script>

</script>
	</body>
</html>