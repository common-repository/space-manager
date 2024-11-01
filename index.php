<?php
/*
Plugin Name: Space Manager
Plugin URI: http://brolly.ca
Description: Allows for management of template spaces using the TinyMCE editor.
Version: 1.2.3
Author: Brolly
Author URI: http://brolly.ca

Copyright 2008  DAN_IMBROGNO  (email : dan.imbrogno@brolly.ca)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once('php/space-manager.class.php');
require_once('php/space-manager-widget.class.php');

require_once('space-manager.php');
require_once('space-manager-widget.php');

global $myDefaultSpaceManager;

$myDefaultSpaceManager = new DefaultSpaceManager();

?>