<?php
use TCRD\Wrapper\ClientWrapper;
use TCRD\Domains;
use TCRD\Domain;
use TCRD\File\FileAccess;

define('ROOT', dirname(dirname(__FILE__)));

require_once realpath(ROOT . '/vendor/autoload.php');

$pimp = new Pimple\Container();

$pimp['config'] = function ($c) {
	$ymal = new Symfony\Component\Yaml\Parser();
	
	$contents = file_get_contents(realpath(ROOT . '/config/config.yml'));
	
	return $ymal->parse($contents);
};

$pimp['TCRD.domains'] = function ($c) {
	$domainConfigs = $c['config']['domains'];
	
	$domains = new Domains();
	
	foreach ($domainConfigs as $key => $config) {
		$client = new ClientWrapper(new Google_Client());
		$client->configure($config['client']);
		
		$accesTokenFile = new FileAccess(
				ROOT . $config['access_token_file_location']);
		$client->setAccessTokenFile($accesTokenFile);
		
		$directory = new Google_Service_Directory($client->getClient());
		$domain = new Domain($directory, $key);
		$domains->addDomain($domain);
		
		
	}
	
	return $domains;
};

