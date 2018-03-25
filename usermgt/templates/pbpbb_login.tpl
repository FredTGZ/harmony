r		<center>
			<span class="LoginMessage">{LoginMessage}</span><br><br>
			<form name="login" method="POST" action="{LoginBaseScript}">
				<input type="hidden" name="sid" value="{UserSID}" />
				<input type="hidden" name="LoginAction" value="login">
				<table class="LoginBox" border="0" cellspacing="0" cellpadding="2">
					<tbody>
						<tr>
							<td class="Title" colspan="2"><center>{LoginBoxLogo}</center></td>
						</tr>
						<tr>
							<td class="Label">{lblNickname}:</td>
							<td class="Input">
								<input class="Text" type="text" maxlen="32" width="32" name="LoginNickname" tabindex="1" value="{LoginNickname}">
							</td>
						</tr>
						<tr>
							<td class="Label">{lblPassword}:</td>
							<td class="Input">
								<input class="Text" type="password" maxlen="32" width="32" name="LoginPassword"  tabindex="2" value="">
							</td>
						</tr>
						<tr>
							<td colspan="2" style="height: 10px;"></td>
						</tr>
						<tr>
							<td class="Input" colspan="2">
								<label class="Checkbox" for="autologin"><input class="Checkbox" type="checkbox" name="autologin" id="autologin" tabindex="3" /> Me connecter automatiquement à chaque visite</label>
							</td>
						</tr>
						<tr>
							<td class="Input" colspan="2">
								<label class="Checkbox" for="viewonline"><input class="Checkbox" type="checkbox" name="viewonline" id="viewonline" tabindex="4" /> Cacher mon statut en ligne pour cette session</label>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="height: 10px;"></td>
						</tr>
						<tr>
							<td class="Buttons" colspan="2">
								<input type="submit" value="{lblSubmit}">
							</td>
						</tr>
					</tbody>
				</table>
			</form>
			{LoginSubscribe}
		</center>