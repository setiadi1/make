<?php

// Ignazio's plugin

$include_folder = get_stylesheet_directory();

require_once ( "$include_folder/section_overlay.php" );

function make_api_loaded( $Make ) {
    if ( is_admin() ) {
        ttfmake_get_section_overlay()->hook();
    }
}

add_action( 'make_api_loaded', 'make_api_loaded', 10 );