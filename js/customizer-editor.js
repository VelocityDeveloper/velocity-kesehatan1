(function ($) {
	'use strict';

	function initEditor($textarea) {
		if (!$textarea.length || $textarea.data('velocitychildEditorInit')) {
			return;
		}

		if (typeof wp === 'undefined' || !wp.editor || !wp.editor.initialize) {
			return;
		}

		var editorId = $textarea.attr('id');
		if (!editorId) {
			return;
		}

		wp.editor.initialize(editorId, {
			tinymce: {
				wpautop: true,
				toolbar1: 'bold italic bullist numlist link unlink undo redo',
				toolbar2: '',
				setup: function (editor) {
					editor.on('change keyup NodeChange', function () {
						editor.save();
						$textarea.trigger('change');
					});
				}
			},
			quicktags: true,
			mediaButtons: false
		});

		$textarea.data('velocitychildEditorInit', true);
	}

	function bootEditors() {
		$('.velocitychild-editor-field').each(function () {
			initEditor($(this));
		});
	}

	$(document).ready(function () {
		window.setTimeout(bootEditors, 120);
	});

	if (typeof wp !== 'undefined' && wp.customize) {
		wp.customize.bind('ready', function () {
			window.setTimeout(bootEditors, 180);
		});
	}
}(jQuery));
