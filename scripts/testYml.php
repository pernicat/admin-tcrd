#!/usr/bin/env php
<?php

$root = dirname(dirname(__FILE__));

require_once realpath($root . '/vendor/autoload.php');


$ymal = new Symfony\Component\Yaml\Parser();

$contents = file_get_contents(realpath($root . '/config/config.yml'));

$config = $ymal->parse($contents);

print_r($config);





print "done\n";
