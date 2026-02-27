<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!function_exists('velocitychild_get_no_image_url')) {
	/**
	 * Get child theme fallback image URL.
	 *
	 * @return string
	 */
	function velocitychild_get_no_image_url() {
		return trailingslashit(get_stylesheet_directory_uri()) . 'img/no-image.webp';
	}
}

if (!function_exists('velocitychild_get_post_thumbnail_html')) {
	/**
	 * Render post thumbnail with Bootstrap ratio wrapper and fallback image.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $args Optional args.
	 * @return string
	 */
	function velocitychild_get_post_thumbnail_html($post_id, $args = array()) {
		$post_id = absint($post_id);
		if (!$post_id) {
			return '';
		}

		$defaults = array(
			'ratio'               => '4x3',
			'wrapper_class'       => '',
			'img_class'           => 'w-100 h-100 object-fit-cover',
			'attachment_fallback' => true,
		);
		$args = wp_parse_args($args, $defaults);

		$image_url = get_the_post_thumbnail_url($post_id, 'large');
		$alt       = get_the_title($post_id);

		if (empty($image_url) && !empty($args['attachment_fallback'])) {
			$attachments = get_posts(
				array(
					'post_type'      => 'attachment',
					'posts_per_page' => 1,
					'post_parent'    => $post_id,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'fields'         => 'ids',
				)
			);

			if (!empty($attachments)) {
				$attachment_id = absint($attachments[0]);
				$image_url     = wp_get_attachment_image_url($attachment_id, 'large');
				$image_alt     = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
				if ($image_alt !== '') {
					$alt = $image_alt;
				}
			}
		}

		if (empty($image_url)) {
			$image_url = velocitychild_get_no_image_url();
		}

		$ratio_class = 'ratio ratio-' . sanitize_html_class((string) $args['ratio']);
		$wrapper     = trim($ratio_class . ' ' . (string) $args['wrapper_class']);
		$img_class   = trim((string) $args['img_class']);

		$html  = '<div class="' . esc_attr($wrapper) . '">';
		$html .= '<a href="' . esc_url(get_permalink($post_id)) . '" title="' . esc_attr(get_the_title($post_id)) . '">';
		$html .= '<img src="' . esc_url($image_url) . '" class="' . esc_attr($img_class) . '" alt="' . esc_attr($alt) . '" loading="lazy" decoding="async">';
		$html .= '</a>';
		$html .= '</div>';

		return $html;
	}
}
