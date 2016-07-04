<?php
require 'config.php';
unset($_SESSION['id']);
unset($_SESSION['login']);
$url = str_replace('logout.php', '', $url);
header('location: '.$url);

