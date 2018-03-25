<!-- BEGIN ulist_open --><ul><!-- END ulist_open -->
<!-- BEGIN ulist_close --></ul><!-- END ulist_close -->

<!-- BEGIN olist_open --><ol type="{LIST_TYPE}"><!-- END olist_open -->
<!-- BEGIN olist_close --></ol><!-- END olist_close -->

<!-- BEGIN listitem --><li><!-- END listitem -->

<!-- BEGIN quote_username_open --></span>
<table width="90%" cellspacing="1" cellpadding="3" border="0" align="center">
<tr> 
	  <td><span class="genmed"><b>{USERNAME} {L_WROTE}:</b></span></td>
	</tr>
	<tr>
	  <td class="quote"><!-- END quote_username_open -->
<!-- BEGIN quote_open --></span>
<table width="90%" cellspacing="1" cellpadding="3" border="0" align="center">
<tr> 
	  <td><span class="genmed"><b>{L_QUOTE}:</b></span></td>
	</tr>
	<tr>
	  <td class="quote"><!-- END quote_open -->
<!-- BEGIN quote_close --></td>
	</tr>
</table>
<span class="postbody"><!-- END quote_close -->

<!-- BEGIN code_open --></span>
<table width="90%" cellspacing="1" cellpadding="3" border="0" align="center">
<tr> 
	  <td><span class="genmed"><b>{L_CODE}:</b></span></td>
	</tr>
	<tr>
	  <td class="code"><!-- END code_open -->
<!-- BEGIN code_close --></td>
	</tr>
</table>
<span class="postbody"><!-- END code_close -->


<!-- BEGIN b_open --><span style="font-weight: bold"><!-- END b_open -->
<!-- BEGIN b_close --></span><!-- END b_close -->

<!-- BEGIN u_open --><span style="text-decoration: underline"><!-- END u_open -->
<!-- BEGIN u_close --></span><!-- END u_close -->

<!-- BEGIN i_open --><span style="font-style: italic"><!-- END i_open -->
<!-- BEGIN i_close --></span><!-- END i_close -->

<!-- BEGIN color_open --><span style="color: {COLOR}"><!-- END color_open -->
<!-- BEGIN color_close --></span><!-- END color_close -->

<!-- BEGIN size_open --><span style="font-size: {SIZE}px; line-height: normal"><!-- END size_open -->
<!-- BEGIN size_close --></span><!-- END size_close -->

<!-- BEGIN img --><img src="{URL}" border="0" /><!-- END img -->

<!-- BEGIN url --><a href="{URL}">{DESCRIPTION}</a><!-- END url -->
<!-- BEGIN doclink --><span class="doclink" href="index.php" OnClick="ViewDocument('{DOCUMENT}');" onmouseover="this.style.cursor='hand'">{DESCRIPTION}</span><!-- END doclink -->

<!-- BEGIN email --><a href="mailto:{EMAIL}">{EMAIL}</a><!-- END email -->

<!-- BEGIN t1_open --><h1><!-- END t1_open -->
<!-- BEGIN t2_open --><h2><!-- END t2_open -->
<!-- BEGIN t3_open --><h3><!-- END t3_open -->
<!-- BEGIN t1_close --></h1><!-- END t1_close -->
<!-- BEGIN t2_close --></h2><!-- END t2_close -->
<!-- BEGIN t3_close --></h3><!-- END t3_close -->

<!-- BEGIN popup --><span style="background-color: transparent; cursor: pointer;" onclick="openpopup('{URL}*', {WIDTH}, {HEIGHT}, '')"><u>{DESCRIPTION}</u></span><!-- END popup -->
<!-- BEGIN popup_simple --><span style="background-color: transparent; cursor: pointer;" onclick="openpopup('{URL}', 800, 600, '')"><u>{DESCRIPTION}</u></span><!-- END popup_simple -->

<!-- BEGIN table_open --><table cellspacing="0" cellpadding="0"><!-- END table_open -->
<!-- BEGIN table2_open --><table cellspacing="0" cellpadding="0">
<caption>{TABLE_CAPTION}</caption><!-- END table2_open -->
<!-- BEGIN table_close --></table><!-- END table_close -->
<!-- BEGIN row_open --><tr><!-- END row_open -->
<!-- BEGIN row2_open --><tr class="{ROWCLASS}"><!-- END row2_open -->
<!-- BEGIN row_close --></tr><!-- END row_close -->
<!-- BEGIN cell_open --><td><!-- END cell_open -->
<!-- BEGIN cell2_open --><td class="{CELLCLASS}"><!-- END cell2_open -->
<!-- BEGIN cell_close --></td><!-- END cell_close -->
