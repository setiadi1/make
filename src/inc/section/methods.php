<?php
/**
 * @package Make
 */

/**
 * Class MAKE_Plus_Methods
 *
 * @since 1.7.0.
 */
final class MAKE_Section_Methods implements MAKE_Section_MethodsInterface, MAKE_Util_HookInterface {
	/**
	 * Whether Make Plus is installed and active.
	 *
	 * @since 1.7.0.
	 *
	 * @var bool
	 */
	private $plus = null;

	/**
	 * Indicator of whether the hook routine has been run.
	 *
	 * @since 1.7.0.
	 *
	 * @var bool
	 */
	private static $hooked = false;

	/**
	 * Hook into WordPress.
	 *
	 * @since 1.7.0.
	 *
	 * @return void
	 */
	public function hook() {
		if ( $this->is_hooked() ) {
			return;
		}



		// Hooking has occurred.
		self::$hooked = true;
	}

	/**
	 * Check if the hook routine has been run.
	 *
	 * @since 1.7.0.
	 *
	 * @return bool
	 */
	public function is_hooked() {
		return self::$hooked;
	}

	public function load_section_template( $slug, $path, $return = false, $section_data ) {
		$templates = array(
			$slug . '.php',
			trailingslashit( $path ) . $slug . '.php'
		);

		/**
		 * Filter the templates to try and load.
		 *
		 * @since 1.2.3.
		 *
		 * @param array    $templates    The list of template to try and load.
		 * @param string   $slug         The template slug.
		 * @param string   $path         The path to the template.
		 */
		$templates = apply_filters( 'make_load_section_template', $templates, $slug, $path );

		if ( '' === $located = locate_template( $templates, true, false ) ) {
			if ( isset( $templates[1] ) && file_exists( $templates[1] ) ) {
				if ( $return ) {
					ob_start();
				}

				require( $templates[1] );
				$located = $templates[1];

				if ( $return ) {
					$located = ob_get_clean();
				}
			}
		}

		return $located;
	}

	public function get_html_id( $current_section ) {
		$prefix = 'builder-section-';
		$id = sanitize_title_with_dashes( $current_section['id'] );

		/**
		 * Filter the section wrapper's HTML id attribute.
		 *
		 * @since 1.6.0.
		 *
		 * @param string    $section_id         The string used in the section's HTML id attribute.
		 * @param array     $current_section    The data for the section.
		 */
		return apply_filters( 'make_section_html_id', $prefix . $id, $current_section );
	}

	public function get_sections( $sections_meta ) {
		$sections = array();

		if ( ! empty( $sections_meta ) ) {
			foreach ( $sections_meta as $section ) {
				$section_meta = get_metadata_by_mid( 'post', $section );
				$section_data = json_decode( wp_unslash( $section_meta->meta_value ), true );

				$sections[$section_data['sid']] = $section_data;
			}
		}

		return $sections;
	}

	public function get_prev_section_data( $current_section, $sections ) {
		foreach ( $sections as $sid => $data ) {
			if ( $current_section['sid'] == $sid ) {
				break;
			} else {
				$prev_key = $sid;
			}
		}

		$prev_section = ( isset( $prev_key ) && isset( $sections[ $prev_key ] ) ) ? $sections[ $prev_key ] : array();

		/**
		 * Allow developers to alter the "next" section data.
		 *
		 * @since 1.2.3.
		 *
		 * @param array    $prev_section       The data for the next section.
		 * @param array    $current_section    The data for the current section.
		 * @param array    $sections           The list of all sections.
		 */
		return apply_filters( 'make_get_prev_section_data', $prev_section, $current_section, $sections );
	}

	public function get_next_section_data( $current_section, $sections ) {
		$next_is_the_one = false;
		$next_data       = array();

		if ( ! empty( $sections ) ) {
			foreach ( $sections as $sid => $data ) {
				if ( true === $next_is_the_one ) {
					$next_data = $data;
					break;
				}

				if ( $current_section['sid'] == $sid ) {
					$next_is_the_one = true;
				}
			}
		}

		/**
		 * Allow developers to alter the "next" section data.
		 *
		 * @since 1.2.3.
		 *
		 * @param array    $next_data          The data for the next section.
		 * @param array    $current_section    The data for the current section.
		 * @param array    $sections           The list of all sections.
		 */
		return apply_filters( 'make_get_next_section_data', $next_data, $current_section, $sections );
	}

