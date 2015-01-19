#!/usr/bin
<?php
require_once realpath('bootstrap.php');

define('DEBUG', true);

/* @var $process TCRD\Process */
$process = $pimp['TCRD.process'];

$process->run();

//$process->newPositions();
//$process->validatePositionMembers();
//$process->removeUsersFromPositions();

print "done\n";