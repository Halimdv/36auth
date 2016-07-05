<?php
require 'config.php';
unset($_SESSION['id']);
unset($_SESSION['login']);
setcookie("remember", "", time() -1);
$url = str_replace('logout.php', '', $url);
header('location: '.$url);

