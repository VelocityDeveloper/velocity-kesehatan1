(function (api, $) {
	'use strict';

	function VelocityRepeater(control) {
		var self = this;
		this.control = control;
		this.$container = control.container.find('.velocity-repeater');
		this.$store = this.$container.find('.velocity-repeater-store');
		this.$itemsWrap = this.$container.find('.velocity-repeater-items');
		this.template = (this.$container.find('.velocity-repeater-template').html() || '').trim();
		this.defaultLabel = this.$container.data('default-label') || '';
		this.editorIndex = 0;
		this.$container.find('.velocity-repeater-template').remove();
		this.bindEvents();

		if (!this.$itemsWrap.children().length) {
			this.addItem({}, null, true);
		}

		this.updateSummaries();
		this.$itemsWrap.children('.velocity-repeater-item').each(function () {
			self.initEditorsInItem($(this));
			self.toggleItem($(this), false);
		});
	}

	VelocityRepeater.prototype.bindEvents = function () {
		var self = this;

		self.$container.on('click', '.velocity-repeater-add', function (event) {
			event.preventDefault();
			self.addItem({}, null, true);
		});

		self.$container.on('click', '.velocity-repeater-remove', function (event) {
			event.preventDefault();
			var $item = $(this).closest('.velocity-repeater-item');
			self.removeEditorsInItem($item);
			$item.remove();
			self.sync();
			self.updateSummaries();
		});

		self.$container.on('click', '.velocity-repeater-clone', function (event) {
			event.preventDefault();
			var $item = $(this).closest('.velocity-repeater-item');
			var values = self.readItem($item);
			self.addItem(values, $item, true);
		});

		self.$container.on('click', '.velocity-repeater-toggle', function (event) {
			event.preventDefault();
			var $item = $(this).closest('.velocity-repeater-item');
			self.toggleItem($item);
		});

		self.$container.on('input change', '[data-field]', function () {
			self.sync();
			self.updateSummaries();
		});

		self.$container.on('click', '.velocity-repeater-media-select', function (event) {
			event.preventDefault();
			var $field = $(this).closest('.velocity-repeater-image-field').find('input[data-field]').first();
			self.openMediaFrame($field);
		});

		self.$container.on('click', '.velocity-repeater-media-remove', function (event) {
			event.preventDefault();
			var $field = $(this).closest('.velocity-repeater-image-field').find('input[data-field]').first();
			self.setImage($field, '', '');
			self.sync();
			self.updateSummaries();
		});
	};

	VelocityRepeater.prototype.createItem = function (values) {
		var self = this;
		var $item = $(this.template);
		values = values || {};

		$item.find('[data-field]').each(function () {
			var $field = $(this);
			var key = $field.data('field');
			var defaultValue = $field.data('default') || '';
			var value = Object.prototype.hasOwnProperty.call(values, key) ? values[key] : defaultValue;
			$field.val(value);

			if ($field.closest('.velocity-repeater-image-field').length) {
				var imageUrlKey = key + '_url';
				var imageUrl = Object.prototype.hasOwnProperty.call(values, imageUrlKey) ? values[imageUrlKey] : '';
				self.renderImagePreview($field, imageUrl);
			}
		});

		return $item;
	};

	VelocityRepeater.prototype.addItem = function (values, after, openByDefault) {
		var $item = this.createItem(values);

		if (after && after.length) {
			$item.insertAfter(after);
		} else {
			this.$itemsWrap.append($item);
		}

		this.initEditorsInItem($item);
		this.toggleItem($item, !!openByDefault);
		this.sync();
		this.updateSummaries();
		return $item;
	};

	VelocityRepeater.prototype.readItem = function ($item) {
		var values = {};

		$item.find('[data-field]').each(function () {
			var $field = $(this);
			var key = $field.data('field');
			values[key] = $field.val();

			var $previewImage = $field.closest('.velocity-repeater-image-field').find('.velocity-repeater-image-preview img');
			if ($previewImage.length) {
				values[key + '_url'] = $previewImage.attr('src') || '';
			}
		});

		return values;
	};

	VelocityRepeater.prototype.getEditorId = function ($field) {
		var id = $field.attr('id');
		if (id) {
			return id;
		}

		this.editorIndex += 1;
		id = 'velocity_repeater_editor_' + String(this.control.id || 'control').replace(/[^a-z0-9_]/gi, '_') + '_' + this.editorIndex;
		$field.attr('id', id);
		return id;
	};

	VelocityRepeater.prototype.initEditor = function ($field) {
		var self = this;
		var editorId;

		if (!$field || !$field.length || $field.data('velocityEditorInit')) {
			return;
		}

		if (typeof wp === 'undefined' || !wp.editor || !wp.editor.initialize) {
			return;
		}

		editorId = self.getEditorId($field);
		wp.editor.initialize(editorId, {
			tinymce: {
				wpautop: true,
				toolbar1: 'bold italic bullist numlist link unlink undo redo',
				toolbar2: '',
				setup: function (editor) {
					editor.on('change keyup NodeChange', function () {
						editor.save();
						$field.trigger('input').trigger('change');
					});
				}
			},
			quicktags: true,
			mediaButtons: false
		});

		$field.data('velocityEditorInit', true);
	};

	VelocityRepeater.prototype.initEditorsInItem = function ($item) {
		var self = this;
		$item.find('.velocity-repeater-editor').each(function () {
			self.initEditor($(this));
		});
	};

	VelocityRepeater.prototype.removeEditorsInItem = function ($item) {
		if (typeof wp === 'undefined' || !wp.editor || !wp.editor.remove) {
			return;
		}

		$item.find('.velocity-repeater-editor').each(function () {
			var editorId = $(this).attr('id');
			if (editorId) {
				wp.editor.remove(editorId);
			}
		});
	};

	VelocityRepeater.prototype.saveEditorsInItem = function ($item) {
		if (typeof tinymce === 'undefined') {
			return;
		}

		$item.find('.velocity-repeater-editor').each(function () {
			var editorId = $(this).attr('id');
			var editor = editorId ? tinymce.get(editorId) : null;
			if (editor) {
				editor.save();
			}
		});
	};

	VelocityRepeater.prototype.toggleItem = function ($item, forceOpen) {
		var shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : $item.hasClass('is-collapsed');

		if (shouldOpen) {
			$item.removeClass('is-collapsed');
			$item.find('.velocity-repeater-toggle').attr('aria-expanded', 'true');
		} else {
			$item.addClass('is-collapsed');
			$item.find('.velocity-repeater-toggle').attr('aria-expanded', 'false');
		}
	};

	VelocityRepeater.prototype.updateSummary = function ($item, index) {
		var summary = this.defaultLabel || 'Slide';
		var order = typeof index === 'number' ? (index + 1) : '';
		if (order) {
			summary = summary + ' ' + order;
		}

		$item.find('.velocity-repeater-item-label').text(summary);
	};

	VelocityRepeater.prototype.updateSummaries = function () {
		var self = this;
		this.$itemsWrap.children('.velocity-repeater-item').each(function (index) {
			self.updateSummary($(this), index);
		});
	};

	VelocityRepeater.prototype.renderImagePreview = function ($field, imageUrl) {
		var $preview = $field.closest('.velocity-repeater-image-field').find('.velocity-repeater-image-preview');
		var safeUrl = (imageUrl || '').toString();
		$preview.empty();

		if (safeUrl) {
			$preview.append($('<img>', { src: safeUrl, alt: '' }));
			$preview.addClass('has-image');
		} else {
			$preview.removeClass('has-image');
		}
	};

	VelocityRepeater.prototype.setImage = function ($field, imageId, imageUrl) {
		if (!$field || !$field.length) {
			return;
		}

		$field.val(imageId);
		this.renderImagePreview($field, imageUrl);
		$field.trigger('change');
	};

	VelocityRepeater.prototype.openMediaFrame = function ($field) {
		var self = this;
		if (typeof wp === 'undefined' || !wp.media) {
			return;
		}

		var frame = wp.media({
			title: 'Pilih gambar',
			multiple: false,
			library: {
				type: 'image'
			},
			button: {
				text: 'Gunakan gambar'
			}
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var imageId = attachment.id || '';
			var imageUrl = attachment.url || '';

			if (attachment.sizes) {
				if (attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) {
					imageUrl = attachment.sizes.thumbnail.url;
				} else if (attachment.sizes.medium && attachment.sizes.medium.url) {
					imageUrl = attachment.sizes.medium.url;
				}
			}

			self.setImage($field, imageId, imageUrl);
			self.sync();
			self.updateSummaries();
		});

		frame.open();
	};

	VelocityRepeater.prototype.sync = function () {
		var self = this;
		var data = [];

		this.$itemsWrap.children('.velocity-repeater-item').each(function () {
			self.saveEditorsInItem($(this));

			var row = {};
			var isEmpty = true;

			$(this).find('[data-field]').each(function () {
				var $field = $(this);
				var key = $field.data('field');
				var value = $field.val();

				row[key] = value;
				if (value) {
					isEmpty = false;
				}
			});

			if (!isEmpty) {
				data.push(row);
			}
		});

		this.$store.val(JSON.stringify(data)).trigger('change');
	};

	function initControl(control) {
		if (!control || control.velocityRepeaterInitialized) {
			return;
		}

		control.velocityRepeaterInitialized = true;
		new VelocityRepeater(control);
	}

	api.controlConstructor.velocity_repeater = api.Control.extend({
		ready: function () {
			initControl(this);
		}
	});

	api.bind('ready', function () {
		api.control.each(function (control) {
			if ('velocity_repeater' === control.params.type) {
				initControl(control);
			}
		});
	});
}(wp.customize, jQuery));
