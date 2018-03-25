		<center>
			<span class="LoginMessage">{LoginMessage}</span><br><br>
			<form name="login" method="POST" action="{LoginBaseScript}">
				<table class="LoginBox" border="0" cellspacing="0" cellpadding="6">
					<tbody>
						<tr>
							<td class="Title" colspan="2">{LoginBoxLogo}{LoginTitle1}</td>
						</tr>
						<tr>
							<td class="Label">{lblNickname}:</td>
							<td class="Input"><input class="Text Rounded" type="text" maxlen="32" width="32" name="LoginNickname" value="{LoginNickname}" /></td>
						</tr>
						<tr>
							<td class="Label">{lblPassword}:</td>
							<td class="Input"><input class="Text Rounded" type="password" maxlen="32" width="32" name="LoginPassword"  value="" /></td>
						</tr>
						<tr>
							<td class="Center" colspan="2">
								<input class="Buttons Rounded" type="submit" value="{lblSubmit}" />
								<input type="hidden" name="LoginAction" value="login" />
							</td>
						</tr>
					</tbody>
				</table>
			</form>
			{LoginSubscribe}
		</center>