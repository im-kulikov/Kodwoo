<?php defined('SYSPATH') or die('No direct script access.');
/* Copyright (c) 2011, Tyler Larson. All rights reserved.
 * Use and distribution permitted under the terms of the MIT license.
 * See LICENSE.txt for details.
 */

// Load the Twig class autoloader
$file = Kohana::find_file('vendor', 'Dwoo/lib/dwooAutoload');
if (!$file) throw new Kohana_Exception ("Dwoo library not found.\nDownload from dwoo.org and extract to modules/kodwoo/vendor/Dwoo");
require_once $file;
