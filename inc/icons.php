<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!function_exists('velocitychild_get_bootstrap_icon_choices')) {
	/**
	 * Get Bootstrap icon choices from parent theme icon manifest.
	 *
	 * @return array<string, string>
	 */
	function velocitychild_get_bootstrap_icon_choices() {
		static $choices = null;

		if (is_array($choices)) {
			return $choices;
		}

		$choices = array();
		$file    = trailingslashit(get_template_directory()) . 'fonts/bootstrap-icons.json';

		if (is_readable($file)) {
			$content = file_get_contents($file);
			$data    = json_decode((string) $content, true);

			if (is_array($data)) {
				$slugs = array_keys($data);
				sort($slugs, SORT_STRING);
				foreach ($slugs as $slug) {
					$slug = sanitize_key(str_replace('_', '-', (string) $slug));
					if ($slug !== '') {
						$choices[$slug] = $slug;
					}
				}
			}
		}

		if (empty($choices)) {
			$choices = array(
				'heart-pulse'    => 'heart-pulse',
				'hospital'       => 'hospital',
				'shield-check'   => 'shield-check',
				'person-check'   => 'person-check',
				'telephone-fill' => 'telephone-fill',
				'envelope-fill'  => 'envelope-fill',
			);
		}

		return $choices;
	}
}

if (!function_exists('velocitychild_get_default_service_icon')) {
	/**
	 * Get default icon slug.
	 *
	 * @return string
	 */
	function velocitychild_get_default_service_icon() {
		$choices = velocitychild_get_bootstrap_icon_choices();
		if (isset($choices['heart-pulse'])) {
			return 'heart-pulse';
		}

		$keys = array_keys($choices);
		return !empty($keys) ? (string) $keys[0] : 'heart-pulse';
	}
}

if (!function_exists('velocitychild_normalize_service_icon')) {
	/**
	 * Normalize and validate icon slug.
	 *
	 * @param string $raw Raw icon value.
	 * @return string
	 */
	function velocitychild_normalize_service_icon($raw) {
		$slug = strtolower(trim((string) $raw));
		$slug = str_replace('_', '-', $slug);

		if (strpos($slug, 'bi-') === 0) {
			$slug = substr($slug, 3);
		}

		$slug    = sanitize_key($slug);
		$choices = velocitychild_get_bootstrap_icon_choices();

		if (isset($choices[$slug])) {
			return $slug;
		}

		return velocitychild_get_default_service_icon();
	}
}

if (!function_exists('velocitychild_get_bootstrap_icon_html')) {
	/**
	 * Render icon markup using Bootstrap Icons font class.
	 *
	 * @param string $slug Icon slug.
	 * @return string
	 */
	function velocitychild_get_bootstrap_icon_html($slug) {
		$slug = velocitychild_normalize_service_icon($slug);

		return '<i class="bi bi-' . esc_attr($slug) . '" aria-hidden="true"></i>';
	}
}
