<?php

/**
 * Fuction yang digunakan di theme ini.
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

add_action('after_setup_theme', 'velocitychild_theme_setup', 9);

function velocitychild_theme_setup()
{

	// Load justg_child_enqueue_parent_style after theme setup
	add_action('wp_enqueue_scripts', 'justg_child_enqueue_parent_style', 20);

	if (class_exists('Kirki')) :
		$textdomain = 'justg';

		$args = array(
			'orderby' => 'name',
			'hide_empty' => false,
		);
		$cats = array(
			'' => 'Show All'
		);
		$categories = get_categories($args);
		foreach ($categories as $category) {
			$kategori[$category->term_id] = $category->name;
		}

		Kirki::add_panel('panel_velocity', [
			'priority'    => 10,
			'title'       => esc_html__('Velocity Theme', $textdomain),
			'description' => esc_html__('', $textdomain),
		]);

		// section title_tagline
		Kirki::add_section('title_tagline', [
			'panel'    => 'panel_velocity',
			'title'    => __('Site Identity', $textdomain),
			'priority' => 10,
		]);

		// Section Contact
		Kirki::add_section('panel_contact', [
			'panel'    => 'panel_velocity',
			'title'    => __('Header Contact', $textdomain),
			'priority' => 11,
		]);
		Kirki::add_field('justg_config', [
			'type'        => 'text',
			'settings'    => 'contact_email',
			'label'       => __('Email', $textdomain),
			'section'     => 'panel_contact',
		]);
		Kirki::add_field('justg_config', [
			'type'        => 'text',
			'settings'    => 'contact_phone',
			'label'       => __('Telepon', $textdomain),
			'section'     => 'panel_contact',
		]);
		Kirki::add_field('justg_config', [
			'type'        => 'text',
			'settings'    => 'contact_wa',
			'label'       => __('Whatsapp', $textdomain),
			'section'     => 'panel_contact',
		]);


		///Section Color
		Kirki::add_section('section_colorvelocity', [
			'panel'    => 'panel_velocity',
			'title'    => __('Color & Background', $textdomain),
			'priority' => 10,
		]);
		Kirki::add_field('justg_config', [
			'type'        => 'color',
			'settings'    => 'color_theme',
			'label'       => __('Theme Color', $textdomain),
			'description' => esc_html__('', $textdomain),
			'section'     => 'section_colorvelocity',
			'default'     => '#176cb7',
			'transport'   => 'auto',
			'output'      => [
				[
					'element'   => ':root',
					'property'  => '--color-theme',
				],
				[
					'element'   => ':root',
					'property'  => '--bs-primary',
				],
				[
					'element'   => '.border-color-theme',
					'property'  => '--bs-border-color',
				]
			],
		]);
		Kirki::add_field('justg_config', [
			'type'        => 'background',
			'settings'    => 'background_themewebsite',
			'label'       => __('Website Background', $textdomain),
			'description' => esc_html__('', $textdomain),
			'section'     => 'section_colorvelocity',
			'default'     => [
				'background-color'      => '#F5F5F5',
				'background-image'      => '',
				'background-repeat'     => 'repeat',
				'background-position'   => 'center center',
				'background-size'       => 'cover',
				'background-attachment' => 'scroll',
			],
			'transport'   => 'auto',
			'output'      => [
				[
					'element'   => ':root[data-bs-theme=light] body',
				],
				[
					'element'   => 'body',
				],
			],
		]);


		// section Home Slider
		Kirki::add_section('section_homeslider', [
			'panel'    => 'panel_velocity',
			'title'    => __('Home Slider', $textdomain),
			'priority' => 12,
		]);
		for($x = 1; $x <= 10; $x++){
			Kirki::add_field('justg_config', [
				'type'        => 'image',
				'settings'    => 'home_slider'.$x,
				'label'       => __('Slider '.$x, $textdomain),
				'section'     => 'section_homeslider',
				'description' => esc_html__('Ukuran 1000x400', $textdomain),
				'choices'     => [
					'save_as' => 'id',
				],
			]);
		}

		// section Home Service
		Kirki::add_section('section_homeservice', [
			'panel'    => 'panel_velocity',
			'title'    => __('Home Services', $textdomain),
			'priority' => 13,
		]);
		$vdicon = new VelocityChild\Icon;
    	$fontawesome  = $vdicon->fontawesome();
		for($x = 1; $x <= 4; $x++){
			Kirki::add_field('justg_config', [
				'type'        => 'select',
				'settings'    => 'hs_icon'.$x,
				'label'       => __('Service Icon '.$x, $textdomain),
				'description' => esc_html__('Gambar icon: https://fontawesome.com/v4/icons/', $textdomain),
				'section'     => 'section_homeservice',
				'choices'     => $fontawesome,
			]);
			Kirki::add_field('justg_config', [
				'type'        => 'image',
				'settings'    => 'hs_img'.$x,
				'label'       => __('Service Image '.$x, $textdomain),
				'section'     => 'section_homeservice',
				'choices'     => [
					'save_as' => 'id',
				],
			]);
			Kirki::add_field('justg_config', [
				'type'        => 'editor',
				'settings'    => 'hs_text'.$x,
				'label'       => __('Service Text '.$x, $textdomain),
				'section'     => 'section_homeservice',
			]);
			Kirki::add_field('justg_config', [
				'type'        => 'url',
				'settings'    => 'hs_url'.$x,
				'label'       => __('Service Link '.$x, $textdomain),
				'section'     => 'section_homeservice',
			]);
		}

		
		// section Home News
		Kirki::add_section('section_homenews', [
			'panel'    => 'panel_velocity',
			'title'    => __('Home News', $textdomain),
			'priority' => 14,
		]);
        Kirki::add_field('justg_config', [
            'type'  => 'text',
            'settings'  => 'hn_title',
            'label'     => esc_html__('Title', $textdomain),
            'section'   => 'section_homenews',
        ]);
        Kirki::add_field('justg_config', [
            'type'  => 'select',
            'settings'  => 'home_news',
            'label'     => esc_html__('Post Category', $textdomain),
            'section'   => 'section_homenews',
            'placeholder' => esc_html__('Select Category', $textdomain),
            'priority'  => 10,
            'multiple'  => 1,
            'choices'   => $kategori,
        ]);



		// remove panel in customizer 
		Kirki::remove_panel('global_panel');
		Kirki::remove_panel('panel_header');
		Kirki::remove_panel('panel_footer');
		Kirki::remove_panel('panel_antispam');
		Kirki::remove_control('display_header_text');
		Kirki::remove_section('header_image');

	endif;

	//remove action from Parent Theme
	remove_action('justg_header', 'justg_header_menu');
	remove_action('justg_do_footer', 'justg_the_footer_open');
	remove_action('justg_do_footer', 'justg_the_footer_content');
	remove_action('justg_do_footer', 'justg_the_footer_close');
	remove_theme_support('widgets-block-editor');
}


///remove breadcrumbs
add_action('wp_head', function () {
	if (!is_single()) {
		remove_action('justg_before_title', 'justg_breadcrumb');
	}
});

if (!function_exists('justg_header_open')) {
	function justg_header_open()
	{
		echo '<header id="wrapper-header" class="px-2 mt-3 position-relative z-index-2">';
		echo '<div id="wrapper-navbar" class="container bg-white shadow-sm px-3" itemscope itemtype="http://schema.org/WebSite">';
	}
}
if (!function_exists('justg_header_close')) {
	function justg_header_close()
	{
		echo '</div>';
		echo '</header>';
	}
}


///add action builder part
add_action('justg_header', 'justg_header_berita');
function justg_header_berita()
{
	require_once(get_stylesheet_directory() . '/inc/part-header.php');
}
add_action('justg_do_footer', 'justg_footer_berita');
function justg_footer_berita()
{
	require_once(get_stylesheet_directory() . '/inc/part-footer.php');
}
add_action('justg_before_wrapper_content', 'justg_before_wrapper_content');
function justg_before_wrapper_content()
{
	echo '<div class="px-2">';
	echo '<div class="card rounded-0 border-0 shadow-sm px-3 container">';
}
add_action('justg_after_wrapper_content', 'justg_after_wrapper_content');
function justg_after_wrapper_content()
{
	echo '</div>';
	echo '</div>';
}

add_action('wp_footer', 'velocity_tour1_footer');
function velocity_tour1_footer()
{ ?>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<?php
}


// excerpt more
if ( ! function_exists( 'velocity_custom_excerpt_more' ) ) {
	function velocity_custom_excerpt_more( $more ) {
		return '...';
	}
}
add_filter( 'excerpt_more', 'velocity_custom_excerpt_more' );

// excerpt length
function velocity_excerpt_length($length){
	return 40;
}
add_filter('excerpt_length','velocity_excerpt_length');


//register widget
add_action('widgets_init', 'justg_widgets_init', 20);
if (!function_exists('justg_widgets_init')) {
	function justg_widgets_init()
	{
		$textdomain = 'justg';
		register_sidebar(
			array(
				'name'          => __('Main Sidebar', $textdomain),
				'id'            => 'main-sidebar',
				'description'   => __('Main sidebar widget area', $textdomain),
				'before_widget' => '<aside id="%1$s" class="widget rounded-top %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title"><span>',
				'after_title'   => '</span></h3>',
				'show_in_rest'   => false,
			)
		);
	}
}


if (!function_exists('justg_right_sidebar_check')) {
	function justg_right_sidebar_check()
	{
		if (is_singular('fl-builder-template')) {
			return;
		}
		if (!is_active_sidebar('main-sidebar')) {
			return;
		}
		echo '<div class="widget-area right-sidebar pt-3 pt-md-0 ps-md-3 ps-0 pe-0 col-md-4 order-3" id="right-sidebar" role="complementary">';
		do_action('justg_before_main_sidebar');
		dynamic_sidebar('main-sidebar');
		do_action('justg_after_main_sidebar');
		echo '</div>';
	}
}