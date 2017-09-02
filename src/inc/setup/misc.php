<?php
/**
 * @package Make
 */

/**
 * Class MAKE_Setup_Misc
 *
 * Miscellaneous theme setup routines.
 *
 * @since 1.7.0.
 */
final class MAKE_Setup_Misc extends MAKE_Util_Modules implements MAKE_Setup_MiscInterface, MAKE_Util_HookInterface {
	/**
	 * An associative array of required modules.
	 *
	 * @since 1.7.0.
	 *
	 * @var array
	 */
	protected $dependencies = array(
		'view'     => 'MAKE_Layout_ViewInterface',
		'thememod' => 'MAKE_Settings_ThemeModInterface',
		'widgets'  => 'MAKE_Setup_WidgetsInterface',
		'scripts'  => 'MAKE_Setup_ScriptsInterface',
		'plus'     => 'MAKE_Plus_MethodsInterface',
	);

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

		// Theme support
		add_action( 'after_setup_theme', array( $this, 'theme_support' ), 20 );

		// Menu locations
		add_action( 'after_setup_theme', array( $this, 'menu_locations' ) );

		// Content width
		add_action( 'template_redirect', array( $this, 'content_width' ) );

		// Body classes
		add_filter( 'body_class', array( $this, 'body_classes' ) );

		// Post classes
		add_filter( 'post_class', array( $this, 'post_classes' ) );

