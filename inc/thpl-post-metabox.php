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

// Hooks the metabox.
add_action( 'admin_init', 'thpl_add_promo', 1 );

// Save Post Action Hook.
add_action( 'save_post', 'thpl_save_meta_box_data' );

/**
 * Add Meta Box.
 *
 * @return void
 */
function thpl_add_promo() {

	add_meta_box(
		'thpl-details-container',
		__( 'Details', 'thpl' ),
		'thpl_add_setting_field_display',
		'thpl',
		'normal'
	);
}


/**
 * Show the add/edit postpage in admin
 *
 * @param WP_Post $post Actual Post.
 * @return void
 */
function thpl_add_setting_field_display( $post ) {

	// Hidden field.
	wp_nonce_field( 'thpl_meta_box_nonce', 'thpl_meta_box_nonce' );
	?>
	<div class="thpl_field">

		<!-- Price -->
		<div class="form-container">
			<div class="thpl_field_title">
				<?php
				// Get post meta data.
				$price = get_post_meta( $post->ID, '_thpl_price', true );
				$price = ( empty( $price ) ) ? 0 : $price;

				// Field.
				echo esc_html__( 'Price', 'thpl' );
				?>
				<span style="color:red;">*</span>
			</div>
			<input class="thpl-field regular-number" id="thpl-price" name="thpl_price" step="0.01" type="number" value="<?php echo esc_attr( $price ); ?>" placeholder="0.00">
		</div>
		<!-- /Price -->

		<!-- Price Suffix -->
		<div class="form-container">
			<div class="thpl_field_title">
				<?php
				// Get post meta data.
				$suffix = get_post_meta( $post->ID, '_thpl_price_suffix', true );
				$suffix = ( empty( $suffix ) ) ? '' : $suffix;

				// Field.
				echo esc_html__( 'Price Suffix', 'thpl' );
				?>
				<span style="color:red;">*</span>
			</div>
			<input class="thpl-field regular-text" id="thpl-price-suffix" name="thpl_price_suffix" type="text" value="<?php echo esc_attr( $suffix ); ?>" placeholder="<?php esc_html_e( '30 days', 'thpl' ); ?>">
		</div>
		<!-- /Price Suffix -->

		<hr>

		<!-- Button Text -->
		<div class="form-container">
			<div class="thpl_field_title">
				<?php
				// Get post meta data.
				$button_text = get_post_meta( $post->ID, '_thpl_button_text', true );
				$button_text = ( empty( $button_text ) ) ? __( 'Order', 'thpl' ) : $button_text;

				// Field.
				echo esc_html__( 'Button Text', 'thpl' );
				?>
				<span style="color:red;">*</span>
			</div>
			<input class="thpl-field regular-text" id="thpl-button-text" name="thpl_button_text" type="text" value="<?php echo esc_attr( $button_text ); ?>" placeholder="<?php esc_html_e( 'Order', 'thpl' ); ?>">
		</div>
		<!-- /Button Text -->

		<!-- Url Order -->
		<div class="form-container">
			<div class="thpl_field_title">
				<?php
				// Get post meta data.
				$url_order = get_post_meta( $post->ID, '_thpl_url_order', true );
				$url_order = ( empty( $url_order ) ) ? '' : $url_order;

				// Field.
				echo esc_html__( 'Order Url', 'thpl' );
				?>
				<span style="color:red;">*</span>
			</div>
			<input class="thpl-field regular-text" id="thpl-url-order" name="thpl_url_order" type="url" value="<?php echo esc_attr( $url_order ); ?>" placeholder="<?php esc_html_e( 'https://', 'thpl' ); ?>">
		</div>
		<!-- /Url Order -->
		<hr>

		<!-- Attributes -->
		<div class="form-container">
			<div class="thpl_field_title">
				<?php
				// Get post meta data.
				$attributes = get_post_meta( $post->ID, '_thpl_attributes', true );
				$empty_att  = ( empty( $attributes ) ) ? true : false;
				$attributes = ( empty( $attributes ) ) ? 'Atrribute : Value |' : $attributes;

				// Field.
				echo esc_html__( 'Atributes', 'thpl' );
				?>
				<?php
				if ( ! $empty_att ) {
					$attribute_array = explode( '|', $attributes );
					$htmlout         = '<ul class="list-unstyled">';
					foreach ( $attribute_array as $attrubute ) {
						$attribute_values = explode( ':', $attrubute, 2 );
						$htmlout         .= '<li>
									<b>' . $attribute_values[0] . '</b> : 
									' . $attribute_values[1] . '
								</li>';
					}
					$htmlout .= '</ul>';
					echo $htmlout; // phpcs:ignore
					echo esc_html__( 'Edit', 'thpl' );
				}
				?>
			</div>
			<textarea class="thpl-attributes-field" rows=5 style="width:100%;" id="thpl-attributes" name="thpl_attributes" placeholder="<?php esc_html_e( 'Atrribute : Value |', 'thpl' ); ?>"><?php echo esc_attr( $attributes ); ?></textarea>
		</div>
		<!-- /Attributes -->

		<br>
		<div style="width:100%;text-align: center;font-size: 3em;color:#ddd;" id="th-promo-panels-icon-review"></div>
		<em><span style="color:red;">*</span> <?php echo esc_html__( 'Required fields', 'thpl' ); ?></em>
	</div>
	<?php
}

/**
 * Save Post actions.
 *
 * @param Integer $post_id Post ID.
 * @return void
 */
function thpl_save_meta_box_data( $post_id ) {

	if ( ! isset( $_POST['thpl_meta_box_nonce'] ) ) { // phpcs:ignore
		return;
	}
	if ( ! wp_verify_nonce( $_POST['thpl_meta_box_nonce'], 'thpl_meta_box_nonce' ) ) { // phpcs:ignore
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['post_type'] ) && 'thpl' === $_POST['post_type'] ) { // phpcs:ignore

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	if ( ! isset( $_POST['thpl_price'] ) ) { // phpcs:ignore
		return;
	}

	// Save Attributes.
	$thpl_price = stripslashes( wp_strip_all_tags( sanitize_text_field( $_POST['thpl_price'] ) ) ); // phpcs:ignore
	$thpl_price_suffix = stripslashes( wp_strip_all_tags( sanitize_text_field( $_POST['thpl_price_suffix'] ) ) ); // phpcs:ignore
	$thpl_button_text = stripslashes( wp_strip_all_tags( sanitize_text_field( $_POST['thpl_button_text'] ) ) ); // phpcs:ignore
	$thpl_url_order = stripslashes( wp_strip_all_tags( sanitize_text_field( $_POST['thpl_url_order'] ) ) ); // phpcs:ignore
	$thpl_attributes = stripslashes( wp_strip_all_tags( sanitize_text_field( $_POST['thpl_attributes'] ) ) ); // phpcs:ignore

	update_post_meta( $post_id, '_thpl_price', $thpl_price );
	update_post_meta( $post_id, '_thpl_price_suffix', $thpl_price_suffix );
	update_post_meta( $post_id, '_thpl_button_text', $thpl_button_text );
	update_post_meta( $post_id, '_thpl_url_order', $thpl_url_order );
	update_post_meta( $post_id, '_thpl_attributes', $thpl_attributes );
}
