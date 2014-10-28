<?php
//PARAMETERS
//login : name of logged in user, null if not logged in
global $locale;
?>
<?php if($_DATA["login"]){ ?>
Logged in as <?=$_DATA["login"]?>. <a href="<?=abspath("/test/logout")?>">Logout</a>
<?php } else { ?>
Not logged in.
<?php } ?>
<h3>LOGIN</h3>
<form method="POST" action="<?=abspath("/test/login_action")?>">
<input type="text" name="email" placeholder="email">
<input type="password" name="password" placeholder="password">
<input type="submit" value="Login">
</form>

<h3>REGISTER</h3>
<form method="POST" action="<?=abspath("/test/register_action")?>">
<input type="text" name="email" placeholder="email">
<input type="text" name="username" placeholder="username">
<input type="password" name="password" placeholder="password">
<input type="submit" value="Register">
</form>