		// Excerpt more
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );

		// Embed container
		add_filter( 'embed_handler_html', array( $this, 'embed_container' ), 10, 3 );
		add_filter( 'embed_oembed_html' , array( $this, 'embed_container' ), 10, 3 );

		// Category transient flusher
		add_action( 'edit_category', array( $this, 'category_transient_flusher' ) );
		add_action( 'save_post', array( $this, 'category_transient_flusher' ) );

		// Builder publish checkbox
		add_filter( 'make_builder_is_default', '__return_true' );
		// Default to builder page
		add_filter( 'make_builder_toggle_display', array( $this, 'make_builder_toggle_display' ) );

		// Master sections description
		if ( ! $this->plus()->is_plus() ) {
			add_filter( 'make_section_settings', array( $this, 'master_demo_setting' ), 50, 2 );
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

	/**
	 * Declare theme support for various WordPress features.
	 *
	 * @since 1.7.0.
	 *
	 * @hooked action after_setup_theme
	 *
	 * @return void
	 */
	public function theme_support() {
		global $content_width;

		// Automatic feed links
		add_theme_support( 'automatic-feed-links' );

		// Custom background
		add_theme_support( 'custom-background', array(
			'default-color'      => $this->thememod()->get_default( 'background_color' ),
			'default-image'      => $this->thememod()->get_default( 'background_image' ),
			'default-repeat'     => $this->thememod()->get_default( 'background_repeat' ),
			'default-position-x' => $this->thememod()->get_default( 'background_position_x' ),
			'default-attachment' => $this->thememod()->get_default( 'background_attachment' ),
		) );

		// Custom logo
		add_theme_support( 'custom-logo', array(
			'height'      => $content_width,
			'width'       => $content_width,
			'flex-height' => true,
			'flex-width'  => true,
		) );

		// Customizer: selective refresh for widgets
		add_theme_support( 'customize-selective-refresh-widgets' );

		// HTML5
		add_theme_support( 'html5', array(
			'comment-list',
			'comment-form',
			'search-form',
			'gallery',
			'caption'
		) );

		// Post thumbnails (featured images)
		add_theme_support( 'post-thumbnails' );

		// Title tag
		add_theme_support( 'title-tag' );
	}

	/**
	 * Register menu locations.
	 *
	 * @since 1.7.0.
	 *
	 * @hooked action after_setup_theme
	 *
	 * @return void
	 */
	public function menu_locations() {
		register_nav_menus( array(
			'primary'    => __( 'Primary Navigation', 'make' ),
			'header-bar' => __( 'Header Bar Navigation', 'make' ),
		) );
	}

	/**
	 * Set the content width based on current layout
	 *
	 * @since 1.0.0.
	 *
	 * @hooked action template_redirect
	 *
	 * @return void
	 */
	public function content_width() {
		global $content_width;

		$new_width = $content_width;
		$left = $this->widgets()->has_sidebar( 'left' );
		$right = $this->widgets()->has_sidebar( 'right' );

		// No sidebars
		if ( ! $left && ! $right ) {
			$new_width = 960;
		}
		// Both sidebars
		else if ( $left && $right ) {
			$new_width = 464;
		}
		// One sidebar
		else if ( $left || $right ) {
			$new_width = 620;
		}

		/**
		 * Filter to modify the $content_width variable.
		 *
		 * @since 1.4.8
		 *
		 * @param int  $new_width    The new content width.
		 * @param bool $left         True if the current view has a left sidebar.
		 * @param bool $right        True if the current view has a right sidebar.
		 */
		$content_width = apply_filters( 'make_content_width', $new_width, $left, $right );
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since  1.0.0.
	 *
	 * @hooked filter body_class
	 *
	 * @param array $classes    Classes for the body element.
	 *
	 * @return array            Modified class list.
	 */
	public function body_classes( array $classes ) {
		// Current view
		if ( ! is_null( $view = $this->view()->get_current_view() ) ) {
			$classes[] = 'view-' . $view;
		}

		// Full-width vs Boxed
		$classes[] = make_get_thememod_value( 'general-layout' );

		// Header branding position
		if ( 'right' === make_get_thememod_value( 'header-branding-position' ) ) {
			$classes[] = 'branding-right';
		}

		// Header Bar text position
		if ( 'flipped' === make_get_thememod_value( 'header-bar-content-layout' ) ) {
			$classes[] = 'header-bar-flipped';
		}

		// Left Sidebar
		if ( true === make_has_sidebar( 'left' ) ) {
			$classes[] = 'has-left-sidebar';
		}

		// Right Sidebar
		if ( true === make_has_sidebar( 'right' ) ) {
			$classes[] = 'has-right-sidebar';
		}

		return $classes;
	}

	/**
	 * Adds custom classes to the array of post container classes.
	 *
	 * @since  1.7.0.
	 *
	 * @hooked filter post_class
	 *
	 * @param array $classes    Classes for the post container element.
	 *
	 * @return array            Modified class list.
	 */
	public function post_classes( array $classes ) {
		if ( ! is_admin() ) {
			// Author avatar class
			$author_key    = 'layout-' . make_get_current_view() . '-post-author';
			$author_option = make_get_thememod_value( $author_key );

			if ( 'avatar' === $author_option ) {
				$classes[] = 'has-author-avatar';
			}
		}

		return $classes;
	}

	/**
	 * Modify the excerpt suffix
	 *
	 * @since 1.0.0.
	 *
	 * @hooked filter excerpt_more
	 *
	 * @return string
	 */
	public function excerpt_more() {
		return ' &hellip;';
	}

	/**
	 * Add a wrapper div to the output of oembeds and the [embed] shortcode.
	 *
	 * Also enqueues FitVids, since the embed might be a video.
	 *
	 * @since 1.6.0.
	 *
	 * @hooked filter embed_handler_html
	 * @hooked filter embed_oembed_html
	 *
	 * @param string $html    The generated HTML of the embed handler.
	 * @param string $url     The embed URL.
	 * @param array  $attr    The attributes of the embed shortcode.
	 *
	 * @return string         The wrapped HTML.
	 */
	public function embed_container( $html, $url, $attr ) {
		// Bail if this is the admin or an RSS feed
		if ( is_admin() || is_feed() ) {
			return $html;
		}

		if ( isset( $attr['width'] ) ) {
			// Add FitVids as a dependency for the Frontend script
			$this->scripts()->add_dependency( 'make-frontend', 'fitvids', 'script' );

			// Get classes
			$default_class = 'ttfmake-embed-wrapper';
			$align_class = 'aligncenter';
			if ( isset( $attr['make_align'] ) ) {
				$align = trim( $attr['make_align'] );
				if ( in_array( $align, array( 'left', 'right', 'center', 'none' ) ) ) {
					$align_class = 'align' . $align;
				}
			}
			$class = trim( "$default_class $align_class" );

			// Get style
			$style = 'max-width: ' . absint( $attr['width'] ) . 'px;';

			// Build wrapper
			$wrapper = "<div class=\"$class\" style=\"$style\">%s</div>";
			$html = sprintf( $wrapper, $html );
		}

		return $html;
	}

	/**
	 * Flush out the transients used in ttfmake_categorized_blog.
	 *
	 * @since 1.0.0.
	 *
	 * @hooked action edit_category
	 * @hooked action save_post
	 *
	 * @return void
	 */
	public function category_transient_flusher() {
		// Bail if this is an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		delete_transient( 'make_category_count' );
	}

	function make_builder_toggle_display( $display ) {
		// Make prevents the toggle
		// from showing up at all on pages
		if ( 'page' === get_post_type() ) {
			$display = false;
		}

		return $display;
	}

	/**
	 * Add a new control definition to a section's config array for the
	 * "Section HTML classes" control.
	 *
	 * @since 1.6.0.
	 *
	 * @hooked filter make_add_section
	 *
	 * @param array $args    The section args.
	 *
	 * @return array         The modified section args.
	 */
	public function master_demo_setting( $settings, $section_type ) {
		if ( ! in_array( $section_type, array( 'text', 'banner', 'gallery' ) ) ) {
			return $settings;
		}

		$index = max( array_keys( $settings ) );
		$plus_link = 'https://thethemefoundry.com/make-buy/';

		$settings[$index + 100] = array(
			'type' => 'divider',
			'label' => __( 'Master', 'make' ),
			'name' => 'divider-master',
			'class' => 'ttfmake-configuration-divider',
		);

		$settings[$index + 125] = array(
			'type' => 'description',
			'label' => __( 'Master', 'make' ),
			'name' => 'master',
			'description' => '<p>' . __( 'Did you know: Master mode lets you add this section to other pages, or parts of pages, and changes you make will apply everywhere this section is used.', 'make'  ) . '</p><p><a href="' . esc_js( $plus_link ) . '" target="_blank">' . __( 'Upgrade to Make Plus to get Master mode.', 'make' ) . '</a></p>',
		);

		return $settings;
	}
}


if ( ! function_exists( 'make_get_context_directory' ) ) :

function make_get_context_directory() {
	return get_template_directory();
}

endif;

if ( ! function_exists( 'make_get_context_directory_uri' ) ) :

function make_get_context_directory_uri() {
	return get_template_directory_uri();
}

endif;
