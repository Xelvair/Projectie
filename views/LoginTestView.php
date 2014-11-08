<?php
//PARAMETERS
//login : name of logged in user, null if not logged in
global $locale;
?>
<?php if($_DATA["login"]){ ?>
Logged in as <?=$_DATA["login"]?>. <a href="<?=abspath("/auth/logout&redirect=test/auth")?>">Logout</a>
<?php } else { ?>
Not logged in.
<?php } ?>
<h3>LOGIN</h3>
<form method="POST" action="<?=abspath("/auth/login&redirect=test/auth")?>">
<input type="text" name="email" placeholder="email">
<input type="password" name="password" placeholder="password">
<input type="submit" value="Login">
</form>

<h3>REGISTER</h3>
<form method="POST" action="<?=abspath("/auth/register&redirect=test/auth")?>">
<input type="text" name="email" placeholder="email">
<input type="text" name="username" placeholder="username">
<input type="text" name="lang" = placeholder="language">
<input type="password" name="password" placeholder="password">
<input type="submit" value="Register">
</form>