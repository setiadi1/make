<?php
/**
 * @package Make
 */

ttfmake_load_section_header();

global $ttfmake_section_data;

/**
 * Execute code before the columns select input is displayed.
 *
 * @since 1.2.3.
 *
 * @param array    $ttfmake_section_data    The data for the section.
 */
do_action( 'make_section_text_before_columns_select', $ttfmake_section_data );

/**
 * Execute code after the columns select input is displayed.
 *
 * @since 1.2.3.
 *
 * @param array    $ttfmake_section_data    The data for the section.
 */
do_action( 'make_section_text_after_columns_select', $ttfmake_section_data );

/**
 * Execute code after the section title is displayed.
 *
 * @since 1.2.3.
 *
 * @param array    $ttfmake_section_data    The data for the section.
 */
do_action( 'make_section_text_after_title', $ttfmake_section_data ); ?>

<div class="ttfmake-text-columns-stage ttfmake-section-text ttfmake-text-columns-{{ get('columns-number') }}">
	<?php
	/**
	 * Execute code after all columns are displayed.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $ttfmake_section_data    The data for the section.
	 */
	do_action( 'make_section_text_after_columns', $ttfmake_section_data );
	?>
</div>

<div class="clear"></div>

<a href="#" class="ttfmake-add-slide ttfmake-text-columns-add-column-link" title="<?php esc_attr_e( 'Add new column', 'make' ); ?>">
	<div class="ttfmake-text-columns-add-column">
		<span>
			<?php esc_html_e( 'Add Column', 'make' ); ?>
		</span>
	</div>
</a>

<?php ttfmake_load_section_footer();
