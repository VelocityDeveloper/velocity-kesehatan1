<?php

/**
 * Fuction yang digunakan di theme ini.
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

add_action('after_setup_theme', 'velocitychild_theme_setup', 9);
add_action('customize_register', 'velocitychild_customize_register', 30);
add_action('customize_controls_enqueue_scripts', 'velocitychild_customize_editor_assets');

if (class_exists('WP_Customize_Control') && !class_exists('VelocityChild_Customize_Editor_Control')) {
	/**
	 * TinyMCE editor control for WordPress Customizer.
	 */
	class VelocityChild_Customize_Editor_Control extends WP_Customize_Control {
		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'velocitychild_editor';

		/**
		 * Render control.
		 *
		 * @return void
		 */
		public function render_content() {
			$editor_id = sanitize_html_class('velocitychild_editor_' . $this->id);
			?>
			<label>
				<?php if (!empty($this->label)) : ?>
					<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
				<?php endif; ?>
				<?php if (!empty($this->description)) : ?>
					<span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
				<?php endif; ?>
				<textarea id="<?php echo esc_attr($editor_id); ?>" class="widefat velocitychild-editor-field" rows="8" <?php $this->link(); ?>><?php echo esc_textarea($this->value()); ?></textarea>
			</label>
			<?php
		}
	}
}

if (!function_exists('velocitychild_customize_editor_assets')) {
	/**
	 * Enqueue assets for customizer editor/repeater controls.
	 *
	 * @return void
	 */
	function velocitychild_customize_editor_assets() {
		$theme   = wp_get_theme();
		$version = $theme ? $theme->get('Version') : '1.0.0';

		wp_enqueue_media();

		if (function_exists('wp_enqueue_editor')) {
			wp_enqueue_editor();
		}

		$script_path = get_stylesheet_directory() . '/js/customizer-editor.js';
		$editor_ver  = file_exists($script_path) ? filemtime($script_path) : $version;

		wp_enqueue_script(
			'velocitychild-customizer-editor',
			get_stylesheet_directory_uri() . '/js/customizer-editor.js',
			array('jquery', 'customize-controls', 'editor'),
			$editor_ver,
			true
		);

		$repeater_css_path = get_stylesheet_directory() . '/css/customizer-repeater.css';
		$repeater_js_path  = get_stylesheet_directory() . '/js/customizer-repeater.js';
		$repeater_css_ver  = file_exists($repeater_css_path) ? filemtime($repeater_css_path) : $version;
		$repeater_js_ver   = file_exists($repeater_js_path) ? filemtime($repeater_js_path) : $version;

		wp_enqueue_style(
			'velocitychild-customizer-repeater',
			get_stylesheet_directory_uri() . '/css/customizer-repeater.css',
			array(),
			$repeater_css_ver
		);

		wp_enqueue_script(
			'velocitychild-customizer-repeater',
			get_stylesheet_directory_uri() . '/js/customizer-repeater.js',
			array('jquery', 'customize-controls', 'media-editor', 'media-views', 'editor'),
			$repeater_js_ver,
			true
		);
	}
}

if (!function_exists('velocitychild_sanitize_icon_slug')) {
	/**
	 * Sanitize icon option against available Bootstrap icon choices.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	function velocitychild_sanitize_icon_slug($value) {
		return velocitychild_normalize_service_icon($value);
	}
}

if (!function_exists('velocitychild_sanitize_home_news')) {
	/**
	 * Sanitize category ID for home news.
	 *
	 * @param mixed $value Raw value.
	 * @return int|string
	 */
	function velocitychild_sanitize_home_news($value) {
		$value = absint($value);
		return $value > 0 ? $value : '';
	}
}

if (!function_exists('velocitychild_get_slider_repeater_fields')) {
	/**
	 * Repeater fields for home slider.
	 *
	 * @return array<string, array<string, string>>
	 */
	function velocitychild_get_slider_repeater_fields() {
		return array(
			'image_id' => array(
				'type'        => 'image',
				'label'       => __('Gambar Slider', 'justg'),
				'description' => __('Pilih gambar slider.', 'justg'),
				'default'     => '',
			),
		);
	}
}

