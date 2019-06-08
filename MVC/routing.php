<?php
function getRoutingList()
{
    $routingList = array(
		'/admin/users' => 'Users:UserList:getUserList',
		//'/admin/ajax' => 'AJAX:AjaxManager:manager',
        '/admin/' => 'Admin:AdminMain:mainPage',
//KundenListe
		'/admin/kundenListe' => 'Kunde:Kunde:showList',
		'/admin/kundenListePrint' => 'Kunde:Kunde:printList',
		'/admin/kundeById/$kId' => 'Kunde:KundeById:show',
		'/admin/kundeWithLastschrift' => 'Kunde:KundeWithLastschrift:getList',
		'/admin/neueKunde' => 'Kunde:neueKunde:showForm',
		'/admin/kundenBearbeitenListe' => 'Kunde:KundeBearbeiten:showList',
		'/admin/kundeBearbeitenById/$kndId' => 'Kunde:KundeBearbeiten:showKundeById',
		'/admin/bezahlenById/$kndId' => 'Kunde:Bezahlen:showKundeById',
		'/admin/ajaxGetBezahlungsformular' => 'Kunde:Bezahlen:ajaxGetFormular',
		'/admin/geburtstagsListe' => 'Kunde:ClientsBirthday:showList',
//Rechnung
		'/admin/ajaxConfirmRechnung' => 'Kunde:Bezahlen:ajaxConfirmRechnung',
		'/admin/ajaxShowRechnung' => 'Rechnung:Rechnung:ajaxShowRechnung',
		'/admin/ajaxEditRechnungsKommentar' => 'Rechnung:Rechnung:ajaxEditKomm',
		'/admin/ajaxDeleteRechnung' => 'Rechnung:Rechnung:ajaxDeleteRechnung',
		'/admin/ajaxSaveRechnung' => 'Kunde:Bezahlen:ajaxSaveRechnung',
		'/admin/testRechnung2Pdf' => 'Rechnung:Rechnung2Pdf:saveRechnungToPdf',
//Kunde
		'/admin/ajaxSaveNewKunde' => 'Kunde:neueKunde:saveNewKunde',
		'/admin/ajaxDeleteKunde' => 'Kunde:KundeLoeschen:ajaxDeleteKunde',
		'/admin/ajaxBankDatesUpdate' => 'Kunde:BankDatenUpdate:ajaxUpdate',
		'/admin/ajaxKursBezahlen' => 'Kunde:KursBezahlen:bezahlen',
		'/admin/ajaxKursVomKundeEntfernen' => 'Kunde:KursEntfernen:ajaxEntfernen',
		'/admin/ajaxGetEmpfohlenGadget' => 'Kunde:Empfohlen:getGadget',
		'/admin/ajaxGetEmpfohlenGadgetUpdate' => 'Kunde:Empfohlen:updateTable',
		'/login/' => 'Login:LoginController:loginForm',
		'/logout/' => 'Login:LoginController:logout',
		'/admin/mitarbeiter' => 'Mitarbeiter:Mitarbeiter:showList',
		'/admin/neuerMitarbeiter' => 'Mitarbeiter:NeuerMitarbeiter:showForm',
		'/admin/ajaxSaveNewMitarbeiter' => 'Mitarbeiter:NeuerMitarbeiter:saveNewMitarbeiter',
		'/admin/mitarbeiterBearbeitenListe' => 'Mitarbeiter:MitarbeiterBearbeiten:showList',
		'/admin/mitarbeiterBearbeitenById/$mtId' => 'Mitarbeiter:MitarbeiterBearbeiten:showMitarbeiterById',
//Lehrer
		'/admin/lehrer' => 'Lehrer:Lehrer:showList',
		'/admin/neuerLehrer' => 'Lehrer:NeuerLehrer:showForm',
		'/admin/lehrerBearbeitenListe' => 'Lehrer:LehrerBearbeiten:showList',
		'/admin/lehrerBearbeitenById/$mtId' => 'Lehrer:LehrerBearbeiten:showLehrerById',
		'/admin/ajaxSaveNewLehrer' => 'Lehrer:NeuerLehrer:saveNewLehrer',
		'/admin/lehrerVerdienstListe' => 'Lehrer:Verdienst:showList',
		'/admin/ajaxLehrerVerdienstKinder' => 'Lehrer:Verdienst:showLehrersChildren',
		'/admin/lehrerPrintGroups' => 'Lehrer:PrintGroups:startPage',
		'/admin/anwesensheitsliste' => 'Lehrer:Anwesensheitsliste:getList',
//Kurse
		'/admin/kurseListe' => 'Kurse:Kurse:showList',
		'/admin/neuerKurs' => 'Kurse:NeuerKurs:showForm',
		'/admin/kursInfo/$kId' => 'Kurse:KursInfo:getInfo',
		'/admin/ajaxSaveNewKurs' => 'Kurse:NeuerKurs:saveNewKurs',
		'/admin/ajaxDeleteKurs' => 'Kurse:KursLoeschen:ajaxDeleteKurs',
		'/admin/kurseBearbeitenListe' => 'Kurse:KurseBearbeiten:showList',
		'/admin/kurseBearbeitenById/$kurId' => 'Kurse:KurseBearbeiten:showKursById',
		'/admin/ajaxAddNewTermin' => 'Stundenplan:NewTermin:addNewTermin',
		'/admin/ajaxUpdateTermin' => 'Stundenplan:UpdateTermin:ajaxUpdate',
		'/admin/ajaxDeleteTermin' => 'Stundenplan:UpdateTermin:ajaxDelete',
		'/admin/ajaxStnPlKurInfo/$kurId' => 'Stundenplan:KurInfo:ajaxGetInfo',
		'/admin/ajaxKursSelectorUpdate' => 'Kurse:KursSelector:updateKursSelector',
		'/admin/ajaxAddKursToKunde' => 'Kunde:addKursToKunde:ajaxAddKurs',
		'/admin/ajaxUpdateKursToKunde' => 'Kunde:addKursToKunde:ajaxUpdateKurs',
		'/admin/ajaxEditSonderPreis' => 'Kunde:SonderPreis:ajaxEdit',
		'/admin/ajaxDeleteSonderPreis' => 'Kunde:SonderPreis:ajaxDelete',
//change Kurs
		'/admin/ajaxChangeKursInfo' => 'Kurse:ChangeKurs:getInfoJson',
		'/admin/ajaxChangeKursReallyDo' => 'Kurse:ChangeKurs:changeKursJson',
//Stundenplan
		'/admin/stundenplan' => 'Stundenplan:Stundenplan:showStundeplan',
		'/admin/print-stundenplan' => 'Stundenplan:PrintStundenplan:showStundeplan',
		'/admin/ajaxUpdateKhkKomm' => 'Kunde:UpdateKundenKursKommentar:ajaxUpdateKomm',
		'/admin/schuldner' => 'Kunde:Schuldner:showSchuldner',
//Statistik
		'/admin/statistik' => 'Statistik:Statistik:getStat',
		'/admin/statistikBuchhaltung' => 'Statistik:StatistikBuchhaltung:getStat',
		'/admin/getChartsData' => 'Statistik:Charts:getDataChar',
//Comments
		'/admin/ajaxAddNewKndKomm' => 'Kunde:CommentsToolsAjax:newComment',
		'/admin/ajaxUpdateKndKomm' => 'Kunde:CommentsToolsAjax:updateComment',
		'/admin/ajaxDeleteKndKomm' => 'Kunde:CommentsToolsAjax:deleteComment',
		'/admin/payday' => 'Statistik:PayDay:showCashbox',
//Copy Kunden to new School-Year
		'/admin/copyKndToNewDb' => 'Archiv:ArchivTools:copyKunde',
//Users
		'/admin/users' => 'User:Users:getUsersList',
		'/admin/users/getMitarbeiterListJson' => 'User:newUser:getMitarbeiterListJson',
		'/admin/users/getMitarbeiterInfoJson' => 'User:newUser:getMitarbeiterInfoJson',
		'/admin/users/insertNewLoginPswdJson' => 'Login:LoginTools:ajaxSetPassword',
		'/admin/users/updatePassword' => 'User:Users:updatePassword',
		'/admin/users/getGroupListJson' => 'User:Groups:getGroupListJson',
		'/admin/users/addGroupToMitarbeiterJson' => 'User:Groups:addGroupToMitarbeiter',
		'/admin/users/getUsersGroupJson' => 'User:Groups:getUsersGroup',
		'/admin/users/removeUserFromGroup' => 'User:Groups:removeUserFromGroup',
		'/admin/users/renameGroup' => 'User:Groups:renameGroups',
		'/admin/users/createGroup' => 'User:Groups:createGroups',
		'/admin/users/deleteGroup' => 'User:Groups:deleteGroups',
        //'/second/$name' => 'Content:ShowContent:hello',
        //'/summe/$a/$b' => 'Content:ShowContent:summe',
//Warteliste
		'/admin/warteliste' => 'Warteliste:Warteliste:showList',
    );
    return $routingList;
}