<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<head>
		<title>{LoginTitle1} - {LoginTitle2}</title>
		<meta http-equiv="Content-Type" content="text/html" charset="ISO-8859-1">
		<meta http-equiv="Content-Language" content="{LoginLanguage}">
		{LoginCSS}
		<meta name="author" content="Harmony PHP Library, usermgt module.">
		<meta name="description" content="{TITLE}">
		<meta name="keywords" content="">
		<script type="text/javascript">{LoginJS}</script>
	</head>	
	<body class="Login" onresize="loginToolbarOnResize()"  onscroll="loginToolbarOnResize()">
		<center>
				<br />
				<table border="0" cellspacing="0" cellpadding="6" class="loginbox" width="400" height="150">
					<tbody>
						<tr style="vertical-align: top;">
							<td style="text-align: center;">
								<center><h1>Administration</h1><hr></center>
								{LoginAdminContent}
							</td>
						</tr>
					</tbody>
				</table>
				{LoginNavigator}
		</center>
	</body>
</html>
