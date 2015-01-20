#!/usr/bin
<?php
require_once realpath('bootstrap.php');

//define('DEBUG', true);

/* @var $process TCRD\Process */
$process = $pimp['TCRD.process'];

//$process->run();

//$process->unremoveUsers();

//$process->newPositions();
//$process->validatePositionMembers();
//$process->removeUsersFromPositions();
$process->addusersToPositions();
//$process->createUsers();

//print $process->app->getNextDomain()->getName();

print "done\n";