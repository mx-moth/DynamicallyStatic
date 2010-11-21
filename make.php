#!/usr/bin/php5
<?php

$config = null;
// Include the configuration
require('./config.php');

// Include the main application script
require($config['dirs']['core'] . '/DynamicallyStatic.php');

// Include all the utility libraries and superclasses
require($config['dirs']['core'] . '/Inflector.php');
require($config['dirs']['core'] . '/AbstractParser.php');
require($config['dirs']['core'] . '/AbstractRenderer.php');
require($config['dirs']['core'] . '/AbstractModule.php');

// Set the process rolling
new DynamicallyStatic($config);

?>
