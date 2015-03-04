#!/usr/bin/env php
<?php
require_once realpath(dirname(__FILE__) . '/bootstrap.php');

define('DEBUG', true);
//define('PRODUCTION', true);

/* @var $domains \TCRD\Domains */
$domains = $pimp['TCRD.domains'];

print_r($domains);

print "done\n";