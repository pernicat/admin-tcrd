#!/usr/bin
<?php
require_once realpath('bootstrap.php');

define('DEBUG', true);

/* @var $process TCRD\Process */
$process = $pimp['TCRD.process'];

//$process->run();

//$process->newPositions();
//$process->validatePositionMembers();
//$process->removeUsersFromPositions();
$process->createUsers();

//print $process->app->getNextDomain()->getName();

print "done\n";