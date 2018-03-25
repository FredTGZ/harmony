		<script language="JavaScript" type="text/javascript">
		<!--
		// Startup variables
		var imageTag = false;
		var theSelection = false;
		
		// Check for Browser & Platform for PC & IE specific bits
		// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
		var clientPC = navigator.userAgent.toLowerCase(); // Get client info
		var clientVer = parseInt(navigator.appVersion); // Get browser version
		
		var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
		var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
		                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
		                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
		var is_moz = 0;
		
		var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
		var is_mac = (clientPC.indexOf("mac")!=-1);
		
		// Helpline messages
		b_help = "Texte gras : [b]texte[/b] (alt+b)";
		i_help = "Texte italique : [i]texte[/i] (alt+i)";
		u_help = "Texte souligné : [u]texte[/u] (alt+u)";
		q_help = "Citation : [quote]texte cité[/quote] (alt+q)";
		c_help = "Afficher du code : [code]code[/code] (alt+c)";
		l_help = "Liste : [list]texte[/list] (alt+l)";
		o_help = "Liste ordonnée : [list=]texte[/list] (alt+o)";
		p_help = "Insérer une image : [img]http://image_url/[/img] (alt+p)";
		w_help = "Insérer un lien : [url]http://url/[/url] ou [url=http://url/]Nom[/url] (alt+w)";
		a_help = "Fermer toutes les balises BBCode ouvertes";
		t_help = "Style de titre (1 à 3) : [t1]Titre[/t1]";
		
		// Define the bbCode tags
		bbcode = new Array();
		bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote]','[/quote]','[code]','[/code]','[list]\r\n[*]','[/list]','[list=1]\r\n[*]','[/list]','[img]','[/img]','[url]','[/url]');
		imageTag = false;
		
		// Shows the help messages in the helpline window
		function helpline(help) {
			document.post.helpbox.value = eval(help + "_help");
		}
		
		
		// Replacement for arrayname.length property
		function getarraysize(thearray) {
			for (i = 0; i < thearray.length; i++) {
				if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
					return i;
				}
			return thearray.length;
		}
		
		// Replacement for arrayname.push(value) not implemented in IE until version 5.5
		// Appends element to the array
		function arraypush(thearray,value) {
			thearray[ getarraysize(thearray) ] = value;
		}
		
		// Replacement for arrayname.pop() not implemented in IE until version 5.5
		// Removes and returns the last element of an array
		function arraypop(thearray) {
			thearraysize = getarraysize(thearray);
			retval = thearray[thearraysize - 1];
			delete thearray[thearraysize - 1];
			return retval;
		}
		
		
		function checkForm() {
		
			formErrors = false;
		
			if (document.post.doccontent.value.length < 2) {
				formErrors = "Vous devez entrer un message avant de poster.";
			}
		
			if (formErrors) {
				alert(formErrors);
				return false;
			} else {
				bbstyle(-1);
				return true;
			}
		}
		
		function emoticon(text) {
			var txtarea = document.post.doccontent;
			text = ' ' + text + ' ';
			if (txtarea.createTextRange && txtarea.caretPos) {
				var caretPos = txtarea.caretPos;
				caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
				txtarea.focus();
			} else {
				txtarea.value  += text;
				txtarea.focus();
			}
		}
		
		function bbtitlestyle(bbopen, bbclose) {
			var txtarea = document.post.doccontent;
		
			if ((clientVer >= 4) && is_ie && is_win) {
				theSelection = document.selection.createRange().text;
				if (!theSelection) {
					txtarea.value += bbopen + bbclose;
					txtarea.focus();
					return;
				}
				document.selection.createRange().text = bbopen + theSelection + bbclose;
				txtarea.focus();
				return;
			}
			else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
			{
				mozWrap(txtarea, bbopen, bbclose);
				return;
			}
			else
			{
				txtarea.value += bbopen + bbclose;
				txtarea.focus();
			}
			storeCaret(txtarea);
		}
		
		
		function bbstyle(bbnumber) {
			var txtarea = document.post.doccontent;
		
			txtarea.focus();
			donotinsert = false;
			theSelection = false;
			bblast = 0;
		
			if (bbnumber == -1) { // Close all open tags & default button names
				while (bbcode[0]) {
					butnumber = arraypop(bbcode) - 1;
					txtarea.value += bbtags[butnumber + 1];
					buttext = eval('document.post.addbbcode' + butnumber + '.value');
					eval('document.post.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
				}
				imageTag = false; // All tags are closed including image tags :D
				txtarea.focus();
				return;
			}
		
			if ((clientVer >= 4) && is_ie && is_win)
			{
				theSelection = document.selection.createRange().text; // Get text selection
				if (theSelection) {
					// Add tags around selection
					document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber+1];
					txtarea.focus();
					theSelection = '';
					return;
				}
			}
			else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
			{
				mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber+1]);
				return;
			}
		
			// Find last occurance of an open tag the same as the one just clicked
			for (i = 0; i < bbcode.length; i++) {
				if (bbcode[i] == bbnumber+1) {
					bblast = i;
					donotinsert = true;
				}
			}
		
			if (donotinsert) {		// Close all open tags up to the one just clicked & default button names
				while (bbcode[bblast]) {
						butnumber = arraypop(bbcode) - 1;
						txtarea.value += bbtags[butnumber + 1];
						buttext = eval('document.post.addbbcode' + butnumber + '.value');
						eval('document.post.addbbcode' + butnumber + '.value ="' + buttext.substr(0,(buttext.length - 1)) + '"');
						imageTag = false;
					}
					txtarea.focus();
					return;
			} else { // Open tags
		
				if (imageTag && (bbnumber != 14)) {		// Close image tag before adding another
					txtarea.value += bbtags[15];
					lastValue = arraypop(bbcode) - 1;	// Remove the close image tag from the list
					document.post.addbbcode14.value = "Img";	// Return button back to normal state
					imageTag = false;
				}
		
				// Open tag
				txtarea.value += bbtags[bbnumber];
				if ((bbnumber == 14) && (imageTag == false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
				arraypush(bbcode,bbnumber+1);
				eval('document.post.addbbcode'+bbnumber+'.value += "*"');
				txtarea.focus();
				return;
			}
			storeCaret(txtarea);
		}
		
		// From http://www.massless.org/mozedit/
		function mozWrap(txtarea, open, close)
		{
			var selLength = txtarea.textLength;
			var selStart = txtarea.selectionStart;
			var selEnd = txtarea.selectionEnd;
			if (selEnd == 1 || selEnd == 2)
				selEnd = selLength;
		
			var s1 = (txtarea.value).substring(0,selStart);
			var s2 = (txtarea.value).substring(selStart, selEnd)
			var s3 = (txtarea.value).substring(selEnd, selLength);
			txtarea.value = s1 + open + s2 + close + s3;
			return;
		}
		
		// Insert at Claret position. Code from
		// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
		function storeCaret(textEl) {
			if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
		}
		
		//-->
		</script>
		
			<table border="0" cellpadding="0" cellspacing="0" width="750">
				<tbody>
					<!--<tr height="32">
						<td>
							Titre&nbsp;:&nbsp;<input name="doctitle" size="45" maxlength="60" style="width: 700px;" tabindex="2" value="{DOCUMENT_TITLE}" type="text">
						</td>
					</tr>-->
					<tr class="toolbar" height="32">
						<td valign="top">
							Style:&nbsp;
							<select name="addbbcode20" style="font-family: Sans-Serif; font-size: 8pt; background-color: white;" onchange="bbtitlestyle('[' + this.form.addbbcode20.options[this.form.addbbcode20.selectedIndex].value + ']', '[/' + this.form.addbbcode20.options[this.form.addbbcode20.selectedIndex].value + ']')" onmouseover="helpline('t')">
								<option value="t1" selected>Titre 1</option>
								<option value="t2">Titre 2</option>
								<option value="t3">Titre 3</option>
								<option value="code">Code</option>
								<option value="quote">Citation</option>
							</select>
							&nbsp;&nbsp;&nbsp;
							<input class="button" accesskey="b" value=" B " style="font-weight: bold; width: 30px;" onclick="bbstyle(0)" onmouseover="helpline('b')" type="button">
							<input class="button" accesskey="i" value=" i " style="font-style: italic; width: 30px;" onclick="bbstyle(2)" onmouseover="helpline('i')" type="button">
							<input class="button" accesskey="u" value=" u " style="text-decoration: underline; width: 30px;" onclick="bbstyle(4)" onmouseover="helpline('u')" type="button">
							&nbsp;&nbsp;&nbsp;
							<input class="button" accesskey="p" value="Image" style="width: 55px;" onclick="bbstyle(14)" onmouseover="helpline('p')" type="button">
							<input class="button" accesskey="w" value="Lien" style="text-decoration: underline; width: 40px;" onclick="bbstyle(16)" onmouseover="helpline('w')" type="button">
							&nbsp;&nbsp;&nbsp;
							<input class="button" accesskey="l" value="Liste" style="width: 50px;" onclick="bbstyle(10)" onmouseover="helpline('l')" type="button">
							<input class="button" accesskey="o" value="Liste=" style="width: 50px;" onclick="bbstyle(12)" onmouseover="helpline('o')" type="button">
							&nbsp;&nbsp;&nbsp;
							<a href="javascript:bbstyle(-1)" onmouseover="helpline('a')">Fermer les Balises</a>
							&nbsp;&nbsp;&nbsp;
							<input name="helpbox" size="45" maxlength="100" style="width: 750px; font-size: 10px;" class="aide" value="Astuce : Une mise en forme peut être appliquée au texte sélectionné." type="text">
						</td>
					</tr>
					<tr>
						<td>
							<input name="mode" value="newtopic" type="hidden"><input name="f" value="1" type="hidden"><input accesskey="s" tabindex="6" name="post" value="Envoyer" type="submit">
						</td>
					</tr>
				</tbody>
			</table>
			<hr>
			<textarea name="doccontent" rows="35" wrap="virtual" style="width: 100%;" tabindex="3" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);">{DOCUMENT_BODY}</textarea>
