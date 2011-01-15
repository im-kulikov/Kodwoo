<?php defined('SYSPATH') or die('No direct script access.');

// Load the Twig class autoloader
$file = Kohana::find_file('vendor', 'Dwoo/lib/dwooAutoload');
if (!$file) throw new Kohana_Exception ("Dwoo vendor library not found.");
require_once $file;
