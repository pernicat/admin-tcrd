#!/usr/bin/env php
<?php

$root = dirname(dirname(__FILE__));

require_once 'bootstrap.php';

$config = $pimp['config'];

print_r($config);

print 'done';