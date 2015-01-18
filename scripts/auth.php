#!/usr/bin/php
<?php
require_once realpath('../vendor/autoload.php');

// project_id angular-yen-828

//$accessToken = 'ya29._wANFU6dcvanNIv8xB3UXA6KTguMeEDFxDNNEhEd6QHLqo61ymjrOl5nh6BcZB0Tg0fJMP3mTk8c0g';
//$refreshToken = '1/YHhr4eM9FBvepsqWdrDQv53-1ljrmAYLREpbwtbpOqQMEudVrK5jSpoR30zcRFq6';
$client = new Google_Client();
// Get your credentials from the console
$client->setClientId('139808716223-o9i4ejdimat95p91s0gmo0tbea4739qk.apps.googleusercontent.com');
$client->setClientSecret('QdK_EQvXlW8yCw7VYDEv-WFO');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
$client->setScopes(array(
		'https://www.googleapis.com/auth/drive',
		'https://spreadsheets.google.com/feeds',
		'https://www.googleapis.com/auth/admin.directory.user',
		'https://www.googleapis.com/auth/admin.directory.group'
));

//$service = new Google_DriveService($client);
if (!isset($accessToken) || (null == $accessToken)) {
	
	$authUrl = $client->createAuthUrl();
	
	//Request authorization
	print "Please visit:\n$authUrl\n\n";
	print "Please enter the auth code:\n";
	$authCode = trim(fgets(STDIN));
	
	// Exchange authorization code for access token
	$accessToken = $client->authenticate($authCode);
	
}

$client->setAccessToken($accessToken);

print_r(json_decode($accessToken, true));
print_r(json_decode($client->getRefreshToken(), true));