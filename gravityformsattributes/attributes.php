<?php
/*
Plugin Name: Gravity Forms Attributes Add-On
Description: Gravity Forms field that allows for the distribution of "attributes"
Version: 1.0
Author: Top-Node IT
Author URI: https://www.top-node.com
License: GPL-2.0+
Text Domain: gravityformsattributes
Domain Path: /languages

------------------------------------------------------------------------
Copyright 2007-2020 Top-Node IT cc

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

define( 'GF_ATTRIBUTES_VERSION', '1.0' );

add_action( 'gform_loaded', array( 'GF_Attributes_Bootstrap', 'load' ), 5 );

class GF_Attributes_Bootstrap {

	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gf-attributes.php' );

		GFAddOn::register( 'GFAttributes' );
	}
}

function gf_attributes() {
	return GFAttributes::get_instance();
}
