<?php
//PARAMETERS
//title : title of the webpage
//body : body of the page
?>

<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="<?=abspath("public/css/bootstrap.min.css")?>">
		<script type='text/javascript' src="<?=abspath("public/js/jquery-2.1.1.min.js")?>"></script>
		<script type='text/javascript' src="<?=abspath("public/js/bootstrap.min.js")?>"></script>
		<link rel="shortcut icon" href="<?=abspath("public/images/favicon.ico")?>" type="image/x-icon">
		<link rel="icon" href="<?=abspath("public/images/favicon.ico")?>" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="<?=abspath("public/css/styles.css")?>">
    <title><?=$_DATA["title"];?></title>
</head>    
<body>
    <?php echo $_DATA["body"]; ?>
</body>
</html>