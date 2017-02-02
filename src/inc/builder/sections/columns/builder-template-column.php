<?php
global $ttfmake_section_data;

$section_name   = 'ttfmake-section[{{ get("parentID") }}][columns][{{ get("id") }}]';
$combined_id = "{{ get('parentID') }}-{{ get('id') }}";
$overlay_id  = "ttfmake-overlay-" . $combined_id;

?>
<?php
	$column_name = $section_name . '[columns][{{ get("id") }}]';
	$iframe_id = 'ttfmake-iframe-'. $combined_id;
	$textarea_id = 'ttfmake-content-'. $combined_id;
	$link        = '{{ get("image-link") }}';
	$image_id    = '{{ get("image-id") }}';
	$title       = '{{ get("title") }}';
	$content     = '{{ get("content") }}';

	$column_buttons = array(
		100 => array(
			'label'              => __( 'Configure column', 'make' ),
			'href'               => '#',
			'class'              => 'configure-column-link ttfmake-overlay-open',
			'title'              => __( 'Configure column', 'make' ),
			'other-a-attributes' => ' data-overlay="#' . $overlay_id .'"',
		),
		200 => array(
			'label'              => __( 'Edit text column', 'make' ),
			'href'               => '#',
			'class'              => 'edit-content-link edit-text-column-link {{ (get("content")) ? "item-has-content" : "" }}',
			'title'              => __( 'Edit content', 'make' ),
			'other-a-attributes' => 'data-textarea="' . $textarea_id . '" data-iframe="' . $iframe_id . '"',
		),
		300 => array(
			'label'              => __( 'Remove text column', 'make' ),
			'href'               => '#',
			'class'              => 'remove-text-column-link ttfmake-text-column-remove',
			'title'              => __( 'Remove column', 'make' )
		)
	);

	/**
	 * Filter the buttons added to a text column.
	 *
	 * @since 1.4.0.
	 *
	 * @param array    $column_buttons          The current list of buttons.
	 * @param array    $ttfmake_section_data    All data for the section.
	 */
	$column_buttons = apply_filters( 'make_column_buttons', $column_buttons, $ttfmake_section_data );
	ksort( $column_buttons );

	/**
	 * Filter the classes applied to each column in a Columns section.
	 *
	 * @since 1.2.0.
	 *
	 * @param string    $column_classes          The classes for the column.
	 * @param int       $i                       The column number.
	 * @param array     $ttfmake_section_data    The array of data for the section.
	 */
	$column_classes = apply_filters( 'ttfmake-text-column-classes', 'ttfmake-text-column', $ttfmake_section_data );
?>

<div class="ttfmake-text-column{{ (get('size')) ? ' ttfmake-column-width-'+get('size') : '' }}" data-id="{{ get('id') }}">
	<div title="<?php esc_attr_e( 'Drag-and-drop this column into place', 'make' ); ?>" class="ttfmake-sortable-handle">
		<div class="sortable-background column-sortable-background"></div>
	</div>

	<?php
	/**
	 * Execute code before an individual text column is displayed.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $ttfmake_section_data    The data for the section.
	 */
	do_action( 'make_section_text_before_column', $ttfmake_section_data );
	?>

	<?php foreach ( $column_buttons as $button ) : ?>
		<a href="<?php echo esc_url( $button['href'] ); ?>" class="column-buttons <?php echo esc_attr( $button['class'] ); ?>" title="<?php echo esc_attr( $button['title'] ); ?>" <?php if ( ! empty( $button['other-a-attributes'] ) ) echo $button['other-a-attributes']; ?>>
			<span>
				<?php echo esc_html( $button['label'] ); ?>
			</span>
		</a>
	<?php endforeach; ?>

	<?php echo ttfmake_get_builder_base()->add_uploader( $column_name, 0, __( 'Set image', 'make' ), 'image-url' ); ?>
	<?php ttfmake_get_builder_base()->add_frame( $combined_id, 'content', '', $content ); ?>

	<?php
	/**
	 * Execute code after an individual text column is displayed.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $ttfmake_section_data    The data for the section.
	 */
	do_action( 'make_section_text_after_column', $ttfmake_section_data );
	?>

	<?php
	global $ttfmake_overlay_class, $ttfmake_overlay_id, $ttfmake_overlay_title;
	$ttfmake_overlay_class = 'ttfmake-configuration-overlay';
	$ttfmake_overlay_id    = $overlay_id;
	$ttfmake_overlay_title = __( 'Configure column', 'make' );

	get_template_part( '/inc/builder/core/templates/overlay', 'header' );

	/**
	 * Filter the definitions of the Columns section's column configuration inputs.
	 *
	 * @since 1.4.0.
	 *
	 * @param array    $inputs    The input definition array.
	 */
	$inputs = apply_filters( 'make_column_configuration', array(
		100 => array(
			'type'    => 'section_title',
			'name'    => 'title',
			'label'   => __( 'Enter column title', 'make' ),
			'default' => '{{ get("title") }}',
			'class'   => 'ttfmake-configuration-title',
		)
	) );

	// Sort the config in case 3rd party code added another input
	ksort( $inputs, SORT_NUMERIC );

	// Print the inputs
	$output = '';

	foreach ( $inputs as $input ) {
		if ( isset( $input['type'] ) && isset( $input['name'] ) ) {
			$output       .= ttfmake_create_input( $column_name, $input, array() );
		}
	}

	echo $output;

?>
	<div class="ttfmake-configuration-overlay-input-wrap">
		<label for="image-link"><?php echo __( 'Image link URL', 'make' ); ?></label>
		<input type="text" name="image-link" id="image-link" value="{{ get('image-link') }}" data-model-attr="image-link">
	</div>

	<?php get_template_part( '/inc/builder/core/templates/overlay', 'footer' ); ?>
</div>
