		<center><font color="red">{LoginMessage}</font></center>
		<br>
		<center>
			<form name="login" method="POST" action="{LoginBaseScript}">
				<table border="0" cellspacing="0" cellpadding="6" class="LoginBox" style="display: block;">
					<tbody>
						<tr>
							<td colspan="100%" style="text-align: center;">
								<center>
								{LoginBoxLogo}<span class="LoginBoxTitle">{lblLoginEditProfile}</span>
								<hr>
								</center>
							</td>
						</tr>
						<tr>
							<td><b>{lblNickname}:</b></td>
							<td><input class="Text Rounded Disabled" type="text" maxlen="32" width="32" name="LoginNickname" value="{LoginNickname}" disabled></td>
						</tr>
						<tr>
							<td><b>{lblSurname}:</b></td>
							<td><input class="Text Rounded" type="text" maxlen="32" width="32" name="LoginSurname" value="{LoginSurname}" /></td>
						</tr>
						<tr>
							<td><b>{lblName}:</b></td>
							<td><input class="Text Rounded" type="text" maxlen="32" width="32" name="LoginName" value="{LoginName}" /></td>
						</tr>
						<tr>
							<td><b>{lblNewPassword}:</b></td>
							<td><input class="Text Rounded" type="password" maxlen="32" width="32"  name="LoginPassword"  value="" /></td>
						</tr>
						<tr>
							<td><b>{lblConfirmPassword}:</b></td>
							<td><input class="Text Rounded" type="password" maxlen="32" width="32"  name="LoginPassword2"  value="" /></td>
						</tr>
						<tr>
							<td><b>{lblEmail}:</b></td>
							<td><input class="Text Rounded" type="text" maxlen="32" width="32"  name="LoginEmail"  value="{LoginEmail}" /></td>
						</tr>
						<tr>
							<td colspan="2"><hr /></td>
						</tr>
						<tr>
							<td><b>{lblCurrentPassword}:</b></td>
							<td><input class="Text Rounded" type="password" maxlen="32" width="32"  name="LoginOldPassword"  value="" /></td>
						</tr>
						<tr>
							<td colspan="100%" style="text-align: center;">
								<input type="hidden" name="LoginAction" value="edit_valid">
								<hr />
								<input type="button" class="Buttons Rounded" OnClick="document.location = '{LoginBaseScript}';" value="{lblCancel}">
								&nbsp;<input type="submit" value="{lblSubmit}" class="Buttons Rounded">
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</center>
