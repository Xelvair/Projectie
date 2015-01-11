<?php
//PARAMETERS
//title : title of the webpage
//body : body of the page
//body_padding : whether to add padding to the navbar
//current_user : assoc array of all information about the current user
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
        <link rel="stylesheet" type="text/css" href="<?=abspath("public/css/fileinput.css")?>">
    <script src='<?=abspath("public/js/Projectie.js")?>'></script>
    <script>Projectie.server_addr = "<?=abspath("")?>"</script>
    <?php if(isset($_DATA["current_user"])){ ?>
        <script>Projectie.current_user = <?=json_encode($_DATA["current_user"])?></script>
    <?php } ?>
    <script src='<?=abspath("public/js/chat.js")?>'></script>
    <script src="<?=abspath("public/js/fileinput.js")?>"></script>
    <script src="<?=abspath("public/js/TagBox.js")?>"></script>
    <script src="<?=abspath("public/js/ParticipationList.js")?>"></script>
    <title><?=$_DATA["title"];?></title>
</head>    
<body <?php if($_DATA["body_padding"]){echo "class='body-padding'";} ?>>
    <?php echo $_DATA["body"]; ?>
</body>
</html>