if (!function_exists('velocitychild_get_legacy_slider_items')) {
	/**
	 * Backward compatibility for old home_slider1..10 settings.
	 *
	 * @return array<int, array<string, int>>
	 */
	function velocitychild_get_legacy_slider_items() {
		$items = array();
		for ($x = 1; $x <= 10; $x++) {
			$image_id = absint(velocitytheme_option('home_slider' . $x, 0));
			if ($image_id > 0) {
				$items[] = array(
					'image_id' => $image_id,
				);
			}
		}

		return $items;
	}
}

if (!function_exists('velocitychild_sanitize_slider_repeater')) {
	/**
	 * Sanitize slider repeater values.
	 *
	 * @param mixed $value Raw value.
	 * @return array<int, array<string, int>>
	 */
	function velocitychild_sanitize_slider_repeater($value) {
		if (is_string($value)) {
			$decoded = json_decode($value, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				$value = $decoded;
			}
		}

		if (!is_array($value)) {
			return array();
		}

		$clean = array();
		foreach ($value as $item) {
			if (!is_array($item)) {
				continue;
			}

			$image_id = isset($item['image_id']) ? absint($item['image_id']) : 0;
			if ($image_id > 0) {
				$clean[] = array(
					'image_id' => $image_id,
				);
			}
		}

		return $clean;
	}
}

if (!function_exists('velocitychild_get_home_slider_items')) {
	/**
	 * Get active slider items from repeater setting with legacy fallback.
	 *
	 * @return array<int, array<string, int>>
	 */
	function velocitychild_get_home_slider_items() {
		$items_raw = get_theme_mod('home_slider_repeater', null);
		if (null === $items_raw) {
			$items = velocitychild_get_legacy_slider_items();
		} else {
			$items = velocitychild_sanitize_slider_repeater($items_raw);
		}

		return $items;
	}
}

if (!function_exists('velocitychild_get_services_repeater_fields')) {
	/**
	 * Repeater fields for home services.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function velocitychild_get_services_repeater_fields() {
		return array(
			'icon' => array(
				'type'    => 'select',
				'label'   => __('Icon', 'justg'),
				'description' => sprintf(
					__('Referensi icon: <a href="%s" target="_blank" rel="noopener noreferrer">icons.getbootstrap.com</a>', 'justg'),
					esc_url('https://icons.getbootstrap.com/')
				),
				'default' => velocitychild_get_default_service_icon(),
				'choices' => velocitychild_get_bootstrap_icon_choices(),
			),
			'image_id' => array(
				'type'    => 'image',
				'label'   => __('Background Image', 'justg'),
				'default' => '',
			),
			'text' => array(
				'type'        => 'editor',
				'label'       => __('Service Text', 'justg'),
				'description' => __('Bisa pakai HTML sederhana.', 'justg'),
				'default'     => '',
			),
			'url' => array(
				'type'    => 'url',
				'label'   => __('Service Link', 'justg'),
				'default' => '',
			),
		);
	}
}

if (!function_exists('velocitychild_get_legacy_home_services_items')) {
	/**
	 * Backward compatibility for old hs_icon/hs_img/hs_text/hs_url settings.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function velocitychild_get_legacy_home_services_items() {
		$items = array();
		for ($x = 1; $x <= 4; $x++) {
			$icon  = velocitytheme_option('hs_icon' . $x, '');
			$image = absint(velocitytheme_option('hs_img' . $x, 0));
			$text  = velocitytheme_option('hs_text' . $x, '');
			$url   = velocitytheme_option('hs_url' . $x, '');

			if ($icon || $image || $text || $url) {
				$items[] = array(
					'icon'     => velocitychild_normalize_service_icon($icon),
					'image_id' => $image,
					'text'     => (string) $text,
					'url'      => (string) $url,
				);
			}
		}

		return $items;
	}
}

if (!function_exists('velocitychild_sanitize_services_repeater')) {
	/**
	 * Sanitize home services repeater values.
	 *
	 * @param mixed $value Raw value.
	 * @return array<int, array<string, mixed>>
	 */
	function velocitychild_sanitize_services_repeater($value) {
		if (is_string($value)) {
			$decoded = json_decode($value, true);
			if (json_last_error() === JSON_ERROR_NONE) {
				$value = $decoded;
			}
		}

		if (!is_array($value)) {
			return array();
		}

		$clean = array();
		foreach ($value as $item) {
			if (!is_array($item)) {
				continue;
			}

			$icon     = isset($item['icon']) ? velocitychild_normalize_service_icon($item['icon']) : velocitychild_get_default_service_icon();
			$image_id = isset($item['image_id']) ? absint($item['image_id']) : 0;
			$text     = isset($item['text']) ? wp_kses_post((string) $item['text']) : '';
			$url      = isset($item['url']) ? esc_url_raw((string) $item['url']) : '';

			if ('' === trim(strip_tags($text)) && 0 === $image_id && '' === $url && '' === $icon) {
				continue;
			}

			$clean[] = array(
				'icon'     => $icon,
				'image_id' => $image_id,
				'text'     => $text,
				'url'      => $url,
			);
		}

		return $clean;
	}
}

