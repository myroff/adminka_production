<?php
namespace Tools;
use PDO as PDO;

class TmplTools {
	
	public static function printMaterializeSelector($data, $selectorName="", $selectorId="", $selectedValue="", $label="", $meterializeOn=FALSE)
	{
		$name	= empty($selectorName)	? '' : "name='$selectorName'";
		$id		= empty($selectorId)	? '' : "id='$selectorId'";
		
		$content = "<select $name $id>";
		
		foreach($data as $val => $txt){
			$isSel = $selectedValue == $val ? 'selected="selected"' : '';
			$content .= "<option value='".$val."' $isSel >".$txt."</option>";
		}
		$content .= "</select>";
		
		if($label){
			#$content .= "<label for='$selectorId' >$label</label>";
			$content .= "<label  >$label</label>";
		}
		
		if($meterializeOn){
			$jqSelector = $selectorId ? "#$selectorId" : "select";
			$content .= '<script>$(document).ready(function(){$("'.$jqSelector.'").formSelect();});</script>';
		}
		
		return $content;
	}


	public static function getLehrerSelector($selectorName="", $selectorId="", $selectedValue="", $label="", $meterializeOn=FALSE)
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return "kein dbh";
		}
		
		$q = "SELECT lehrId, anrede, vorname, name FROM lehrer ORDER BY vorname, name";
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);			
		} 
		catch (Exception $ex) {
			//print $ex;
			return $ex;
		}
		
		$data = array("" => "");
		
		foreach($rs as $r){
			$data[$r['lehrId']] = $r['anrede']." ".$r['vorname']." ".$r['name'];
		}
		
		$content = self::printMaterializeSelector($data, $selectorName, $selectorId, $selectedValue, $label, $meterializeOn);
		
		return $content;
	}
	
	public static function getWeekdaySelector($selectorName="", $selectorId="", $selectedValue="", $label="", $meterializeOn=FALSE)
	{
		
		$data = array("" => "", 1 => "Montag", 2 => "Dienstag", 3 => "Mittwoch", 4 => "Donnerstag"
					, 5 => "Freitag", 6 => "Samstag", 7 => "Sonntag");
		
		$content = self::printMaterializeSelector($data, $selectorName, $selectorId, $selectedValue, $label, $meterializeOn);
		
		return $content;
	}
	
	public static function getUnterrichtSelect($selectorName="", $selectorId="", $size="")
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh)
		{
			return "kein dbh";
		}
		
		$q = "SELECT kurId, kurName, kurPreis, kurMinAlter, kurMaxAlter, kurMinKlasse, kurMaxKlasse FROM kurse ORDER BY kurName";
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);			
		} 
		catch (Exception $ex) {
			//print $ex;
			return $ex;
		}
		$name = empty($selectorName) ? '' : "name='$selectorName'";
		$id = empty($selectorId) ? '' : "id='$selectorId'";
		$size = empty($size) ? '' : "size='$size'";
		
		echo "<select $name $id $size>";
		foreach($rs as $r)
		{
			$alter = $r['kurMinAlter'];
			$alter .= $r['kurMinAlter'] < $r['kurMaxAlter'] ? " bis ".$r['kurMaxAlter'] : '';

			$klasse = $r['kurMinKlasse'];
			$klasse .= $r['kurMinKlasse'] < $r['kurMaxKlasse'] ? " bis ".$r['kurMaxKlasse'] : "";
			
			echo"<option value='".$r['kurId']."'>".$r['kurName'].": ".$r['kurPreis']."€. ".$alter." Jahre. ".$klasse." Klasse</option>";
		}
		echo"</select>";
	}
	
	public static function getRaumSelector($selectorName="", $selectorId="", $selectedValue="")
	{
		$startRaum = 2;
		$endRaum = 10;
		
		$name = empty($selectorName) ? '' : "name='$selectorName'";
		$id = empty($selectorId) ? '' : "id='$selectorId'";
		$size = empty($size) ? '' : "size='$size'";
		
		echo "<select $name $id $size>";
		echo "<option value='' ></option>";
		
		for($i=$startRaum; $i<=$endRaum; ++$i)
		{
			$sel = $selectedValue == $i ? "selected" : "";
			echo"<option value='".$i."' $sel >".$i." Klasse</option>";
		}
		echo"</select>";
	}
	
	public static function getTimeSelectorOld($selectorName="", $selectorId="", $selectedValue="")
	{
		$startTime = 7;
		$endTime = 22;
		
		$name = empty($selectorName) ? '' : "name='$selectorName'";
		$id = empty($selectorId) ? '' : "id='$selectorId'";
		
		?>
<select <?php echo"$id $name";?> value='5'>
	<option value=""></option>
<?php
		for($i=$startTime; $i <= $endTime; $i++)
		{
			$sel = $selectedValue === $i.":00" ? "selected" : "";
			echo "<option value='".$i.":00' $sel >".$i.":00</option>";
			$sel = $selectedValue === $i.":30" ? "selected" : "";
			echo "<option value='".$i.":30' $sel >".$i.":30</option>";
		}
?>
</select>
<?php
	}
	
	public static function getTimeSelector($selectorName="", $selectorId="", $selectedValue="", $label="")
	{
		$content = "<label for='$selectorId'>$label</label>";
		$content .= "<input type='text' class='timepicker' name='$selectorName' id='$selectorId' value='$selectedValue'>";
		$jqSelector = $selectorId ? '#'.$selectorId : '.timepicker';
		$content .= '<script>$(document).ready(function(){$("'.$jqSelector.'").timepicker({twelveHour : false, showClearBtn : true});});</script>';
		return $content;
	}
	
	
	public static function getKursSelector($selectorName="", $selectorId="", $selectedValue="")
	{
		$name = empty($selectorName) ? '' : "name='$selectorName'";
		$id = empty($selectorId) ? '' : "id='$selectorId'";
		
		?>
<select <?php echo"$id $name";?> >
	<option value=""></option>
	<option value="Deutsch" <?=$selectedValue === 'Deutsch' ? 'selected' : ''?> >Deutsch</option>
	<option value="Mathe" <?=$selectedValue === 'Mathe' ? 'selected' : ''?> >Mathe</option>
	<option value="Russisch" <?=$selectedValue === 'Russisch' ? 'selected' : ''?> >Russisch</option>Choreografie
	<option value="Englisch" <?=$selectedValue === 'Englisch' ? 'selected' : ''?> >Englisch</option>
	<option value="Tanz" <?=$selectedValue === 'Tanz' ? 'selected' : ''?> >Tanz</option>
	<option value="Choreografie" <?=$selectedValue === 'Choreografie' ? 'selected' : ''?> >Choreografie</option>
	<option value="Kunst" <?=$selectedValue === 'Kunst' ? 'selected' : ''?> >Kunst</option>
	<option value="Gesang" <?=$selectedValue === 'Gesang' ? 'selected' : ''?> >Gesang</option>
	<option value="Logik" <?=$selectedValue === 'Logik' ? 'selected' : ''?> >Logik</option>
	<option value="Konzentration" <?=$selectedValue === 'Konzentration' ? 'selected' : ''?> >Konzentration</option>
	<option value="Kinderbetreuung" <?=$selectedValue === 'Kinderbetreuung' ? 'selected' : ''?> >Kinderbetreuung</option>
</select>
		<?php
	}
	
	public static function getKursSelectorById($selectorName="", $selectorId="", $selectedValue="", $label="", $meterializeOn=FALSE)
	{
		require_once BASIS_DIR.'/MVC/DBFactory.php';
		$dbh = \MVC\DBFactory::getDBH();
		if(!$dbh){
			return "kein dbh";
		}
		
		$q = "SELECT k.kurId, k.kurName, l.vorname, l.name FROM kurse as k LEFT JOIN lehrer as l USING(lehrId) WHERE isKurInactive IS NULL ORDER BY kurName";
		
		try{
			$sth = $dbh->prepare($q);
			$sth->execute();
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);			
		} 
		catch (Exception $ex){
			//print $ex;
			return $ex;
		}
		
		$data = array("" => "");
		foreach($rs as $r){
			$data[$r['kurId']] = $r['kurName']." [".$r['name']." ".$r['vorname']."]";
		}
		
		$content = self::printMaterializeSelector($data, $selectorName, $selectorId, $selectedValue, $label, $meterializeOn);
		
		return $content;
	}
	
	public static function getKlasseSelector($selectorName="", $selectorId="", $selectedValue="")
	{
		$name = empty($selectorName) ? '' : "name='$selectorName'";
		$id = empty($selectorId) ? '' : "id='$selectorId'";
		
		?>
<select <?php echo"$id $name";?> >
	<option value=""></option>
	<option value="1" <?=$selectedValue === '1' ? 'selected' : ''?> >1 Klasse</option>
	<option value="2" <?=$selectedValue === '2' ? 'selected' : ''?> >2 Klasse</option>
	<option value="3" <?=$selectedValue === '3' ? 'selected' : ''?> >3 Klasse</option>
	<option value="4" <?=$selectedValue === '4' ? 'selected' : ''?> >4 Klasse</option>
	<option value="5" <?=$selectedValue === '5' ? 'selected' : ''?> >5 Klasse</option>
	<option value="6" <?=$selectedValue === '6' ? 'selected' : ''?> >6 Klasse</option>
	<option value="7" <?=$selectedValue === '7' ? 'selected' : ''?> >7 Klasse</option>
	<option value="8" <?=$selectedValue === '8' ? 'selected' : ''?> >8 Klasse</option>
	<option value="9" <?=$selectedValue === '9' ? 'selected' : ''?> >9 Klasse</option>
</select>
		<?php
	}
	
	public static function getAlterSelector($selectorName="", $selectorId="", $selectedValue="")
	{
		$name = empty($selectorName) ? '' : "name='$selectorName'";
		$id = empty($selectorId) ? '' : "id='$selectorId'";
		
		?>
<select <?php echo"$id $name";?> >
	<option value=""></option>
	<option value="1" <?=$selectedValue === '1' ? 'selected' : ''?> >1 Jahr</option>
	<option value="2" <?=$selectedValue === '2' ? 'selected' : ''?> >2 Jahren</option>
	<option value="3" <?=$selectedValue === '3' ? 'selected' : ''?> >3 Jahren</option>
	<option value="4" <?=$selectedValue === '4' ? 'selected' : ''?> >4 Jahren</option>
	<option value="5" <?=$selectedValue === '5' ? 'selected' : ''?> >5 Jahren</option>
	<option value="6" <?=$selectedValue === '6' ? 'selected' : ''?> >6 Jahren</option>
	<option value="7" <?=$selectedValue === '7' ? 'selected' : ''?> >7 Jahren</option>
	<option value="8" <?=$selectedValue === '8' ? 'selected' : ''?> >8 Jahren</option>
	<option value="9" <?=$selectedValue === '9' ? 'selected' : ''?> >9 Jahren</option>
</select>
		<?php
	}
}
