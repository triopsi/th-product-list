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

// Registers the teams post type.
add_action( 'init', 'register_thpl_type' );

// Register new taxonomy.
add_action( 'init', 'register_thpl_taxonomy' );

// Add update messages.
add_filter( 'post_updated_messages', 'thpl_updated_messages' );

// Add new Column.
add_filter( 'manage_edit-thpls_categories_columns', 'thpl_custom_categories_add_new_columns' );

// Adds the shortcode column in the postslistbar.
add_filter( 'manage_thpl_posts_columns', 'add_thpl_columns' );

// Handles shortcode column display.
add_action( 'manage_thpl_posts_custom_column', 'thpl_custom_columns', 10, 2 );

// Add new Column.
add_action( 'manage_thpls_categories_custom_column', 'thpl_custom_categories_columns', 10, 3 );


/**
 * Function about the ini of the Plugin
 *
 * @return void
 */
function register_thpl_type() {

	// Defines labels.
	$labels = array(
		'name'               => __( 'TH Product List', 'thpl' ),
		'singular_name'      => __( 'Product', 'thpl' ),
		'menu_name'          => __( 'TH Product List', 'thpl' ),
		'name_admin_bar'     => __( 'TH Product List', 'thpl' ),
		'add_new'            => __( 'Add New Product', 'thpl' ),
		'add_new_item'       => __( 'Add New Product', 'thpl' ),
		'new_item'           => __( 'New Product', 'thpl' ),
		'edit_item'          => __( 'Edit Product', 'thpl' ),
		'view_item'          => __( 'View Product', 'thpl' ),
		'all_items'          => __( 'All Products', 'thpl' ),
		'search_items'       => __( 'Search Products', 'thpl' ),
		'not_found'          => __( 'No Products found.', 'thpl' ),
		'not_found_in_trash' => __( 'No Products found in Trash.', 'thpl' ),
	);

	// Defines permissions.
	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_admin_bar'  => true,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor' ),
		'menu_icon'          => 'dashicons-columns',
		'query_var'          => true,
		'rewrite'            => false,
	);

	// Registers post type.
	register_post_type( 'thpl', $args );

}

/**
 * Function to register post taxonomies
 */
function register_thpl_taxonomy() {

	$labels = array(
		'name'                       => __( 'Product Categories', 'thpl' ),
		'singular_name'              => __( 'Product Category', 'thpl' ),
		'search_items'               => __( 'Search Product categories', 'thpl' ),
		'all_items'                  => __( 'All Product categories', 'thpl' ),
		'parent_item'                => __( 'Parent Product Category', 'thpl' ),
		'parent_item_colon'          => __( 'Parent Product Category:', 'thpl' ),
		'edit_item'                  => __( 'Edit Product Category', 'thpl' ),
		'update_item'                => __( 'Update Product Category', 'thpl' ),
		'add_new_item'               => __( 'Add New Product Category', 'thpl' ),
		'new_item_name'              => __( 'New Product Category Name', 'thpl' ),
		'separate_items_with_commas' => __( 'Separate Product categories with commas', 'thpl' ),
		'add_or_remove_items'        => __( 'Add or remove Product category', 'thpl' ),
		'choose_from_most_used'      => __( 'Choose from the most used Product categories', 'thpl' ),
		'not_found'                  => __( 'No Product category found.', 'thpl' ),
		'menu_name'                  => __( 'Product Categories', 'thpl' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => true,
	);

	// Register Taxonomies.
	register_taxonomy( 'thpls_categories', array( 'thpl' ), $args );

}

/**
 * Update post message functions
 *
 * @param String $messages Message.
 * @return Array New Array with Message.
 */
function thpl_updated_messages( $messages ) {
	$post              = get_post();
	$post_type         = get_post_type( $post );
	$post_type_object  = get_post_type_object( $post_type );
	$messages['thpl'] = array(
		1  => __( 'Product updated.', 'thpl' ),
		4  => __( 'Product updated.', 'thpl' ),
		6  => __( 'Product published.', 'thpl' ),
		7  => __( 'Product saved.', 'thpl' ),
		10 => __( 'Product draft updated.', 'thpl' ),
	);
	return $messages;
}

/**
 * Shortcodestyle function.
 *
 * @param Array   $column Collumn.
 * @param Integer $post_id Post ID.
 */
function thpl_custom_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'thpl_shortcode':
			global $post;
			$slug      = '';
			$slug      = $post->ID;
			$shortcode = '<span class="shortcode"><input type="text" onfocus="this.select();" readonly="readonly" value="[thpl id=&quot;' . $slug . '&quot;]" class="large-text code"></span>';
			echo $shortcode; // phpcs:ignore
			break;
		case 'thpl_price':
			global $post;
			$price = get_post_meta( $post->ID, '_thpl_price', true );
			$price = ( empty( $price ) ) ? 0 : $price;
			$label = '<span>' . $price . ' €</span>';
			echo $label; // phpcs:ignore
			break;
	}
}


/**
 * Shortcodestyle function.
 *
 * @param String  $string Content.
 * @param Array   $columns Collumn.
 * @param Integer $term_id Post ID.
 */
function thpl_custom_categories_columns( $string, $columns, $term_id ) {
	switch ( $columns ) {
		case 'thpl_cat_shortcode':
			$slug      = get_term( $term_id, 'thpls_categories' );
			$shortcode = '<span class="shortcode"><input type="text" onfocus="this.select();" readonly="readonly" value="[thpl category=' . $slug->slug . ']" class="large-text code"></span>';
			echo $shortcode; // phpcs:ignore
			break;
	}
}

/**
 * Add New collumn.
 *
 * @param Array $columns All Columns.
 * @return Array All Columns with new col.
 */
function thpl_custom_categories_add_new_columns( $columns ) {

	$columns['thpl_cat_shortcode'] = __( 'Shortcode', 'thpl' );
	return $columns;
}

/**
 * AdminCollumnBar function.
 *
 * @param Array $columns Collumn.
 * @return Array Arraymerge.
 */
function add_thpl_columns( $columns ) {
	$columns['title'] = __( 'Products', 'thpl' );
	unset( $columns['author'] );
	unset( $columns['date'] );
	return array_merge( $columns, array( 'thpl_price' => __( 'Price', 'thpl' ), 'thpl_shortcode' => __( 'Shortcode', 'thpl' ) ) );
}

