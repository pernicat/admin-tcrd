#!/usr/bin
<?php
require_once realpath('bootstrap.php');


/* @var $app TCRD\App */
$app = $pimp['TCRD.app'];

//$users = $app->listRemovedUsers();
//$users = $app->listMissmatchUsers();
//$users = $app->getSuspendedUsers();
//$users = $app->findMissmatchUsers();

$users = $app->listUnsuspendedUsers();


/* @var $user Google_Service_Directory_User */
foreach ($users as $user) {
	//$user->customerId
	print $user->getPrimaryEmail() . "\n";
	$user->setSuspended(false);
	$app->updateDomainUser($user);
}

print "done\n";