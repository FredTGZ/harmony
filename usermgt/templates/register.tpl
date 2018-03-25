		<br>
		<br>
		<center><font color="red">{LoginMessage}</font></center>
		<br>
		<center>
			<form name="login" method="POST" action="{LoginBaseScript}">
				<table border="0" cellspacing="0" cellpadding="6" class="LoginBox">
					<tbody>
						<tr>
							<td colspan="100%" style="text-align: center;">
								<center>
								<table border="0" cellspacing="0" cellpadding="0"><tr><td>{LoginBoxLogo}</td></tr>
								<tr><td class="logintitle">{lblRegister}</td></tr></table>
								<hr class="loginhr">
								</center>
							</td>
						</tr>
						<tr>
							<td class="Label">{lblNickname}:</td>
							<td class="Input"><input class="Text Rounded" type="text" maxlength="32" width="32" name="LoginNickname" value=""></td>
						</tr>
						<tr>
							<td class="Label">{lblSurname}:</td>
							<td class="Input"><input class="Text Rounded" type="text" maxlength="32" width="32" name="LoginSurname" value=""></td>
						</tr>
						<tr>
							<td class="Label">{lblName}:</td>
							<td class="Input"><input class="Text Rounded" type="text" maxlength="32" width="32" name="LoginName" value=""></td>
						</tr>
						<tr>
							<td class="Label">{lblPassword}:</td>
							<td class="Input"><input class="Text Rounded" type="password" maxlength="32" width="32"  name="LoginPassword"  value=""></td>
						</tr>
						<tr>
							<td class="Label">{lblConfirmPassword}:</td>
							<td class="Input"><input class="Text Rounded" type="password" maxlength="32" width="32"  name="LoginPassword2"  value=""></td>
						</tr>
						<tr>
							<td class="Label">{lblEmail}:</td>
							<td class="I"><input class="Text Rounded" type="Text" maxlength="32" width="32"  name="LoginEmail"  value=""></td>
						</tr>
						<tr>
							<td colspan="100%" style="text-align: center;">
								<input type="hidden" name="LoginAction" value="register_valid">
								<hr class="loginhr">
								<input class="Buttons Rounded" type="button" value="{lblCancel}" OnClick="document.location='.';">
								&nbsp;<input class="Buttons Rounded" type="submit" value="{lblSubmit}" >
								
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</center>