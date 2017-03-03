<?php
/**
 * @package Make
 */

global $ttfmake_section_data, $ttfmake_sections;
$text_columns = ttfmake_builder_get_text_array( $ttfmake_section_data );
$darken   = ( isset( $ttfmake_section_data[ 'darken' ] ) ) ? absint( $ttfmake_section_data[ 'darken' ] ) : 0;
?>

<section id="<?php echo esc_attr( ttfmake_get_builder_save()->section_html_id( $ttfmake_section_data ) ); ?>" class="builder-section<?php echo esc_attr( ttfmake_builder_get_text_class( $ttfmake_section_data, $ttfmake_sections ) ); ?>" style="<?php echo esc_attr( ttfmake_builder_get_text_style( $ttfmake_section_data ) ); ?>">
	<?php if ( '' !== $ttfmake_section_data['title'] ) : ?>
	<h3 class="builder-text-section-title">
		<?php echo apply_filters( 'the_title', $ttfmake_section_data['title'] ); ?>
	</h3>
	<?php endif; ?>
	<div class="builder-section-content">
		<?php $columns_layout_size = $ttfmake_section_data['columns-number']; ?>
		<?php if ( ! empty( $text_columns ) ) : $i = 1; foreach ( $text_columns as $column ) : ?>
			<?php if ( $i == 1 ): ?>
				<div class="builder-text-row">
			<?php endif; ?>
			<div class="builder-text-column builder-text-column-<?php echo $i; ?>" id="builder-section-<?php echo esc_attr( $ttfmake_section_data['id'] ); ?>-column-<?php echo $i; ?>">
				<?php if ( '' !== $column['content'] ) : ?>
				<div class="builder-text-content">
					<?php ttfmake_get_builder_save()->the_builder_content( $column['content'] ); ?>
				</div>
				<?php endif; ?>
			</div>
			<?php if ( $i % $columns_layout_size == 0 ): ?>
				</div>
				<?php if ( $i < sizeof( $text_columns ) ): ?>
					<div class="builder-text-row">
				<?php endif; ?>
			<?php endif; ?>
		<?php $i++; endforeach; endif; ?>
	</div>
	<?php if ( 0 !== $darken ) : ?>
	<div class="builder-section-overlay"></div>
	<?php endif; ?>
</section>
