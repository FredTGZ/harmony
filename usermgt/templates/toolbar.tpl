<!-- USERMGT module for Harmony - Toolbar Template -->
		{CSSStyle}<br>
		<table id="logintoolbar" border="0" cellspacing="0" cellpadding="0" class="LoginToolbar">
			<tbody>
				<tr>
					<td style="text-align: left; padding-left: 4px;">{LoginYouAreConnected}<span id="SessionExpiration"></span></td>
					<td>{LoginMessage}</td>
					<td><span id="AjaxServiceState"></span></td>
					<td width="24"><img src="{HarmonyPath}/image.php?img={ClientBrowserImage}" style="cursor: pointer; border: 0px; width: 24px; height: 24px;" OnClick="DisplayClientInfos()"></td>
					<td width="8"></td>
					<td width="24"><img src="{HarmonyPath}/image.php?img=login_info" style="cursor: pointer; border: 0px; width: 24px; height: 24px;" title="About usermgt" OnClick="DisplayVersion()"></td>
					<td width="8"></td>
					{LoginAdminButton}
					<td width="8"></td>
					<td width="100">
						<div style="height: 24px;">
							<form name="disconnect" method="post" action="{LoginRootScript}" style="margin: 0px;">
								<input type="hidden" name="LoginAction" value="reset">
								<input class="Buttons Rounded" type="submit" value="{LoginDisconnect}" name="submit_disconnect">
							</form>
						</div>
					</td>
					<td width="8"></td>
					<td width="125" style="vertical-align: middle;">
						<div style="height: 24px;">
							<form name="edit_profile" method="post" action="{LoginBaseScript}" style="margin: 0px;">
								<input type="hidden" name="LoginAction" value="edit">
								<input class="Buttons Rounded" style="display: block;" type="submit" value="{lblLoginEditProfile}" name="submit_edit">
							</form>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<script language="JavaScript">
			function dump(arr,level) {
				var dumped_text = "";
				if(!level) level = 0;
				
				//The padding given at the beginning of the line.
				var level_padding = "";
				for(var j=0;j<level+1;j++) level_padding += "    ";
				
				if(typeof(arr) == 'object') { //Array/Hashes/Objects 
					for(var item in arr) {
						var value = arr[item];
						
						if(typeof(value) == 'object') { //If it is an array,
							dumped_text += level_padding + "'" + item + "' ...\n";
							dumped_text += dump(value,level+1);
						} else {
							dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
						}
					}
				} else { //Stings/Chars/Numbers etc.
					dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
				}
				return dumped_text;
			}
			
			
			var EndDate = new Date().getTime() + {LoginSessionExpiration}  * 1000;
			

			function UpdateSessionExpiration()
			{
				var time = (EndDate - new Date().getTime());
				
				if (time>=0) {
					var minutes = Math.floor(time/60000);
					var seconds = Math.floor((time - minutes * 60000)/1000);
					
					document.getElementById('SessionExpiration').innerHTML = '{LoginSessionExpirationText} ' + minutes + ' {LoginTimeMinutes}, ' + seconds + ' {LoginTimeSeconds}';
					window.setTimeout("UpdateSessionExpiration()", 100);
				}
				else document.location = '{LoginBaseScript}';
			}
			
			UpdateSessionExpiration();
			
			function DisplayClientInfos()
			{
				alert("IP Adress: {ClientIP}\nLanguage: {ClientLanguage}\nBrowser: {ClientBrowser}\nOperating System: {ClientOS}");
			}


			function DisplayVersion()
			{
				alert('{ModuleName}' + ' version ' + '{ModuleVersion}'+"\n\n"+'{ModuleDescription}');
			}
		</script>
		<script type="text/javascript">loginToolbarOnResize();</script>
<!-- USERMGT module for Harmony - Toolbar Template -->
