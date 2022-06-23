<html>
	<head>
		<title>SWIFF: Users.</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

		<link rel="stylesheet" href="<?=BASIS_URL?>/Public/css/style.css" type="text/css" media="screen" />

		<script src="<?=BASIS_URL?>/Public/js/jquery-2.1.1.min.js"></script>

		<style>
			#groups{overflow:hidden;}
			#grpList{list-style-type:none;text-decoration:none;padding:0;}
			#grpList li {display:block;float:left;margin-left:10px;border:1px solid black;border-radius:5px;padding:5px;cursor:pointer;}

			#users{margin-top:20px;}

			#infoBox{}
		</style>
	</head>
	<body>
		<div id="horizontalMenu">
			<?php
			TemplateTools\Menu::adminMenu();
			?>
		</div>
		<div id="meldung">
			<?php if(isset($meldung)) echo $meldung;?>
		</div>
		<!-- START OF CONTENT -->
		<div id="mainContent">
			<div id="groups">
				<span>Gruppen</span>
				<button id="addGroup">Neue Gruppe</button>
				<button id="addUser">Neuer User</button>
				<ul id="grpList">
				<?php
				foreach ($groups as $g) {
					echo "<li><span class='grpName'>".$g['grpName']."</span>(".$g['grpId'].")<br>"
						."<button class='renameGroup' grpId='".$g['grpId']."' >Umbenennen</button><br>"
						."<button class='deleteGroup' grpId='".$g['grpId']."' >Löschen</button>"
						."</li>";
				}
				?>
				</ul>
			</div>
			<div id="users" class="standardTable">
				<table>
					<tr>
						<th>Login</th>
						<th>Gruppen</th>
						<th>Anrede</th>
						<th>Vorname</th>
						<th>Name</th>
						<th>Funktionen</th>
					</tr>
					<?php
					foreach ($users as $u) {
						echo "<tr>";
							echo "<td>".$u['login']."</td>";
							echo "<td>".$u['gruppen']."</td>";
							echo "<td>".$u['anrede']."</td>";
							echo "<td>".$u['vorname']."</td>";
							echo "<td>".$u['name']."</td>";
							echo "<td>"
									."<button class='addGroupToUser' mtId='".$u['mtId']."' >Add Group</button>"
									."<button class='removeUserFromGroup' mtId='".$u['mtId']."' >Remove Group</button>"
									."<button class='updatePassword' mtId='".$u['mtId']."' >Password ändern</button>"
								."</td>";
						echo "</tr>";
					}
					?>
				</table>
			</div>
		</div>

		<div id="infoBox" class="messageBox">
			<div id="infoBoxTitle"></div>
			<div id="infoBoxDisplay"></div>
			<div id="infoBoxButtons">
				<button id="infoBoxButtonOk">OK</button>
				<button id="infoBoxButtonCancel">Cancel</button>
			</div>
		</div>

		<script>
//Gruppe Umbenennen
		$('.renameGroup').click(function(){
			var grpId = $(this).attr('grpId');
			var oldName = $(this).parent().children('.grpName').first().text();
			var newName = prompt("Please enter new name", oldName);

			if(newName === oldName || newName === '' || newName == null){
				return;
			}

			$.ajax({
				url:'<?=BASIS_URL?>/admin/users/renameGroup',
				type:'POST',
				data:{grpId:grpId, grpName:newName},
				dataType:'JSON',
				success:function(response){
					alert(response.message);

					if(response.status === "ok"){
						window.location.reload(true);
					}
				},
				error:function(errorThrown){
					alert("Error: "+errorThrown);
				}
			});
		});
//neue Gruppe
		$('#addGroup').click(function(){
			var newGroup = prompt("Please enter new group");

			if(newGroup === '' || newGroup == null){
				return;
			}

			$.ajax({
				url:'<?=BASIS_URL?>/admin/users/createGroup',
				type:'POST',
				data:{newGrp:newGroup},
				dataType:'JSON',
				success:function(response){
					alert(response.message);

					if(response.status === "ok"){
						window.location.reload(true);
					}
				},
				error:function(errorThrown){
					alert("Error: "+errorThrown);
				}
			});
		});
