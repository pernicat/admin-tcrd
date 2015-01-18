#!/usr/bin
<?php
require_once realpath('../vendor/autoload.php');

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$accessToken = 'ya29._wANFU6dcvanNIv8xB3UXA6KTguMeEDFxDNNEhEd6QHLqo61ymjrOl5nh6BcZB0Tg0fJMP3mTk8c0g';

$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance($serviceRequest);

$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
$spreadsheetFeed = $spreadsheetService->getSpreadsheets();

$spreadsheet = $spreadsheetFeed->getByTitle('TCRD_DB');

//print_r($spreadsheet);

$worksheetFeed = $spreadsheet->getWorksheets();

$worksheet = $worksheetFeed->getByTitle('Users');

//print_r($worksheet);

$listFeed = $worksheet->getListFeed();

//print_r($listFeed->getEntries());

$values = array();
foreach ($listFeed->getEntries() as $entry) {
	$values[] = $entry->getValues();
}

print_r($values);