	public function get_html_class( $section_data ) {
		$section_type = $section_data['section-type'];

		global $post;
		$sections_meta = json_decode( wp_unslash( get_post_meta( $post->ID, '__ttfmake_layout', true ) ), true );
		$sections = $this->get_sections( $sections_meta );

		$prefix = 'builder-section-';

		// Get the current section type
		$current = ( $section_type ) ? $prefix . $section_type : '';

		$next_data = $this->get_next_section_data( $section_data, $sections );
		$next = ( ! empty( $next_data ) && isset( $next_data['section-type'] ) ) ? $prefix . 'next-' . $next_data['section-type'] : $prefix . 'last';

		// Get the previous section's type
		$prev_data = $this->get_prev_section_data( $section_data, $sections );
		$prev      = ( ! empty( $prev_data ) && isset( $prev_data['section-type'] ) ) ? $prefix . 'prev-' . $prev_data['section-type'] : $prefix . 'first';

		/**
		 * Filter the section classes.
		 *
		 * @since 1.2.3.
		 *
		 * @param string    $classes            The sting of classes.
		 * @param array     $current_section    The array of data for the current section.
		 */

		$section_classes = apply_filters( 'make_section_classes', $prev . ' ' . $current . ' ' . $next, $section_data );

		$html_class = ' ';

		$full_width = isset( $section_data['full-width'] ) && 0 !== absint( $section_data['full-width'] );

		if ( true === $full_width ) {
			$html_class .= ' builder-section-full-width';
		}

		switch( $section_type ) {
			case 'text':
				$columns_number = ( isset( $section_data['columns-number'] ) ) ? absint( $section_data['columns-number'] ) : 1;
				$html_class .= ' builder-text-columns-' . $columns_number;

				$bg_color = ( isset( $section_data['background-color'] ) && ! empty( $section_data['background-color'] ) );

				$bg_image = ( isset( $section_data['background-image'] ) && 0 !== absint( $section_data['background-image'] ) );
				if ( true === $bg_color || true === $bg_image ) {
					$html_class .= ' has-background';
				}
				break;

			case 'gallery':
				break;

			case 'banner':
				break;
		}

		/**
		 * Filter the text section class.
		 *
		 * @since 1.2.3.
		 *
		 * @param string    $text_class              The computed class string.
		 * @param array     $ttfmake_section_data    The section data.
		 * @param array     $sections                The list of sections.
		 */
		$section_specific_classes = apply_filters( 'make_builder_get_text_class', $html_class, $section_data, $sections );

		return $section_classes . $section_specific_classes;
	}

	public function get_section_styles( $section_data ) {
		$style = '';

		// Background color
		if ( isset( $section_data['background-color'] ) && ! empty( $section_data['background-color'] ) ) {
			$style .= 'background-color:' . maybe_hash_hex_color( $section_data['background-color'] ) . ';';
		}

		// Background image
		if ( isset( $section_data['background-image'] ) && 0 !== absint( $section_data['background-image'] ) ) {
			$image_src = ttfmake_get_image_src( $section_data['background-image'], 'full' );
			if ( isset( $image_src[0] ) ) {
				$style .= 'background-image: url(\'' . addcslashes( esc_url_raw( $image_src[0] ), '"' ) . '\');';
			}
		}

		// Background style
		if ( isset( $section_data['background-style'] ) && ! empty( $section_data['background-style'] ) ) {
			if ( in_array( $section_data['background-style'], array( 'cover', 'contain' ) ) ) {
				$style .= 'background-size: ' . $section_data['background-style'] . '; background-repeat: no-repeat;';
			}
		}

		// Background position
		if ( isset( $section_data['background-position'] ) && ! empty( $section_data['background-position'] ) ) {
			$rule = explode( '-', $section_data['background-position'] );
			$style .= 'background-position: ' . implode( ' ', $rule ) . ';';
		}

		return $style;
	}

	public function get_content( $content ) {
		/**
		 * Filter the content used for "post_content" when the builder is used to generate content.
		 *
		 * @since 1.2.3.
		 * @deprecated 1.7.0.
		 *
		 * @param string    $content    The post content.
		 */
		$content = apply_filters( 'ttfmake_the_builder_content', $content );

		/**
		 * Filter the content used for "post_content" when the builder is used to generate content.
		 *
		 * @since 1.2.3.
		 *
		 * @param string    $content    The post content.
		 */
		$content = apply_filters( 'make_the_builder_content', $content );

		$content = str_replace( ']]>', ']]&gt;', $content );

		echo $content;
	}
}
