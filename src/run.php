<?php

use Ordinal\Chapbook;
use Ordinal\ChapbookException;

try {
	require_once 'vendor/autoload.php';
	$chapbook = new Chapbook();
	$filename = $argv[1];
	echo $chapbook->loadFromFile($filename)->createHtml();
}
catch (ChapbookException $chapbookException) {
	echo 'ERROR: '.$chapbookException->getMessage().PHP_EOL;
	exit(1);
}
