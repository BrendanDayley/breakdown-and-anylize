<?php
	/* $_body="document.loginform.username.focus()"; */
	include("header.php");
?>
<br />
<form name="loginform" method="post" action="">
<div class="border">
<table class="admintable" width="90%" style="border-collapse: collapse; background-color: white;" cellpadding="0" cellspacing="0">
	<tr><td class="tablehead" style="padding-left: 5px;" colspan=2><?php echo(__("Please log in to access this page")); ?></td></tr>
	<tr>
		<td>
			<table style="padding-left: 5px;">
				<tr><td colspan="2">&nbsp;<span class="warning"><?php echo($_SESSION["API_global_error"]); ?></span></td></tr>
				<tr><td width="10%" align="right"><label for="username"><?php echo(__("Username")); ?></label>: </td><td style="text-align: left;"><input type="text" name="username" id="username" title="username" required></td></tr>
				<tr><td align="right"><label for="password"><?php echo(__("Password")); ?></label>: </td><td style="text-align: left;"><input type="password" title="password" name="password" id="password" required></td></tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2"><?php echo(__("If you have lost or forgotten your password please ")); ?><a href="../lost_pass.php"><?php echo(__("click here")); ?></a>!</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
			</table>
		</td>
	</tr>
	<tr><td class="tablehead" colspan="2" style="text-align: center;"><input type="submit" name="submit" value="<?php echo(__("Login")); ?>"></td></tr>
</table>
</div>
</form>
<br />
<?php
    $sql = "SELECT * FROM widgets WHERE clientstyle = :cstyle ORDER BY displayorder";
    $params = array('cstyle' => $API_CONFIG["clientstyle"]);
	$widgets = api_DoRemoteSQL($API_CONFIG["database"],$sql,$params);
	foreach ($widgets as $widg){
        include($API_CONFIG["base"]."/widgets/".$widg['file']);
    }// each widget foreach loop
?>
<script>
<!--
    document.getElementById('username').focus();
-->
</script>
<?php
	include("footer.php");
?>