//löschen Gruppe
		$('.deleteGroup').click(function(){
			var grpId = $(this).attr('grpId');
			var grpName = $(this).parent().children('.grpName').first().text();

			if(confirm("Die Gruppe "+grpName+" löschen?")){
				$.ajax({
					url:'<?=BASIS_URL?>/admin/users/deleteGroup',
					type:'POST',
					data:{grpId:grpId},
					dataType:'JSON',
					success:function(response){
						alert(response.message);

						if(response.status === "ok"){
							window.location.reload(true);
						}
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});
			}
		});
//get Userlist JSON
		$('#addUser').click(function(){
			$('#infoBoxTitle').html("Wählen Sie einen Mitarbeiter.");

			$.ajax({
				url:'<?=BASIS_URL?>/admin/users/getMitarbeiterListJson',
				type:'POST',
				dataType:'JSON',
				success:function(response){
					if(response.status === "ok"){
						MitarbeiterToRadioButton(response.message);
					}
					else{
						alert(response.message);
					}
				},
				error:function(errorThrown){
					alert("Error: "+errorThrown);
				}
			});

			$('#infoBox').slideDown();
		});

//MitarbeiterJSON to Radio-List
		function MitarbeiterToRadioButton(jsonList){
			var listLength = jsonList.length;

			for(var i=0; i<listLength; i++){
				var txt = '<input type="radio" name="mitarbeiter" value="'+jsonList[i]['mtId']+'">'+jsonList[i]['vorname']+' '+jsonList[i]['name']+'<br>';
				$('#infoBoxDisplay').append(txt);
			}
			$('#infoBoxButtonOk').off('click');
			$('#infoBoxButtonOk').click(function(){
				var mtId = $('input[name=mitarbeiter]:checked').val();

				$.ajax({
					url:'<?=BASIS_URL?>/admin/users/getMitarbeiterInfoJson',
					type:'POST',
					data: {mtId:mtId},
					dataType:'JSON',
					success:function(response){
						if(response.status === "ok"){
							addNewUserForm(response.message);
						}
						else{
							alert(response.message);
						}
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});

			});
		}
//send dates to server
		function addNewUserForm(userInfo){
			$form = $("<form id=newUserForm></form>");
			$form.append("<div>Mitarbeiter: "+userInfo['vorname']+" "+userInfo['name']+"</div>");
			$form.append("Login:");
			$login = $("<input type='text' name='login' id='nu_login' />");
			$form.append($login);
			$form.append("Password:");
			$pswd = $("<input type='text' name='pswd' id='nu_pswd'/>");
			$form.append($pswd);

			$('#infoBoxDisplay').empty();
			$('#infoBoxDisplay').append($form);

			var mtId = userInfo['mtId'];
			$('#infoBoxButtonOk').off('click');
			$('#infoBoxButtonOk').click(function(){
				var login = $login.val();
				var pswd = $pswd.val();
				alert("mtId="+mtId+" login="+login+" pswd="+pswd);

				$.ajax({
					url:'<?=BASIS_URL?>/admin/users/insertNewLoginPswdJson',
					type:'POST',
					data: {mtId:mtId,login:login,newPswd:pswd},
					dataType:'JSON',
					success:function(response){
						if(response.status === "ok"){
							alert(response.message);
							window.location.reload(true);
						}
						else{
							alert(response.message);
						}
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});

			});
		}

//add Group to user
		$('.addGroupToUser').click(function(){
			$('#infoBoxTitle').html("Wählen Sie eine Gruppe aus.");
			var mtId = $(this).attr('mtId');

			$.ajax({
					url:'<?=BASIS_URL?>/admin/users/getGroupListJson',
					type:'POST',
					dataType:'JSON',
					success:function(response){
						if(response.status === "ok"){
							GroupsToRadioButton(response.message, mtId, '/addGroupToMitarbeiterJson');
						}
						else{
							alert(response.message);
						}
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});

			$('#infoBox').slideDown();
		});
		function GroupsToRadioButton(jsonList, mtId, actionUrl){
			var listLength = jsonList.length;

			for(var i=0; i<listLength; i++){
				var txt = '<input type="radio" name="group" value="'+jsonList[i]['grpId']+'">'+jsonList[i]['grpName']+'<br>';
				$('#infoBoxDisplay').append(txt);
			}
			$('#infoBoxButtonOk').off('click');

			$('#infoBoxButtonOk').click(function(){
				var grpId = $('input[name=group]:checked').val();

				$.ajax({
					url:'<?=BASIS_URL?>/admin/users'+actionUrl,
					type:'POST',
					data: {mtId:mtId,grpId:grpId},
					dataType:'JSON',
					success:function(response){
						if(response.status === "ok"){
							alert(response.message);
							window.location.reload(true);
						}
						else{
							alert(response.message);
						}
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});

			});

		}
//remove User From Group
		$('.removeUserFromGroup').click(function(){
			$('#infoBoxTitle').html("Wählen Sie eine Gruppe aus.");
			var mtId = $(this).attr('mtId');

			$.ajax({
					url:'<?=BASIS_URL?>/admin/users/getUsersGroupJson',
					type:'POST',
					data:{mtId:mtId},
					dataType:'JSON',
					success:function(response){
						if(response.status === "ok"){
							GroupsToRadioButton(response.message, mtId, '/removeUserFromGroup');
						}
						else{
							alert(response.message);
						}
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});

			$('#infoBox').slideDown();
		});
//Password ändern
		$('.updatePassword').click(function(){
			$('#infoBoxTitle').html("Neues Passwort eingeben.");
			var mtId = $(this).attr('mtId');

			$form = $("<form id=updatePassword></form>");
			$form.append("Password:");
			$pswd = $("<input type='text' name='pswd' id='nu_pswd'/>");
			$form.append($pswd);

			$('#infoBoxDisplay').empty();
			$('#infoBoxDisplay').append($form);

			$('#infoBox').slideDown();

			$('#infoBoxButtonOk').off('click');
			$('#infoBoxButtonOk').click(function(){
				var pswd = $pswd.val();
				alert("mtId="+mtId+" pswd="+pswd);

				$.ajax({
					url:'<?=BASIS_URL?>/admin/users/updatePassword',
					type:'POST',
					data: {mtId:mtId,pswd:pswd},
					dataType:'JSON',
					success:function(response){
						if(response.status === "ok"){
							alert(response.message);
							window.location.reload(true);
						}
						else{
							alert(response.message);
						}
					},
					error:function(errorThrown){
						alert("Error: "+errorThrown);
					}
				});

			});

		});
//button cancel in infoBox
		$('#infoBoxButtonCancel').click(function(){
			$('#infoBox').slideUp();
			$('#infoBoxDisplay').empty();
			$('#infoBoxButtonOk').off('click');
		});
		</script>
	</body>
</html>