if (!function_exists('velocitychild_get_home_services_items')) {
	/**
	 * Get active home services repeater items with legacy fallback.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function velocitychild_get_home_services_items() {
		$items_raw = get_theme_mod('home_services_repeater', null);
		if (null === $items_raw) {
			$items = velocitychild_get_legacy_home_services_items();
		} else {
			$items = velocitychild_sanitize_services_repeater($items_raw);
		}

		return $items;
	}
}

if (!class_exists('Velocitychild_Repeater_Control') && class_exists('WP_Customize_Control')) {
	/**
	 * Generic repeater control for WordPress customizer.
	 */
	class Velocitychild_Repeater_Control extends WP_Customize_Control {
		/**
		 * Control type.
		 *
		 * @var string
		 */
		public $type = 'velocity_repeater';

		/**
		 * Repeater fields definition.
		 *
		 * @var array<string, array<string, string>>
		 */
		public $fields = array();

		/**
		 * Item label for repeater entries.
		 *
		 * @var string
		 */
		public $item_label = '';

		/**
		 * Add button label.
		 *
		 * @var string
		 */
		public $add_button_label = '';

		/**
		 * Constructor.
		 *
		 * @param WP_Customize_Manager $manager Manager.
		 * @param string               $id      Control ID.
		 * @param array                $args    Control args.
		 * @param array                $options Options.
		 */
		public function __construct($manager, $id, $args = array(), $options = array()) {
			if (isset($args['fields'])) {
				$this->fields = (array) $args['fields'];
				unset($args['fields']);
			}
			if (isset($args['item_label'])) {
				$this->item_label = (string) $args['item_label'];
				unset($args['item_label']);
			}
			if (isset($args['add_button_label'])) {
				$this->add_button_label = (string) $args['add_button_label'];
				unset($args['add_button_label']);
			}
			parent::__construct($manager, $id, $args);
		}

		/**
		 * Render control content.
		 *
		 * @return void
		 */
		protected function render_content() {
			if (empty($this->fields)) {
				return;
			}

			$value = $this->value();
			if (is_string($value)) {
				$decoded = json_decode($value, true);
				$value   = (json_last_error() === JSON_ERROR_NONE) ? $decoded : array();
			}

			if (!is_array($value)) {
				$value = array();
			}

			$encoded_value = wp_json_encode($value);
			if (empty($encoded_value)) {
				$encoded_value = '[]';
			}
			?>
			<div class="velocity-repeater-control">
				<?php if (!empty($this->label)) : ?>
					<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
				<?php endif; ?>
				<?php if (!empty($this->description)) : ?>
					<p class="description"><?php echo wp_kses_post($this->description); ?></p>
				<?php endif; ?>

				<div class="velocity-repeater" data-fields="<?php echo esc_attr(wp_json_encode($this->fields)); ?>" data-default-label="<?php echo esc_attr($this->item_label ? $this->item_label : __('Item', 'justg')); ?>">
					<input type="hidden" class="velocity-repeater-store" <?php $this->link(); ?> value="<?php echo esc_attr($encoded_value); ?>">
					<div class="velocity-repeater-items">
						<?php
						if (!empty($value)) {
							foreach ($value as $item) {
								echo $this->get_single_item_markup($item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
						}
						?>
					</div>
					<button type="button" class="button button-primary velocity-repeater-add"><?php echo esc_html($this->add_button_label ? $this->add_button_label : __('Tambah Item', 'justg')); ?></button>
					<script type="text/html" class="velocity-repeater-template">
						<?php echo $this->get_single_item_markup(array()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</script>
				</div>
			</div>
			<?php
		}

		/**
		 * Render a repeater item.
		 *
		 * @param array<string, mixed> $item_values Item value.
		 * @return string
		 */
		private function get_single_item_markup($item_values = array()) {
			ob_start();
			$summary = $this->item_label ? $this->item_label : __('Item', 'justg');
			?>
			<div class="velocity-repeater-item">
				<button type="button" class="velocity-repeater-toggle" aria-expanded="true">
					<span class="velocity-repeater-item-label"><?php echo esc_html($summary); ?></span>
					<span class="velocity-repeater-toggle-icon" aria-hidden="true"></span>
				</button>
				<div class="velocity-repeater-item-body">
					<?php foreach ($this->fields as $field_key => $field) :
						$field_type    = isset($field['type']) ? $field['type'] : 'text';
						$field_label   = isset($field['label']) ? $field['label'] : '';
						$field_default = isset($field['default']) ? $field['default'] : '';
						$field_desc    = isset($field['description']) ? $field['description'] : '';
						$field_value   = isset($item_values[$field_key]) ? $item_values[$field_key] : $field_default;

						if ('image' === $field_type) :
							$image_id  = absint($field_value);
							$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
							?>
							<div class="velocity-repeater-field">
								<span class="velocity-repeater-field-label"><?php echo esc_html($field_label); ?></span>
								<div class="velocity-repeater-image-field">
									<input type="hidden" data-field="<?php echo esc_attr($field_key); ?>" data-default="<?php echo esc_attr($field_default); ?>" value="<?php echo esc_attr($image_id); ?>">
									<div class="velocity-repeater-image-preview<?php echo $image_url ? ' has-image' : ''; ?>">
										<?php if ($image_url) : ?>
											<img src="<?php echo esc_url($image_url); ?>" alt="">
										<?php endif; ?>
									</div>
									<div class="velocity-repeater-image-actions">
										<button type="button" class="button velocity-repeater-media-select"><?php esc_html_e('Pilih Gambar', 'justg'); ?></button>
										<button type="button" class="button-link button-link-delete velocity-repeater-media-remove"><?php esc_html_e('Hapus', 'justg'); ?></button>
									</div>
								</div>
								<?php if (!empty($field_desc)) : ?>
									<span class="description customize-control-description"><?php echo wp_kses_post($field_desc); ?></span>
								<?php endif; ?>
							</div>
						<?php elseif ('editor' === $field_type) : ?>
							<label class="velocity-repeater-field">
								<span class="velocity-repeater-field-label"><?php echo esc_html($field_label); ?></span>
								<textarea class="velocity-repeater-editor" rows="6" data-field="<?php echo esc_attr($field_key); ?>" data-default="<?php echo esc_attr($field_default); ?>"><?php echo esc_textarea((string) $field_value); ?></textarea>
								<?php if (!empty($field_desc)) : ?>
									<span class="description customize-control-description"><?php echo wp_kses_post($field_desc); ?></span>
								<?php endif; ?>
							</label>
						<?php elseif ('textarea' === $field_type) : ?>
							<label class="velocity-repeater-field">
								<span class="velocity-repeater-field-label"><?php echo esc_html($field_label); ?></span>
								<textarea data-field="<?php echo esc_attr($field_key); ?>" data-default="<?php echo esc_attr($field_default); ?>"><?php echo esc_textarea((string) $field_value); ?></textarea>
								<?php if (!empty($field_desc)) : ?>
									<span class="description customize-control-description"><?php echo wp_kses_post($field_desc); ?></span>
								<?php endif; ?>
							</label>
						<?php elseif ('select' === $field_type) : ?>
							<?php $choices = isset($field['choices']) && is_array($field['choices']) ? $field['choices'] : array(); ?>
							<label class="velocity-repeater-field">
								<span class="velocity-repeater-field-label"><?php echo esc_html($field_label); ?></span>
								<select data-field="<?php echo esc_attr($field_key); ?>" data-default="<?php echo esc_attr($field_default); ?>">
									<?php foreach ($choices as $choice_value => $choice_label) : ?>
										<option value="<?php echo esc_attr((string) $choice_value); ?>" <?php selected((string) $field_value, (string) $choice_value); ?>><?php echo esc_html((string) $choice_label); ?></option>
									<?php endforeach; ?>
								</select>
								<?php if (!empty($field_desc)) : ?>
									<span class="description customize-control-description"><?php echo wp_kses_post($field_desc); ?></span>
								<?php endif; ?>
							</label>
						<?php else : ?>
							<label class="velocity-repeater-field">
								<span class="velocity-repeater-field-label"><?php echo esc_html($field_label); ?></span>
								<input type="<?php echo esc_attr($field_type); ?>" data-field="<?php echo esc_attr($field_key); ?>" data-default="<?php echo esc_attr($field_default); ?>" value="<?php echo esc_attr((string) $field_value); ?>">
								<?php if (!empty($field_desc)) : ?>
									<span class="description customize-control-description"><?php echo wp_kses_post($field_desc); ?></span>
								<?php endif; ?>
							</label>
						<?php endif; ?>
					<?php endforeach; ?>
					<div class="velocity-repeater-actions">
						<button type="button" class="button velocity-repeater-clone"><?php esc_html_e('Clone', 'justg'); ?></button>
						<button type="button" class="button button-secondary velocity-repeater-remove"><?php esc_html_e('Hapus', 'justg'); ?></button>
					</div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}
	}
}

if (!function_exists('velocitychild_customize_register')) {
	/**
	 * Child theme customizer settings without Kirki.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer manager.
	 * @return void
	 */
	function velocitychild_customize_register(WP_Customize_Manager $wp_customize) {
		$textdomain = 'justg';

		$kategori = array(
			'' => esc_html__('Show All', $textdomain),
		);

		$categories = get_categories(
			array(
				'orderby'    => 'name',
				'hide_empty' => false,
			)
		);

		foreach ($categories as $category) {
			$kategori[$category->term_id] = $category->name;
		}

		if (!$wp_customize->get_panel('panel_velocity')) {
			$wp_customize->add_panel(
				'panel_velocity',
				array(
					'priority'    => 10,
					'title'       => esc_html__('Velocity Theme', $textdomain),
					'description' => '',
				)
			);
		}

		$site_identity_section = $wp_customize->get_section('title_tagline');
		if ($site_identity_section) {
			$site_identity_section->panel    = 'panel_velocity';
			$site_identity_section->priority = 10;
			$site_identity_section->title    = __('Site Identity', $textdomain);
		}

		$wp_customize->add_section(
			'panel_contact',
			array(
				'panel'    => 'panel_velocity',
				'title'    => __('Header Contact', $textdomain),
				'priority' => 11,
			)
		);

		$wp_customize->add_setting(
			'contact_email',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'contact_email',
			array(
				'type'    => 'text',
				'label'   => __('Email', $textdomain),
				'section' => 'panel_contact',
			)
		);

		$wp_customize->add_setting(
			'contact_phone',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'contact_phone',
			array(
				'type'    => 'text',
				'label'   => __('Telepon', $textdomain),
				'section' => 'panel_contact',
			)
		);

		$wp_customize->add_setting(
			'contact_wa',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'contact_wa',
			array(
				'type'    => 'text',
				'label'   => __('Whatsapp', $textdomain),
				'section' => 'panel_contact',
			)
		);

		$wp_customize->add_section(
			'section_homeslider',
			array(
				'panel'    => 'panel_velocity',
				'title'    => __('Home Slider', $textdomain),
				'priority' => 12,
			)
		);

		$wp_customize->add_setting(
			'home_slider_repeater',
			array(
				'default'           => velocitychild_get_legacy_slider_items(),
				'sanitize_callback' => 'velocitychild_sanitize_slider_repeater',
			)
		);
		$wp_customize->add_control(
			new Velocitychild_Repeater_Control(
				$wp_customize,
				'home_slider_repeater',
				array(
					'label'       => __('Daftar Slider', $textdomain),
					'description' => esc_html__('Tambah, clone, dan hapus gambar slider.', $textdomain),
					'section'     => 'section_homeslider',
					'priority'    => 5,
					'fields'      => velocitychild_get_slider_repeater_fields(),
					'item_label'  => __('Slide', $textdomain),
					'add_button_label' => __('Tambah Slide', $textdomain),
				)
			)
		);

		$wp_customize->add_section(
			'section_homeservice',
			array(
				'panel'    => 'panel_velocity',
				'title'    => __('Home Services', $textdomain),
				'priority' => 13,
			)
		);

		$wp_customize->add_setting(
			'home_services_repeater',
			array(
				'default'           => velocitychild_get_legacy_home_services_items(),
				'sanitize_callback' => 'velocitychild_sanitize_services_repeater',
			)
		);
		$wp_customize->add_control(
			new Velocitychild_Repeater_Control(
				$wp_customize,
				'home_services_repeater',
				array(
					'label'            => __('Daftar Layanan', $textdomain),
					'description'      => esc_html__('Tambah, clone, dan hapus layanan sesuai kebutuhan.', $textdomain),
					'section'          => 'section_homeservice',
					'priority'         => 5,
					'fields'           => velocitychild_get_services_repeater_fields(),
					'item_label'       => __('Layanan', $textdomain),
					'add_button_label' => __('Tambah Layanan', $textdomain),
				)
			)
		);

		$wp_customize->add_section(
			'section_homenews',
			array(
				'panel'    => 'panel_velocity',
				'title'    => __('Home News', $textdomain),
				'priority' => 14,
			)
		);

		$wp_customize->add_setting(
			'hn_title',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'hn_title',
			array(
				'type'    => 'text',
				'label'   => esc_html__('Title', $textdomain),
				'section' => 'section_homenews',
			)
		);

		$wp_customize->add_setting(
			'home_news',
			array(
				'default'           => '',
				'sanitize_callback' => 'velocitychild_sanitize_home_news',
			)
		);
		$wp_customize->add_control(
			'home_news',
			array(
				'type'    => 'select',
				'label'   => esc_html__('Post Category', $textdomain),
				'section' => 'section_homenews',
				'choices' => $kategori,
			)
		);

		// Remove unused parent/legacy panels and controls.
		$wp_customize->remove_panel('global_panel');
		$wp_customize->remove_panel('panel_header');
		$wp_customize->remove_panel('panel_footer');
		$wp_customize->remove_panel('panel_antispam');
		$wp_customize->remove_control('display_header_text');
		$wp_customize->remove_section('header_image');
		$wp_customize->remove_section('section_colorvelocity');
	}
}

function velocitychild_theme_setup()
{

	// Load justg_child_enqueue_parent_style after theme setup
	add_action('wp_enqueue_scripts', 'justg_child_enqueue_parent_style', 20);

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
if (!function_exists('velocity_custom_excerpt_more')) {
	function velocity_custom_excerpt_more($more)
	{
		return '...';
	}
}
add_filter('excerpt_more', 'velocity_custom_excerpt_more');

// excerpt length
function velocity_excerpt_length($length)
{
	return 40;
}
add_filter('excerpt_length', 'velocity_excerpt_length');


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
				'show_in_rest'  => false,
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
