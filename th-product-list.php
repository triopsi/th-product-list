<?php
/**
 * Plugin Name: TH Product List
 * Plugin URI: https://triopsi-hosting.com
 * Description: A simple plugin to present products individually in a product table.
 * Version: 1.0.0
 * Author: triopsi
 * Author URI: https://triopsi-hosting.com
 * Text Domain: thpl
 * Domain Path: /lang/
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0
 *
 * Thpl is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Thpl is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with thpp. If not, see https://www.gnu.org/licenses/gpl-3.0.
 *
 * @package thpp
 **/

// Definie plugin version.
if ( ! defined( 'THPL_VERSION' ) ) {
	define( 'THPL_VERSION', '1.0.0' );
}

define( 'THPL_PLUGIN_FILE', __FILE__ );

/* Loads plugin's text domain. */
add_action( 'init', 'thpl_load_plugin_textdomain' );

// Add Admin Actions.
require_once 'inc/thpl-admin.php';
require_once 'inc/thpl-types.php';
require_once 'inc/thpl-post-metabox.php';
require_once 'inc/thpl-duplicate-functions.php';


// Shortcode.
require_once 'inc/thpl-shortcode.php';

/**
 * Init Script. Load languages
 *
 * @return void
 */
function thpl_load_plugin_textdomain() {
	load_plugin_textdomain( 'thpl', '', 'th-product-list/lang/' );
}
