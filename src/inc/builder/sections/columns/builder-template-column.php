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
	$content     = '{{ get("content") }}';

	$column_buttons = array(
		100 => array(
			'label'              => __( 'Edit content', 'make' ),
			'href'               => '#',
			'class'              => 'edit-text-column-link edit-content-link ttfmake-icon-pencil {{ (get("content")) ? "item-has-content" : "" }}',
			'title'              => __( 'Edit content', 'make' ),
			'other-a-attributes' => 'data-textarea="' . $textarea_id . '" data-iframe="' . $iframe_id . '"',
		),
		600 => array(
			'label'              => __( 'Trash column', 'make' ),
			'href'               => '#',
			'class'              => 'ttfmake-text-column-remove ttfmake-icon-trash',
			'title'              => __( 'Trash column', 'make' )
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

		<a href="#" class="ttfmake-configure-item-button" title="Configure column">
			<span>Configure options</span>
		</a>
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

	<ul class="configure-item-dropdown">
		<?php foreach ( $column_buttons as $button ) : ?>
			<li>
				<a href="<?php echo esc_url( $button['href'] ); ?>" class="column-buttons <?php echo esc_attr( $button['class'] ); ?>" title="<?php echo esc_attr( $button['title'] ); ?>" <?php if ( ! empty( $button['other-a-attributes'] ) ) echo $button['other-a-attributes']; ?>>
					<?php echo esc_html( $button['label'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

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
</div>