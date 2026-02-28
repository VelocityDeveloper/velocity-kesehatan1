<?php

/**
 * Template Name: Home Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package justg
 */

get_header();
?>

<div id="velocityslider" class="carousel slide mx-minus-3" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php
	$slider_items = velocitychild_get_home_slider_items();
	foreach ($slider_items as $index => $slider_item) {
		$class  = (0 === $index) ? ' active' : '';
		$img_id = isset($slider_item['image_id']) ? absint($slider_item['image_id']) : 0;
		if ($img_id) {
			$img_url = wp_get_attachment_image_url($img_id, 'full');
			echo '<div class="carousel-item' . $class . '">';
			echo '<div class="velocity-slider-ratio">';
			if ($img_url) {
				echo '<img src="' . esc_url($img_url) . '" class="velocity-slider-media" alt="">';
			} else {
				echo '<div class="velocity-slider-placeholder" aria-hidden="true"></div>';
			}
			echo '</div>';
			echo '</div>';
		}
	}
	?>
  </div>
  <button class="carousel-control-prev w-auto px-3" type="button" data-bs-target="#velocityslider" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next w-auto px-3" type="button" data-bs-target="#velocityslider" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>


<?php
	echo '<div class="row mx-minus-3">';
	$services_items = velocitychild_get_home_services_items();
	foreach ($services_items as $service_item) {
		$hs_img  = isset($service_item['image_id']) ? absint($service_item['image_id']) : 0;
		$hs_icon = isset($service_item['icon']) ? (string) $service_item['icon'] : '';
		$hs_text = isset($service_item['text']) ? (string) $service_item['text'] : '';
		$hs_url  = isset($service_item['url']) ? (string) $service_item['url'] : '';

		if ($hs_icon || $hs_text) {
			$bg = '';
			if ($hs_img) {
				$bg_url = wp_get_attachment_image_url($hs_img, 'full');
				if ($bg_url) {
					$bg = ' style="background-image:url(' . esc_url($bg_url) . ')"';
				}
			}

			echo '<div class="col-sm-6 col-md p-0">';
			echo '<div class="h-100 text-center m-0 p-3 pt-4 text-white bg-dark velocity-service position-relative"' . $bg . '>';
			if ($hs_icon) {
				echo '<div class="position-relative z-index mb-2">';
				echo '<span class="velocity-hs-icon">' . velocitychild_get_bootstrap_icon_html($hs_icon) . '</span>';
				echo '</div>';
			}
			if ($hs_text) {
				echo '<div class="py-2 position-relative z-index">';
				echo wp_kses_post($hs_text);
				echo '</div>';
			}
			if ($hs_url) {
				echo '<a class="velocity-hs-link" href="' . esc_url($hs_url) . '"></a>';
			}
			echo '</div>';
			echo '</div>';
		}
	}
	echo '</div>';
?>


<?php
$args = array(
	'post_type'      => 'post',
	'posts_per_page' => 3,
);

$home_news = velocitytheme_option('home_news', '');
if (!empty($home_news)) {
	$args['cat'] = absint($home_news);
}

$posts = get_posts($args);
if ($posts) {
	echo '<div class="m-1 py-3">';
	echo '<h2 class="fs-4 fw-bold text-dark mb-3">' . esc_html(velocitytheme_option('hn_title', '')) . '</h2>';
	echo '<div class="row">';
	foreach ($posts as $post) {
		$trimmed_content = wp_trim_words(wp_strip_all_tags($post->post_content), 20);
		$link            = get_the_permalink($post->ID);
		echo '<div class="col-sm mb-4">';
		echo '<div class="mb-2">';
		echo velocitychild_get_post_thumbnail_html($post->ID, array('ratio' => '4x3'));
		echo '</div>';
		echo '<small class="text-muted d-block mb-1">' . esc_html(get_the_date('', $post->ID)) . '</small>';
		echo '<div class="fs-6 mb-2 lh-sm fw-bold">';
		echo '<a class="text-dark" href="' . esc_url($link) . '">' . esc_html($post->post_title) . '</a>';
		echo '</div>';
		echo esc_html($trimmed_content);
		echo '<div class="mt-2">';
		echo '<a class="btn btn-primary btn-sm px-3 py-2" href="' . esc_url($link) . '">Selengkapnya</a>';
		echo '</div>';
		echo '</div>';
	}
	echo '</div>';
	echo '</div>';
}
?>



<?php
get_footer();
