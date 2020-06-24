<?php
namespace TemplateTools;
require_once BASIS_DIR.'/Tools/User.php';
use Tools\User as User;

class Menu
{
	private static $menuArr= 
	[
		['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/kundenListe', 'title'=>'Kunden',
			'linksList'=>
			[
				['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/neueKunde', 'title'=>'neue Kunde'],
				['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/kundenBearbeitenListe', 'title'=>'Kunden bearbeiten'],
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/kundeWithLastschrift', 'title'=>'Lastschrift'],
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/schuldner', 'title'=>'Schuldner'],
				['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/geburtstagsListe', 'title'=>'Geburtstage'],
			]
		],
		['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/mitarbeiter', 'title'=>'Mitarbeiter',
			'linksList'=>
			[
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/neuerMitarbeiter', 'title'=>'neuer Mitarbeiter'],
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/mitarbeiterBearbeitenListe', 'title'=>'Mitarbeiter bearbeiten'],
			]
		],
		['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/lehrer', 'title'=>'Lehrer',
			'linksList'=>
			[
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/neuerLehrer', 'title'=>'neuer Lehrer'],
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/lehrerBearbeitenListe', 'title'=>'Lehrer bearbeiten'],
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/lehrerVerdienstListe', 'title'=>'Verdienst'],
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/lehrerPrintGroups', 'title'=>'Gruppen'],
				['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/anwesensheitsliste', 'title'=>'Anwesensheitsliste'],
			]
		],
		['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/stundenplan', 'title'=>'Stundenplan',
			'linksList'=>
			[
				['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/print-stundenplan', 'title'=>'Stundenplan drücken'],
			]
		],
		['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/kurseListe', 'title'=>'Kurse',
			'linksList'=>
			[
				['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/neuerKurs', 'title'=>'neuer Kurs'],
				['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/kurseBearbeitenListe', 'title'=>'Kurs bearbeiten'],
				['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/seasonsEdit', 'title'=>'Seasons bearbeiten'],
			]
		],
		['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/statistik', 'title'=>'Statistik',
			'linksList'=>
			[
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/statistikBuchhaltung', 'title'=>'Buchhaltung'],
				['group'=>['Administrator'], 'user'=>array(),'link'=>'/admin/payday', 'title'=>'PayDay'],
			]
		],
		['group'=>[], 'user'=>['admin'],'link'=>'/admin/users', 'title'=>'Users'],
		['group'=>['Administrator','Editor'], 'user'=>array(),'link'=>'/admin/warteliste', 'title'=>'Warteliste'],
	];
	public static function adminMenuOld() //adminMenu
	{
	?>
<ul>
	<li>
		<a href="<?=BASIS_URL?>/admin/kundenListe" >Kunden</a>
		<ul>
			<li>
				<a href="<?=BASIS_URL?>/admin/neueKunde" >neue Kunde</a>
			</li>
			<li>
				<a href="<?=BASIS_URL?>/admin/kundenBearbeitenListe" >Kunden bearbeiten</a>
			</li>
			<li>
				<a href="<?=BASIS_URL?>/admin/kundeWithLastschrift" >Lastschrift</a>
			</li>
			<li>
				<a href="<?=BASIS_URL?>/admin/schuldner" >Schuldner</a>
			</li>
		</ul>
	</li>
	<li>
		<a href="<?=BASIS_URL?>/admin/mitarbeiter" >Mitarbeiter</a>
		<ul>
			<li>
				<a href="<?=BASIS_URL?>/admin/neuerMitarbeiter" >neuer Mitarbeiter</a>
			</li>
			<li>
				<a href="<?=BASIS_URL?>/admin/mitarbeiterBearbeitenListe" >Mitarbeiter bearbeiten</a>
			</li>
		</ul>
	</li>
	<li>
		<a href="<?=BASIS_URL?>/admin/lehrer" >Lehrer</a>
		<ul>
			<li>
				<a href="<?=BASIS_URL?>/admin/neuerLehrer" >neuer Lehrer</a>
			</li>
			<li>
				<a href="<?=BASIS_URL?>/admin/lehrerBearbeitenListe" >Lehrer bearbeiten</a>
			</li>
			<li>
				<a href="<?=BASIS_URL?>/admin/lehrerVerdienstListe" >Verdienst</a>
			</li>
		</ul>
	</li>
	<li>
		<a href="<?=BASIS_URL?>/admin/stundenplan" >Stundenplan</a>
	</li>
	<li>
		<a href="<?=BASIS_URL?>/admin/kurseListe" >Kurse</a>
		<ul>
			<li>
				<a href="<?=BASIS_URL?>/admin/neuerKurs" >neuer Kurs</a>
			</li>
			<li>
				<a href="<?=BASIS_URL?>/admin/kurseBearbeitenListe" >Kurs bearbeiten</a>
			</li>
			<!--
			<li>
				<a href="<=BASIS_URL?>/admin/stundenplanBearbeiten" >Stundenplan bearbeiten</a>
			</li>
			-->
		</ul>
	</li>
	<li>
		<a href="<?=BASIS_URL?>/admin/statistik" >Statistik</a>
		<ul>
			<li>
				<a href="<?=BASIS_URL?>/admin/statistikBuchhaltung" >Buchhaltung</a>
			</li>
			<li>
				<a href="<?=BASIS_URL?>/admin/payday" >PayDay</a>
			</li>
		</ul>
	</li>
</ul>
	<?php
	}//public static function adminMenu()
	
	public static function adminMenu(){
		$login = User::getUserLogin();
		$gruppen = User::getUserGroup();
		
		self::adminMenuDynam(self::$menuArr, $login, $gruppen);
	}
	
	private static function adminMenuDynam($menuArray, $login, $gruppen){
		echo "<ul>";
		foreach($menuArray as $arr){
			
			if(in_array($login, $arr['user']) OR self::multi_in_array($gruppen, $arr['group'])){
				echo "<li>";
				echo "<a href='".BASIS_URL.$arr['link']."' >".$arr['title']."</a>";
				if(isset($arr['linksList']) AND !empty($arr['linksList']))
					self::adminMenuDynam($arr['linksList'], $login, $gruppen);
				echo "</li>";
			}
		}
		echo "</ul>";
	}//public static function adminMenuDynam()
	
	private static function multi_in_array($needle, $stack) {
		foreach ($needle as $n){
			if(in_array($n, $stack))
					return true;
		}
		return false;
	}
}