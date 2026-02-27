<?php

/**
 * Kumpulan shortcode yang digunakan di theme ini.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// [excerpt count="150"]
add_shortcode('excerpt', 'vd_getexcerpt');
function vd_getexcerpt($atts)
{
    ob_start();
    $atribut = shortcode_atts(
        array(
            'count' => '150', // count character
        ),
        $atts
    );

    $count   = (int) $atribut['count'];
    $excerpt = get_the_content();
    $excerpt = strip_tags($excerpt);
    $excerpt = substr($excerpt, 0, $count);
    $excerpt = substr($excerpt, 0, strripos($excerpt, ' '));
    $excerpt = $excerpt . '...';

    echo $excerpt;

    return ob_get_clean();
}
