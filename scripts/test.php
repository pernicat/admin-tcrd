#!/usr/bin
<?php
require_once realpath('bootstrap.php');


/* @var $process TCRD\Process */
$process = $pimp['TCRD.process'];

//$process->newPositions();
$process->validatePositionMembers();
$process->removeUsersFromPositions();

print "done\n";