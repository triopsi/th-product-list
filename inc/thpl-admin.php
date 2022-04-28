<?php
/**
 * Author: triopsi
 * Author URI: http://triopsi-hosting.com
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0
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
 * along with thpl. If not, see https://www.gnu.org/licenses/gpl-2.0.
 *
 * @package thpl
 **/

// Loaded Plugin.
add_action( 'plugins_loaded', 'thpl_check_version' );

/**
 * Version Check.
 */
function thpl_check_version() {
	if ( THPL_VERSION !== get_option( 'thpl_plugin_version' ) ) {
		thpl_activation();
	}
}

/**
 * Update Version Number
 *
 * @return void
 */
function thpl_activation() {
	update_option( 'thpl_plugin_version', THPL_VERSION );
}
