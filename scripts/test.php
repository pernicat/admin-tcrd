#!/usr/bin
<?php
require_once realpath('bootstrap.php');


/* @var $app TCRD\App */
$app = $pimp['TCRD.app'];

$users = $app->getRemoveList();

/* @var $user Google_Service_Directory_User */
foreach ($users as $user) {
	print $user->getPrimaryEmail() . "\n";
}

