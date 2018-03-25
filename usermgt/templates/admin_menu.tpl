<p style="color: red;">{LoginMessage}</p>
<form action="{LoginBaseScript}" method="post">
	<center>
		<table border="0" cellspacing="4" cellpadding="0">
			<tr>
				<td colspan="2" style="text-align: center;">{UserSearch}&nbsp;:</td>
			<tr>
				<td><input class="Rounded" type="text" name="user_name" value=""></td>
				<td><input class="Buttons Rounded" type="submit" value="{Search}" /></td>
			</tr>
		</table>
	</center>
	<input type="hidden" name="LoginAction" value="admin">
	<input type="hidden" name="LoginAction2" value="finduser">
</form>
