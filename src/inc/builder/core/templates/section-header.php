<?php
/**
 * @package Make
 */

global $ttfmake_section_data, $ttfmake_is_js_template;

$links = array(
	100 => array(
		'href'  => '#',
		'class' => 'ttfmake-section-remove',
		'label' => __( 'Trash section', 'make' ),
		'title' => __( 'Trash section', 'make' ),
	)
);

if ( ! empty( $ttfmake_section_data['section']['config'] ) ) {
	$links[25] = array(
		'href'  => '#',
		'class' => 'ttfmake-section-configure ttfmake-overlay-open',
		'label' => __( 'Configure section', 'make' ),
		'title' => __( 'Configure section', 'make' ),
		'other' => 'data-overlay="#ttfmake-overlay-{{ id }}"'
	);
}

/**
 * Deprecated: Filter the definitions for the links that appear in each Builder section's footer.
 *
 * This filter is deprecated. Use make_builder_section_links instead.
 *
 * @since 1.0.7.
 * @deprecated 1.4.0.
 *
 * @param array    $links    The link definition array.
 */
$links = apply_filters( 'ttfmake_builder_section_footer_links', $links );
/**
 * Filter the definitions for the buttons that appear in each Builder section's header.
 *
 * @since 1.4.0.
 *
 * @param array    $links    The button definition array.
 */
$links = apply_filters( 'make_builder_section_links', $links );
ksort( $links );
?>

<div class="ttfmake-section{{ get('state') == 'open' && ' ttfmake-section-open' || ''}} ttfmake-section-{{ get('section-type') }}" id="ttfmake-section-{{ get('id') }}" data-id="{{ get('id') }}" data-section-type="{{ get('section-type') }}">
	<?php
	/**
	 * Execute code before the section header is displayed.
	 *
	 * @since 1.2.3.
	 */
	do_action( 'make_before_section_header' );
	?>
	<div class="ttfmake-section-header">
		<h3{{ (get('title')) ? ' class=has-title' : '' }}>
			<span class="ttfmake-section-header-title">{{ get('title') }}</span><em><?php echo ( esc_html( $ttfmake_section_data['section']['label'] ) ); ?></em>
		</h3>
		<div class="ttf-make-section-header-button-wrapper">
			<?php foreach ( $links as $link ) : ?>
				<?php
				$href  = ( isset( $link['href'] ) ) ? ' href="' . esc_url( $link['href'] ) . '"' : '';
				$id    = ( isset( $link['id'] ) ) ? ' id="' . esc_attr( $link['id'] ) . '"' : '';
				$label = ( isset( $link['label'] ) ) ? esc_html( $link['label'] ) : '';
				$title = ( isset( $link['title'] ) ) ? ' title="' . esc_html( $link['title'] ) . '"' : '';
				$other = ( isset( $link['other'] ) ) ? ' ' . $link['other'] : '';

				// Set up the class value with a base class
				$class_base = ' class="ttfmake-builder-section-link';
				$class      = ( isset( $link['class'] ) ) ? $class_base . ' ' . esc_attr( $link['class'] ) . '"' : '"';
				?>
				<a<?php echo $href . $id . $class . $title . $other; ?>>
					<span>
						<?php echo $label; ?>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
		<a href="#" class="ttfmake-section-toggle" title="<?php esc_attr_e( 'Click to toggle', 'make' ); ?>">
			<div class="ttfmake-section-toggle__wrapper">
				<span class="ttfmake-section-toggle__indicator"></span>
			</div>
		</a>
	</div>
	<div class="clear"></div>
	<div class="ttfmake-section-body">
		<input type="hidden" value="<?php echo $ttfmake_section_data['section']['id']; ?>" name="<?php echo ttfmake_get_section_name( $ttfmake_section_data, true ); ?>[section-type]" />
