		<center>
			<form name="edit_user" method="post" action="{LoginBaseScript}" style="margin: 0px;">
				<table border="0" cellspacing="0" cellpadding="6" class="LoginBox" style="background-color: silver;">
					<colgroup>
						<col width="80">
						<col width="200">
					</colgroup>
					<tbody>
						<tr>
							<td class="logintitle" colspan="2" style="text-align: center;">
							<p style="font-weight: bold; font-size: 16pt;">{LoginNickname}</p>
							</td>
						</tr>
						<tr>
							<td style="width: 120px;"><b>{lblSurname}:</b></td>
							<td><input type="text" maxlen="32" width="32" name="LoginSurname" style="width: 200px;" value="{LoginSurname}" class="Rounded"></td>
						</tr>
						<tr>
							<td><b>{lblName}:</b></td>
							<td><input type="text" maxlen="32" width="32" name="LoginName" style="width: 200px;" value="{LoginName}" class="Rounded"></td>
						</tr>
						<tr>
							<td><b>{lblEmail}:</b></td>
							<td><input type="text" maxlen="32" width="32"  name="LoginEmail" style="width: 200px;" value="{LoginEmail}" class="Rounded"></td>
						</tr>
						<tr>
							<td><b>{lblPassword}:</b></td>
							<td><input type="password" maxlen="32" width="32"  name="LoginPassword"  value="" style="width: 200px;" class="Rounded"></td>
						</tr>
						<tr>
							<td><b>{lblActivate} ?</b></td>
							<td><input type="checkbox" maxlen="32" width="32"  name="LoginActive"  {LoginActive} class="Rounded"></td>
						</tr>
						<tr>
							<td><b>{lblAdmin} ?</b></td>
							<td><input type="checkbox" maxlen="32" width="32"  name="LoginAdmin"  {LoginAdmin} class="Rounded"></td>
						</tr>
						<tr>
							<td colspan="2"><hr /></td>
						</tr>
						<tr>
							<td colspan="2">
								<center>
									<input type="button" class="Buttons Rounded" OnClick="document.location = '{LoginBaseScript}';" value="{lblCancel}">
									&nbsp;<input type="submit" value="{lblSubmit}" class="Buttons Rounded">
								</center>
							</td>
						</tr>
					</tbody>
				</table>
				
				<input type="hidden" name="LoginNickname" value="{LoginNickname}">
				<input type="hidden" name="LoginAction" value="admin">
				<input type="hidden" name="LoginAction2" value="saveuser">
			</form>
		</center>
