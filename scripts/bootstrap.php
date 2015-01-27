<?php
use TCRD\Wrapper\ClientWrapper;
use TCRD\Domains;

define('ROOT', dirname(dirname(__FILE__)));

require_once realpath(ROOT . '/vendor/autoload.php');

$pimp = new Pimple\Container();

$pimp['config'] = function ($c) {
	$ymal = new Symfony\Component\Yaml\Parser();
	
	$contents = file_get_contents(realpath(ROOT . '/config/config.yml'));
	
	return $ymal->parse($contents);
};

$pimp['TCRD.clients'] = function ($c) {
	$domainConfigs = $c['config']['domains'];
	
	$clients = array();
	
	foreach ($domainConfigs as $key => $config) {
		$client = new ClientWrapper(new Google_Client());
		
		$client->configure($config['client']);
		
		$clients[] = $client;
	}
	
	return $clients;
};