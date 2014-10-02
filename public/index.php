<?php
  if(isset($_GET["url"])){
    $url = explode("/", $_GET["url"]);
    print_r($url);
  }
?>