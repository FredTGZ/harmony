<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html dir="ltr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
		<meta http-equiv="Content-Style-Type" content="text/css">
		<title>{DOCUMENT_TITLE}</title>
		<style type="text/css">
		<!--
		
			body {
				background-color: silver;
				font-family: Sans-Serif;
				font-size: 8pt;
			}

			table {
				font-family: Sans-Serif;
				font-size: 8pt;
			}
			
			input {
				font-family: Sans-Serif;
				font-size: 8pt;
			}

			select {
				font-family: Sans-Serif;
				font-size: 8pt;
			}

			.button {
				border: black fine solid;
				font-family: Sans-Serif;
				font-size: 10pt;
				background-color: transparent;
			}
			.aide {
				border: none;
				background-color: silver;
				font-family: Sans-Serif;
				font-size: 10pt;
			}
			.toolbar {
				background-image: url('bbedit_toolbar.gif');
				vertical-align: middle;
			}
			
			label
			{
				font-weight: bold;
			}
			
		-->
		</style>
	</head>
	<body link="#000080" text="#000000" vlink="#000080">
		<form action="{DOCUMENT_ACTION}" method="post" name="post" onsubmit="return checkForm(this)">
			<table border="0" cellpadding="0" cellspacing="0" width="750">
				<tbody>
					<tr height="32">
						<td width="80"><label for="doctitle">Titre&nbsp;:</label></td>
						<td colspan="4">
							<input name="doctitle" size="45" maxlength="60" style="width: 500px;" tabindex="2" value="{DOCUMENT_TITLE}" type="text">
						</td>
					</tr>
					<tr height="32">
						<td width="80"><label for="dockeywords">Mots clés&nbsp;:</label></td>
						<td colspan="4">
							<input name="dockeywords" size="45" maxlength="60" style="width: 500px;" tabindex="2" value="{DOCUMENT_KEYWORDS}" type="text">
						</td>
					</tr>
					<tr height="32">
						<td width="60"><label for="doclanguage">Langage&nbsp;:&nbsp;</label></td>
						<td width="80">
							<select name="doclanguage">
								<!-- A SUPPRIMER -->
								<option value="fr" selected>Français</option>
								<option value="en" selected>English</option>
							</select>
						</td>
						<td width="100" style="text-align: right;"><label for="doccss">Feuille de style&nbsp;:&nbsp;</label></td>
						<td width="80">
							<select name="doccss">
								{DOCUMENT_LISTCSS}
							</select>
							<!--<input name="doccss" size="45" maxlength="60" style="width: 500px;" tabindex="2" value="{DOCUMENT_CSS}" type="text">-->
						</td>
						<td><input type="checkbox" name="docusecache" value="{DOCUMENT_USECACHE}"><label for="docusecache">&nbsp;Utiliser le cache</label></td>
					</tr>
				</tbody>
			</table>
			<hr>
			
