#!/usr/bin/php
<?php
require_once realpath(dirname(__FILE__) . '/bootstrap.php');

//define('DEBUG', true);

define('PRODUCTION', true);

/* @var $process TCRD\Process */
$process = $pimp['TCRD.process'];

//$process->run();

//$process->filterPositions();
//$process->validatePositionMembers();

// $process->unremoveUsers();

// $process->newPositions();
// $process->validatePositionMembers();
 $process->removeUsersFromPositions();
 $process->addUsersToPositions();
// $process->createUsers();

//print $process->app->getNextDomain()->getName();

print "done\n";