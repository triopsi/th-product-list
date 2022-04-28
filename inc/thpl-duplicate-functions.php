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

// Add new Action in the list.
add_filter( 'post_row_actions', 'thpl_duplicate_post_link', 10, 2 );

// Function creates post duplicate as a draft and redirects then to the edit post screen.
add_action( 'admin_action_thpl_duplicate_post_as_draft', 'thpl_duplicate_post_as_draft' );

// In case we decided to add admin notices.
add_action( 'admin_notices', 'thpl_duplication_admin_notice' );


/**
 * Duplicate Link.
 *
 * @param array   $actions. Array of Actions.
 * @param WP_Post $post Post.
 * @return array
 */
function thpl_duplicate_post_link( $actions, $post ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return $actions;
	}

	if ( 'thpl' === $post->post_type ) {

		$url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'thpl_duplicate_post_as_draft',
					'post'   => $post->ID,
				),
				'admin.php'
			),
			basename( __FILE__ ),
			'duplicate_nonce'
		);

		$actions['duplicate'] = '<a href="' . $url . '" title="' . __( 'Duplicate this product', 'thpl' ) . '" rel="permalink">' . __( 'Duplicate', 'thpl' ) . '</a>';
	}
	return $actions;
}

/**
 * Duplicate Function.
 *
 * @return string
 */
function thpl_duplicate_post_as_draft() {

	// check if post ID has been provided and action.
	if ( empty( $_GET['post'] ) ) {
		wp_die( 'No post to duplicate has been provided!' );
	}

	// Nonce verification.
	if ( ! isset( $_GET['duplicate_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['duplicate_nonce'] ), basename( __FILE__ ) ) ) { //phpcs:ignore
		return;
	}

	// Get the original post id.
	$post_id = absint( $_GET['post'] );

	// And all the original post data then.
	$post = get_post( $post_id );

	/*
	 * if you don't want current user to be the new post author,
	 * then change next couple of lines to this: $new_post_author = $post->post_author;
	 */
	$current_user    = wp_get_current_user();
	$new_post_author = $current_user->ID;

	// if post data exists (I am sure it is, but just in a case), create the post duplicate.
	if ( $post ) {

		// new post data array.
		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order,
		);

		// insert the post by wp_insert_post() function.
		$new_post_id = wp_insert_post( $args );

		/*
		 * get all current post terms ad set them to the new post draft.
		 */
		$taxonomies = get_object_taxonomies( get_post_type( $post ) );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}
		}

		// duplicate all post meta.
		$post_meta = get_post_meta( $post_id );
		if ( $post_meta ) {

			foreach ( $post_meta as $meta_key => $meta_values ) {

				if ( '_wp_old_slug' == $meta_key ) { // do nothing for this meta key.
					continue;
				}

				foreach ( $meta_values as $meta_value ) {
					add_post_meta( $new_post_id, $meta_key, $meta_value );
				}
			}
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'post_type' => ( 'post' !== get_post_type( $post ) ? get_post_type( $post ) : false ),
					'saved'     => 'post_duplication_created', // just a custom slug here.
				),
				admin_url( 'edit.php' )
			)
		);
		exit;

	} else {
		wp_die( 'Post creation failed, could not find original post.' );
	}

}

/**
 * Admin Notice.
 *
 * @return string
 */
function thpl_duplication_admin_notice() {

	// Get the current screen.
	$screen = get_current_screen();

	if ( 'edit' !== $screen->base ) {
		return;
	}

	// Checks if settings updated.
	if ( isset( $_GET['saved'] ) && 'post_duplication_created' == $_GET['saved'] ) { // phpcs:ignore
		 echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Post copy created.', 'thpl' ) . '</p></div>';
	}
}
