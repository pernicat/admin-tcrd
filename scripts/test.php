#!/usr/bin
<?php
require_once realpath('bootstrap.php');

/* @var $clientContainer TCRD\ClientContainer */
$clientContainer = $pimp['TCRD.clientContainer'];

/* @var $client Google_Client */
$client = $clientContainer->getClient();

$directory = new Google_Service_Directory($client);

$users = $directory->users->listUsers(array(
		'domain' => 'tcrollerderby.com',
		'query' => 'isSuspended=false'
));

/* @var $worksheet Google\Spreadsheet\Worksheet */
$worksheet = $pimp['spreadsheet.TCRD_DB.Users'];
$listFeed = $worksheet->getListFeed();


$rosteredUsers = array();

/* @var $entry Google\Spreadsheet\ListEntry */
foreach ($listFeed->getEntries() as $entry) {
	$values = $entry->getValues();
	$rosteredUsers[trim(strtolower($values['tcrde-mail']))] = $entry;
	print $values['tcrde-mail'] . "\n";
}

//print_r($values);

print "\n";

/* @var $exempt array */
$exempt = $pimp['TCRD.exempt'];

/* @var $user Google_Service_Directory_User */
foreach ($users as $user) {
	$email = $user->getPrimaryEmail();
	//print $user->suspended . "\n";
	if (in_array($email, $exempt)) {
		continue;
	}
	
	if (isset($rosteredUsers[$email])) {
		continue;
	}
	
	if ($user->suspended) {
		continue;
	}
	
	print $email . "\n";
	
}
