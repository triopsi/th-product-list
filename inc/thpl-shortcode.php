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

// Add Scripts.
add_action( 'wp_enqueue_scripts', 'add_admin_thpl_style_js' );

// Shortcode on the Page.
add_shortcode( 'thpl', 'thpl_sh' );

/**
 * Undocumented function
 *
 * @return void
 */
function add_admin_thpl_style_js() {

	wp_register_style( 'th-product-list-fronted-style', plugins_url( 'assets/css/front-style.css', THPL_PLUGIN_FILE ), array(), THPL_VERSION, 'all' );
	wp_enqueue_style( 'th-product-list-fronted-style' );
}

/**
 * Show the Shortcode in the post/site/content.
 *
 * @param array $atts All Attributes.
 * @return string HTML Code.
 */
function thpl_sh( $atts ) {

	// Data of the current Post.
	global $post;

	// Shortcode Parameter.
	// phpcs:ignore
	extract(
		shortcode_atts(
			array(
				'orderby'    => 'date',
				'order'      => 'ASC',
				'id'         => '',
				'category'   => '',
				'columns'    => '',
				'css'        => '',
				'css_header' => '',
			),
			$atts
		)
	);

	$order      = ( strtolower( $order ) === 'asc' ) ? 'ASC' : 'DESC';
	$orderby    = ! empty( $orderby ) ? $orderby : 'date';
	$id         = ! empty( $id ) ? $id : '';
	$category   = ! empty( $category ) ? $category : '';
	$columns    = ! empty( $columns ) ? $columns : 'col-md-4';
	$css        = ! empty( $css ) ? $css : '';
	$css_header = ! empty( $css_header ) ? $css_header : '';

	$options = array(
		'columns'    => $columns,
		'css'        => $css,
		'css_header' => $css_header,
	);

	// WP Query Parameters.
	$query_args = array(
		'post_type'      => 'thpl',
		'post_status'    => array( 'publish' ),
		'posts_per_page' => -1,
		'order'          => $order,
		'orderby'        => $orderby,
	);

	// search single product.
	if ( ! empty( $id ) ) {
		$query_args['p'] = $id;
	}

	// Search with category.
	if ( ! empty( $category ) ) {
		$query_args['tax_query'] = array( // phpcs:ignore
			array(
				'taxonomy' => 'thpls_categories',
				'field'    => 'name',
				'terms'    => $category,
			),
		);
	}

	// WP Query Parameters.
	$thpl_query = new WP_Query( $query_args );

	// Default Output.
	$htmlout = '';

	if ( $thpl_query->have_posts() ) {
		ob_start();
		$o        = ob_get_clean();
		$htmlout .= thpl_get_output_list( $thpl_query, $post, $options );
	}
	wp_reset_postdata(); // Reset WP Query.
	return $o . $htmlout;

}


/**
 * Get HTMl Code.
 *
 * @param Object  $thpl_query Array of questions.
 * @param WC_Post $post Acutal Post.
 * @param array   $options Array of options.
 * @return String HTML Code.
 */
function thpl_get_output_list( $thpl_query, $post, $options ) {

	$htmlout = '<!-- Start Triopsi Hosting Product List -->';

	if ( $thpl_query->have_posts() ) {

		// itteration.
		$i = 0;

		$htmlout .= '<div class="d-flex justify-content-around justify-content-center flex-wrap th-product-list mb-5 mt-5 ' . $options['css'] . '">';

		// Outputt all Services.
		foreach ( $thpl_query->get_posts() as $product ) :

			$iduid = uniqid();

			// Get the ID.
			$id_prodcut = $iduid . $product->ID;

			// Get the title.
			$title_faq = $product->post_title;

			// Get the body.
			$body_thpl = $product->post_content;

			// Get the Price.
			$thpl_price = get_post_meta( $product->ID, '_thpl_price', true );
			$thpl_price = ( ! empty( $thpl_price ) ) ? $thpl_price : 0;

			// Get the Suffix.
			$thpl_price_suffix = get_post_meta( $product->ID, '_thpl_price_suffix', true );
			$thpl_price_suffix = ( ! empty( $thpl_price_suffix ) ) ? $thpl_price_suffix : __( '30 Days', 'thpl' );

			// Get the Order Url.
			$thpl_url_order = get_post_meta( $product->ID, '_thpl_url_order', true );
			$thpl_url_order = ( ! empty( $thpl_url_order ) ) ? $thpl_url_order : '#';

			// Get the Button Text.
			$thpl_button_text = get_post_meta( $product->ID, '_thpl_button_text', true );
			$thpl_button_text = ( ! empty( $thpl_button_text ) ) ? $thpl_button_text : __( 'Order', 'thpl' );

			// Get the Atrributes.
			$thpl_attributest = get_post_meta( $product->ID, '_thpl_attributes', true );
			$thpl_attributest = ( ! empty( $thpl_attributest ) ) ? $thpl_attributest : '';

			// itteration high.
			$i++;

			$htmlout .= '<!--' . $i . '-->';
			$htmlout .= '<div class="th-product-in-list ' . $options['columns'] . ' mb-2">';
			$htmlout .= '<div class="card shadow rounded">';
			$htmlout .= '<div class="card-header text-center">';
			$htmlout .= '<h4 class="th-product-title ' . $options['css_header'] . '">' . esc_html( $title_faq ) . '</h4>';
			$htmlout .= '</div>';
			$htmlout .= '<div class="card-body py-3">';
			$htmlout .= $body_thpl;
			$htmlout .= '<div class="d-flex justify-content-center mt-3 mb-4">';
			$htmlout .= '<span class="text-muted">ab</span>';
			$htmlout .= '<span class="price h1 mb-0 text-triopsi">' . number_format( $thpl_price, 2, ',' ) . '&nbsp;€</span>';
			$htmlout .= '<span class="h4 align-self-end mb-1 text-muted" style="font-size: small">&nbsp;/' . $thpl_price_suffix . '</span>';
			$htmlout .= '</div>';
			$htmlout .= '<div class="thpl-order-btn mt-1">';
			$htmlout .= '<a class="btn btn-block text-white" href="' . $thpl_url_order . '">' . $thpl_button_text . '</a>';
			$htmlout .= '</div>';
			$htmlout .= thpl_get_atributes_html( $thpl_attributest );
			$htmlout .= '</div>';
			$htmlout .= '</div>';
			$htmlout .= '</div>';

		endforeach;

		$htmlout .= '</div>';
	}
	$htmlout .= '<!-- End Triopsi Hosting Product List -->';
	return $htmlout;
}

/**
 * Get the Attributes in html list.
 *
 * @param string $attrubites Attributes.
 * @return string
 */
function thpl_get_atributes_html( $attrubites ) {
	if ( empty( $attrubites ) ) {
		return '';
	}
	$attribute_array = explode( '|', $attrubites );
	$htmlout         = '<hr><ul class="list-unstyled mb-0 pl-0">';
	foreach ( $attribute_array as $attrubute ) {
		$attribute_values = explode( ':', $attrubute, 2 );
		$htmlout         .= '<li class="h6 text-muted">
					<span class="text-triopsi h5 mr-2"><i class="fas fa-check align-middle"></i></span>
					<b>' . $attribute_values[0] . '</b> : 
					' . $attribute_values[1] . '
				</li>';
	}
	$htmlout .= '</ul>';

	return $htmlout;
}
