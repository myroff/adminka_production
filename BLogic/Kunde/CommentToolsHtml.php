<?php
namespace Kunde;
require_once BASIS_DIR.'/Tools/Filter.php';
use Tools\Filter as Fltr;
require_once BASIS_DIR.'/MVC/DBFactory.php';
use MVC\DBFactory as DBFactory;
use PDO as PDO;

class CommentToolsHtml 
{
	private static function getComments($kndId)
	{
		$dbh = DBFactory::getDBH();
			
		if(!$dbh)
		{
			return false;
		}
		
		$q = "SELECT cmnt.cmntId, cmnt.kndId, cmnt.mtId, cmnt.comment, m.vorname, m.name"
			.", DATE_FORMAT(created, '%d.%m.%Y %H:%i') as created"
			.", DATE_FORMAT(updated, '%d.%m.%Y %H:%i') as updated"
			." FROM kndComments as cmnt LEFT JOIN mitarbeiter as m USING(mtId)"
			." WHERE cmnt.kndId=:kndId"
			." ORDER BY cmnt.cmntId DESC";
		
		try
		{
			$sth = $dbh->prepare($q);
			$sth->execute(array(':kndId' => $kndId));
			$rs = $sth->fetchAll(PDO::FETCH_ASSOC);
			
			return $rs;
		}
		catch (Exception $ex) {
			$output = array('status' => "error", 'message' => $ex);
		}
	}//end private function getComments($kndId)
	
	public static function showComments($kndId)
	{
		$out = "";
		
	//kndId
		if(empty($kndId))
		{
			$out .= "kndId fehlt.";
		}
		else{
			if(Fltr::isInt($kndId))
			{
				$res = self::getComments($kndId);
				$out .= "";
				foreach($res as $r)
				{
					$out .= "<div class='cmntItem'>";
					$out .= "<span class='cmntAutor'>".$r['vorname']." ".$r['name']."</span>";
					$out .= "<span class='cmntDate'>";
					if(is_null($r['updated']))
					{
						$out .= "Erstellt am ".$r['created'];
					}
					else{
						$out .= "Geändert am ".$r['updated'];
					}
					$out .= "<button class='editItemButton editKndKmnt' cmntId='".$r['cmntId']."'></button>";
					$out .= "<button class='deleteButton deleteKndKmnt' cmntId='".$r['cmntId']."'></button>";
					$out .= "</span>";
					$out .= "<p class='cmntTxt'>".$r['comment']."</p>";
					$out .= "</div>";
				}
				$out .= "<div id='UpdateCmntTable' class='messageBox'>";
				$out .= "<p>Kommentar ändern.</p>";
				$out .= "<form id='UpdateCmntForm'>";
				$out .= "<input type='hidden' id='UpdateCmntForm_cmntId' name='cmntId' value=''/>";
				$out .= "<textarea id='UpdateCmntForm_CmntTxt' name='comment' ></textarea>";
				$out .= "<div class='buttonsOkCancelDiv'>";
				$out .= "<input class='submit' id='UpdateCmntForm_ButtonSpeichern' type='submit' value=''/>"
						."<button class='cancel' id='UpdateCmntForm_ButtonAbbrechen' ></button>";
				$out .= "</div>";
				$out .= "</form>";
				$out .= "</div>";
			}
			else{
				$out .= "kndId ist kein Integer.";
			}
		}
		
		return $out;
	}//end public function showComments($kndId)
	
	public static function newCommentsForm($kndId)
	{
		$out = "";
		
		if(empty($kndId))
		{
			$out .= "Kommentarform kann nicht erstellt werden: kndId fehlt.";
		}
		else{
			if(Fltr::isInt($kndId))
			{
				$out .= "<form id='newKndCmntForm'>";
				$out .= "<input type='hidden' name='kndId' value='$kndId'/>";
				$out .= "<textarea name='comment'></textarea>";
				$out .= "<input class='submit' id='newKndCmntForm_ButtonSpeichern' type='submit' value='' />";
				$out .= "<button class='cancel' id='newKndCmntForm_ButtonAbbrechen' ></button>";
				$out .= "</form>";
			}
			else{
				$out .= "kndId ist kein Integer.";
			}
		}
		
		return $out;
	}//end public function newCommentsForm($kndId)
	
	public static function newCommentsJsFnct($kndId)
	{
		ob_start();
		?>
		$('#newKndCmntForm').submit(function(e){
			e.preventDefault();
			var postData = $(this).serializeArray();

			$.ajax({
				url:'<?=BASIS_URL?>/admin/ajaxAddNewKndKomm',
				type:'POST',
				data:postData,
				dataType:'JSON',
				success:function(response){
					if(response.status == 'ok')
					{
						alert(response.message);
						window.location.reload(true);
					}
					else
					{
						alert(response.message);
					}
				},
				error:function(errorThrown){
					alert(errorThrown);
				}
			});
		});
		
		$('.editKndKmnt').click(function()
		{
			cmntId = $(this).attr('cmntId').trim();
			cmntTxt = $(this).parents().children('.cmntTxt').html().trim();

			$('#UpdateCmntForm_cmntId').val(cmntId);
			$('#UpdateCmntForm_CmntTxt').html(cmntTxt);

			$('#UpdateCmntTable').slideDown();
		});
		
		$('#UpdateCmntForm').submit(function(e){
			e.preventDefault();
			var postData = $(this).serializeArray();

			$.ajax({
				url:'<?=BASIS_URL?>/admin/ajaxUpdateKndKomm',
				type:'POST',
				data:postData,
				dataType:'JSON',
				success:function(response){
					if(response.status == 'ok')
					{
						alert(response.message);
						window.location.reload(true);
					}
					else
					{
						alert(response.message);
					}
				},
				error:function(errorThrown){
					alert(errorThrown);
				}
			});
		});
		
		$('#UpdateCmntForm_ButtonAbbrechen').click(function(e){
			e.preventDefault();
			$('#UpdateCmntTable').slideUp();
		});
		
		$('.deleteKndKmnt').click(function(){
		cmntId = $(this).attr('cmntId').trim();
		
		if(confirm("Wollen Sie wirklich diesen Kommentar löschen?? :-0") == true)
		{
			$.ajax({
				url:'<?=BASIS_URL?>/admin/ajaxDeleteKndKomm',
				type:'POST',
				data:{cmntId:cmntId},
				dataType:'JSON',
				success:function(response){
					if(response.status == 'ok')
					{
						alert(response.message);
						window.location.reload(true);
					}
					else
					{
						alert(response.message);
					}
				},
				error:function(errorThrown){
					alert(errorThrown);
				}
			});
		}
		else{
		}
	});
	<?php
		$js = ob_get_contents();
		ob_end_clean();
		return $js;
	}
}
