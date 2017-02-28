<?php
/**
 * @package Make
 */

global $ttfmake_section_data, $ttfmake_slide_id;

$section_name = "ttfmake-section[{{ get('parentID') }}][banner-slides][{{ id }}]";
$combined_id = "{{ get('parentID') }}-{{ id }}";
$overlay_id  = "ttfmake-overlay-" . $combined_id;
?>

<div class="ttfmake-banner-slide" id="ttfmake-banner-slide-{{ id }}" data-id="{{ id }}" data-section-type="banner-slide">

	<div title="<?php esc_attr_e( 'Drag-and-drop this slide into place', 'make' ); ?>" class="ttfmake-sortable-handle">
		<div class="sortable-background"></div>

		<a href="#" class="ttfmake-configure-item-button" title="Configure banner">
			<span>Configure options</span>
		</a>
	</div>

	<ul class="configure-item-dropdown">
		<li>
			<a href="#" class="edit-content-link ttfmake-icon-pencil{{ get('content') && get('content').length ? ' item-has-content': '' }}" title="<?php esc_attr_e( 'Edit content', 'make' ); ?>" data-textarea="ttfmake-content-<?php echo $combined_id; ?>">
				<?php esc_html_e( 'Edit content', 'make' ); ?>
			</a>
		</li>
		<li>
			<a href="#" class="ttfmake-icon-cog ttfmake-banner-slide-configure ttfmake-overlay-open" title="<?php esc_attr_e( 'Configure slide', 'make' ); ?>" data-overlay="#<?php echo $overlay_id; ?>">
				<?php esc_html_e( 'Configure slide', 'make' ); ?>
			</a>
		</li>
		<li>
			<a href="#" class="ttfmake-icon-trash ttfmake-banner-slide-remove" title="<?php esc_attr_e( 'Trash slide', 'make' ); ?>">
				<?php esc_html_e( 'Trash slide', 'make' ); ?>
			</a>
		</li>
	</ul>

	<?php echo ttfmake_get_builder_base()->add_uploader( $section_name, 0, __( 'Set banner image', 'make' ), 'image-url' ); ?>
	<?php ttfmake_get_builder_base()->add_frame( $combined_id, 'content', '', '', false ); ?>

	<?php
	global $ttfmake_overlay_class, $ttfmake_overlay_id, $ttfmake_overlay_title;
	$ttfmake_overlay_class = 'ttfmake-configuration-overlay';
	$ttfmake_overlay_id    = $overlay_id;
	$ttfmake_overlay_title = __( 'Configure slide', 'make' );

	get_template_part( '/inc/builder/core/templates/overlay', 'header' );

	// Print the inputs
	$inputs = $ttfmake_section_data['section']['slide'];
	$output = '';

	foreach ( $inputs as $input ) {
		if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
			$output .= ttfmake_create_input( $section_name, $input, array() );
		}
	}

	echo $output;

	get_template_part( '/inc/builder/core/templates/overlay', 'footer' );
	?>
</div>
