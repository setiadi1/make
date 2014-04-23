<?php
/**
 * @package ttf-one
 */

if ( ! function_exists( 'ttf_one_mce_buttons_2' ) ) :
/**
 * Activate the Styles dropdown for the Visual editor.
 *
 * @since  1.0.0
 *
 * @param  array    $buttons    Array of activated buttons.
 * @return array                The modified array.
 */
function ttf_one_mce_buttons_2( $buttons ) {
	// Add the styles dropdown
	array_unshift( $buttons, 'styleselect' );

	return $buttons;
}
endif;

add_filter( 'mce_buttons_2', 'ttf_one_mce_buttons_2' );

if ( ! function_exists( 'ttf_one_mce_before_init' ) ) :
/**
 * Add styles to the Styles dropdown.
 *
 * @since  1.0.0
 *
 * @param  array    $settings    TinyMCE settings array.
 * @return mixed                 Modified array.
 */
function ttf_one_mce_before_init( $settings ) {
	$style_formats = array(
		// Big (big)
		array(
			'title' => __( 'Big', 'ttf-one' ),
			'inline' => 'big'
		),
		// Small (small)
		array(
			'title' => __( 'Small', 'ttf-one' ),
			'inline' => 'small'
		),
		// Citation (cite)
		array(
			'title' => __( 'Citation', 'ttf-one' ),
			'inline' => 'cite'
		),
		// Testimonial (blockquote)
		array(
			'title' => __( 'Blockquote: testimonial', 'ttf-one' ),
			'block' => 'blockquote',
			'classes' => 'ttf-one-testimonial',
			'wrapper' => true
		),
		// Line dashed (hr)
		array(
			'title' => __( 'Line: dashed', 'ttf-one' ),
			'selector' => 'hr',
			'attributes' => array(
				'class' => 'ttf-one-line-dashed' // Replace existing classes instead of adding
			),
			'exact' => true
		),
		// Line double (hr)
		array(
			'title' => __( 'Line: double', 'ttf-one' ),
			'selector' => 'hr',
			'attributes' => array(
				'class' => 'ttf-one-line-double' // Replace existing classes instead of adding
			),
			'exact' => true
		),
		// Alert (div)
		array(
			'title' => __( 'Alert', 'ttf-one' ),
			'block' => 'div',
			'attributes' => array(
				'class' => 'ttf-one-alert' // Replace existing classes instead of adding
			),
		),
		// Button (a)
		array(
			'title' => __( 'Button', 'ttf-one' ),
			'selector' => 'a,button',
			'attributes' => array(
				'class' => 'ttf-one-button' // Replace existing classes instead of adding
			)
		),
		// Button download (a)
		array(
			'title' => __( 'Button: download', 'ttf-one' ),
			'selector' => 'a,button',
			'classes' => 'ttf-one-download'
		),
		// Button primary (a)
		array(
			'title' => __( 'Button: primary', 'ttf-one' ),
			'selector' => 'a,button',
			'classes' => 'color-primary-background'
		),
		// Button primary (a)
		array(
			'title' => __( 'Button: secondary', 'ttf-one' ),
			'selector' => 'a,button',
			'classes' => 'color-secondary-background'
		),
		// Success (div)
		array(
			'title' => __( 'Success (Green)', 'ttf-one' ),
			'selector' => 'div,a,button',
			'classes' => 'ttf-one-success',
		),
		// Error (div)
		array(
			'title' => __( 'Error (Red)', 'ttf-one' ),
			'selector' => 'div,a,button',
			'classes' => 'ttf-one-error',
		),
		// Important (div)
		array(
			'title' => __( 'Important (Orange)', 'ttf-one' ),
			'selector' => 'div,a,button',
			'classes' => 'ttf-one-important',
		),
		// List: check 1
		array(
			'title' => __( 'List: checkmark 1', 'ttf-one' ),
			'selector' => 'ul,ol',
			'attributes' => array(
				'class' => 'ttf-one-list ttf-one-list-check' // Replace existing classes instead of adding
			)
		),
		// List: check 2
		array(
			'title' => __( 'List: checkmark 2', 'ttf-one' ),
			'selector' => 'ul,ol',
			'attributes' => array(
				'class' => 'ttf-one-list ttf-one-list-check2' // Replace existing classes instead of adding
			)
		),
		// List: star
		array(
			'title' => __( 'List: star', 'ttf-one' ),
			'selector' => 'ul,ol',
			'attributes' => array(
				'class' => 'ttf-one-list ttf-one-list-star' // Replace existing classes instead of adding
			)
		),
		// List: dot
		array(
			'title' => __( 'List: dot', 'ttf-one' ),
			'selector' => 'ul,ol',
			'attributes' => array(
				'class' => 'ttf-one-list ttf-one-list-dot' // Replace existing classes instead of adding
			)
		),
	);

	// Allow styles to be customized
	$style_formats = apply_filters( 'ttf_one_style_formats', $style_formats );

	// Encode
	$settings['style_formats'] = json_encode( $style_formats );

	return $settings;
}
endif;

add_filter( 'tiny_mce_before_init', 'ttf_one_mce_before_init' );