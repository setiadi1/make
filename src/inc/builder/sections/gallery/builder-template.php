<?php
/**
 * @package Make
 */

ttfmake_load_section_header();

global $ttfmake_section_data;
?>

<div class="ttfmake-gallery-items">
	<div class="ttfmake-gallery-items-stage ttfmake-gallery-columns-{{ get('columns') }}"></div>
	<a href="#" class="ttfmake-add-item ttfmake-gallery-add-item-link" title="<?php esc_attr_e( 'Add new item', 'make' ); ?>">
		<div class="ttfmake-gallery-add-item">
			<span>
				<?php esc_html_e( 'Add new item', 'make' ); ?>
			</span>
		</div>
	</a>
</div>

<?php ttfmake_load_section_footer();