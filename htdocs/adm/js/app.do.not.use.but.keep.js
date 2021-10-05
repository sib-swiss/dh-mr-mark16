"use strict";

// app code - Boot stuff when DOM is loaded
$(function () {
	console.group('App');
	console.log('DOM Loaded.');
	console.log('Settings:', (typeof $.site.settings !== undefined ? $.site.settings : null));
	console.log('Location:', UI.getCurrentLocation());
	console.groupEnd();

	// Dirty edit warning on load
	// TODO: Add page detection to display it only for the edit page
	if (!UI.getValue('warning-displayed') || UI.getValue('warning-displayed') !== 'true') {
		UI.createToast('All the changes made here won\'t be replicated on Nakala!', 'warning');
		UI.storeValue('warning-displayed', true);
	}

	// Initialize forms validation
	$('.ui.form').form();

	// Search select dropdown callbacks
	$('.ui.search.selection.dropdown').dropdown({
		onChange: function(value, text, $choice) {
			console.group('Fomantic-UI Dropdown - Search');
			var state = {
				url: value,
				name: text,
				target: $choice
			};
			console.log('Manuscript selected.', state);
			console.groupEnd();

			UI.createToast('Loading manuscript <strong>' + state.name + '</strong>...');
			window.location.href = state.url;
		}
	});

	// Tab menu callbacks
	// The 'history' feature requires:
	// https://cdnjs.com/libraries/jquery.address
	$('.ui.tabular.menu .item').tab({
		cache: false,
		history: true,
		onLoad: function(tabPath, parameterArray, historyEvent) {
			console.group('Fomantic-UI Tab - Load');
			var state = {
				path: tabPath,
				params: parameterArray,
				event: historyEvent
			};
			console.log('Tab loaded.', state);
			console.groupEnd();

			// Toggle action buttons display
			switch (state.path) {
				case 'images':
				case 'partners':
					$('.ui.stateful.button').hide('slow');
					break;

				case 'presentation':
					$('.ui.stateful.button.gen').show('slow');
					$('.ui.stateful.button.sync').show('slow');
					$('.ui.stateful.button.sub').hide('slow');
					break;
			
				default:
					$('.ui.stateful.button.gen').show('slow');
					$('.ui.stateful.button.sync').hide('slow');
					$('.ui.stateful.button.sub').show('slow');
					break;
			}
		}
	});

	// Image edit modal handler
	$('.ui.edit-image.modal').modal({
		onHidden: function() {
			console.group('Fomantic-UI Modal - Image - Edit');
			console.log('Image modal is closed.');
			console.groupEnd();

			// Clear visible form
			$('.ui.form:visible').form('clear');

			// Detach "change" event on every input file elements
			$('input[type=file]').off('change');
			
			// Clear tab cache
			// $('.ui.tabular.menu .item').tab('cache remove', 'images');

			// Reload tab
			// $('.ui.tabular.menu .item').tab('change tab', 'images');

			// Dirty page reload...
			// TODO: improve tabs caching management
			if (UI.getValue('image-updated') === 'true') {
				UI.createToast('Reloading page...');
				window.location.reload();
			}
		},
		onVisible: function() {
			console.group('Fomantic-UI Modal - Image - Edit');
			console.log('Image modal is open.');
			console.groupEnd();

			// Remove previous update status
			UI.removeValue('image-updated');

			// Input file elements
			// Attach "change" event on visibile input file element only
			$('input[type=file]:visible').on('change', function(event) {
				console.group('App - Upload');
				console.log('User selected file for upload.', event);

				// Get server rendered ids
				var entry   = event.target.id.replace('manuscript_folio_image_', 'manuscript_folio_image_entry_'),
					size    = event.target.id.replace('manuscript_folio_image_', 'manuscript_folio_image_size_'),
					type    = event.target.id.replace('manuscript_folio_image_', 'manuscript_folio_image_type_'),
					content = event.target.id.replace('manuscript_folio_image_', 'manuscript_folio_image_content_'),
					metas   = event.target.id.replace('manuscript_folio_image_', 'manuscript_folio_image_metas_');

				// Get selected file
				var file    = event.target.files[0];

				// Update UI with received file size
				UI.updateFileSize(event.target, true, {"size": size});
				// UI.updateFileSize(event.target, true, {"entry": entry, "size": size});

				// Update UI with image type selector
				var $inputFileLabel = $('#' + entry + ''),
					$inputFileType  = $('#' + type + '');

				// Avoid reloading animation if already visible
				if (!$inputFileLabel.transition('is visible')) {
					$inputFileLabel.transition('fade down');
					$inputFileType.transition('fade down');
				}

				// Display received file size in console
				console.log('Received:', UI.getFileSize(event.target, true));

				// Upload the file without the FUI API
				// UI.sendFiles(event.target.files, window.location.href, $('.ui.form:visible')[0], 'uploaded_file');

				// Gather selected file properties
				$('#' + metas + '').val(JSON.stringify({
					"name": file.name,
					"size": file.size,
					"type": file.type,
					"lastModified": file.lastModified
				}));

				// Gather selected file content
				var reader = new FileReader();
				reader.onload = function(event) {
					console.group('File Reader');

					// Debug received data
					console.log('Data type:', typeof event.target.result);
	
					// Encode content binary string and set as hidden field value
					$('#' + content + '').val(window.btoa(event.target.result));
	
					// Debug encoded data
					console.log('Image data:', window.btoa(event.target.result));

					console.groupEnd();
				};
				reader.readAsBinaryString(file);
				
				console.groupEnd();
			});

			// Radio buttons
			var $radioCopyrightSelector = $('.ui.radio.checkbox.copyrighted:visible');
			var $imageCopyrightTextarea = $('.manuscript.folio.image.copyright:visible');
			$('.ui.radio.checkbox:visible').checkbox({
				onChange: function() {
					console.group('Fomantic-UI Modal - Image - Radio');
					console.log('Changed:', $radioCopyrightSelector, $radioCopyrightSelector.checkbox('is checked'));
					console.groupEnd();
				},
				onChecked: function() {
					// Execute actions when 'copyrighted' type is checked
					if ($radioCopyrightSelector.checkbox('is checked')) {
						// Disable textarea field
						$imageCopyrightTextarea.addClass('disabled');
						$imageCopyrightTextarea.parent().removeClass('required');
						$imageCopyrightTextarea.attr('disabled', true);
						$imageCopyrightTextarea.attr('required', false);
					}
					else {
						// Enable textarea field
						$imageCopyrightTextarea.removeClass('disabled');
						$imageCopyrightTextarea.parent().addClass('required');
						$imageCopyrightTextarea.attr('disabled', false);
						$imageCopyrightTextarea.attr('required', true);
					}
				}
			});
		}
	});

	// Partner image edit modal handler
	$('.ui.edit-partner.modal').modal({
		onHidden: function() {
			console.group('Fomantic-UI Modal - Partner - Edit');
			console.log('Partner modal is closed.');
			console.groupEnd();

			// Clear visible form
			$('.ui.form:visible').form('clear');

			// Detach "change" event on every input file elements
			$('input[type=file]').off('change');
			
			// Clear tab cache
			// $('.ui.tabular.menu .item').tab('cache remove', 'partner');

			// Reload tab
			// $('.ui.tabular.menu .item').tab('change tab', 'partner');

			// Dirty page reload...
			// TODO: improve tabs caching management
			if (UI.getValue('partner-updated') === 'true') {
				UI.createToast('Reloading page...');
				window.location.reload();
			}
		},
		onVisible: function() {
			console.group('Fomantic-UI Modal - Partner - Edit');
			console.log('Partner modal is open.');
			console.groupEnd();

			// Remove previous update status
			UI.removeValue('partner-updated');

			// Input file elements
			// Attach "change" event on visibile input file element only
			$('input[type=file]:visible').on('change', function(event) {
				console.group('App - Upload');
				console.log('User selected file for upload.', event);

				// Get server rendered ids
				var entry   = event.target.id.replace('manuscript_partner_image_', 'manuscript_partner_image_entry_'),
					size    = event.target.id.replace('manuscript_partner_image_', 'manuscript_partner_image_size_'),
					content = event.target.id.replace('manuscript_partner_image_', 'manuscript_partner_image_content_'),
					metas   = event.target.id.replace('manuscript_partner_image_', 'manuscript_partner_image_metas_');

				// Get selected file
				var file    = event.target.files[0];

				// Update UI with received file size
				UI.updateFileSize(event.target, true, {"size": size});
				// UI.updateFileSize(event.target, true, {"entry": entry, "size": size});

				// Update UI with image type selector
				var $inputFileLabel = $('#' + entry + '');

				// Avoid reloading animation if already visible
				if (!$inputFileLabel.transition('is visible')) {
					$inputFileLabel.transition('fade down');
				}

				// Display received file size in console
				console.log('Received:', UI.getFileSize(event.target, true));

				// Upload the file without the FUI API
				// UI.sendFiles(event.target.files, window.location.href, $('.ui.form:visible')[0], 'uploaded_file');

				// Gather selected file properties
				$('#' + metas + '').val(JSON.stringify({
					"name": file.name,
					"size": file.size,
					"type": file.type,
					"lastModified": file.lastModified
				}));

				// Gather selected file content
				var reader = new FileReader();
				reader.onload = function(event) {
					console.group('File Reader');

					// Debug received data
					console.log('Data type:', typeof event.target.result);
	
					// Encode content binary string and set as hidden field value
					$('#' + content + '').val(window.btoa(event.target.result));
	
					// Debug encoded data
					console.log('Image data:', window.btoa(event.target.result));

					console.groupEnd();
				};
				reader.readAsBinaryString(file);
				
				console.groupEnd();
			});
		}
	});

	// Partner image upload modal handler
	$('.ui.upload.partner.modal').modal({
		onHidden: function() {
			console.group('Fomantic-UI Modal - Partner - Upload');
			console.log('Partner modal is closed.');
			console.groupEnd();

			// Clear visible form
			$('.ui.form:visible').form('clear');

			// Detach "change" event on every input file elements
			$('input[type=file]').off('change');
			
			// Clear tab cache
			// $('.ui.tabular.menu .item').tab('cache remove', 'partner');

			// Reload tab
			// $('.ui.tabular.menu .item').tab('change tab', 'partner');

			// Dirty page reload...
			// TODO: improve tabs caching management
			if (UI.getValue('partner-uploaded') === 'true') {
				UI.createToast('Reloading page...');
				window.location.reload();
			}
		},
		onVisible: function() {
			console.group('Fomantic-UI Modal - Partner - Upload');
			console.log('Partner modal is open.');
			console.groupEnd();

			// Remove previous update status
			UI.removeValue('partner-uploaded');

			// Add hook in visible accordion
			$('.ui.accordion:visible').accordion({
				onClose: function() {
					console.group('Fomantic-UI Accordion - Partner - Upload');
					console.log('Accordion is closed.');
					console.groupEnd();
				},
				onOpening: function() {
					console.group('Fomantic-UI Accordion - Partner - Upload');
					console.log('Accordion is opening.');
					console.groupEnd();

					/* $('.ui.pointing.blue.basic.label')
						.removeClass('visible')
						.addClass('hidden')
						.css('display', ''); */
				},
				onOpen: function() {
					console.group('Fomantic-UI Accordion - Partner - Upload');
					console.log('Accordion is open.');
					console.groupEnd();

					// Input file elements
					// Attach "change" event on visibile input file element only
					$('input[type=file]:visible').on('change', function(event) {
						console.group('App - Upload');
						console.log('User selected file for upload.', event);

						// Get server rendered ids
						var entry   = event.target.id.replace('manuscript_partner_image_file_', 'manuscript_partner_image_entry_'),
							size    = event.target.id.replace('manuscript_partner_image_file_', 'manuscript_partner_image_size_'),
							content = event.target.id.replace('manuscript_partner_image_file_', 'manuscript_partner_image_content_'),
							metas   = event.target.id.replace('manuscript_partner_image_file_', 'manuscript_partner_image_metas_');

						// Get selected file
						var file    = event.target.files[0];

						// Update UI with received file size
						UI.updateFileSize(event.target, true, {"size": size});
						// UI.updateFileSize(event.target, true, {"entry": entry, "size": size});

						// Update UI with image type selector
						var $inputFileLabel = $('#' + entry + '');

						// Avoid reloading animation if already visible
						/* if (!$inputFileLabel.transition('is visible')) {
							$inputFileLabel.transition('fade down');
						} */

						// Display size label without animation
						$inputFileLabel.css('display', 'inline-block');

						// Display received file size in console
						console.log('Received:', UI.getFileSize(event.target, true));

						// Upload the file without the FUI API
						// UI.sendFiles(event.target.files, window.location.href, $('.ui.form:visible')[0], 'uploaded_file');

						// Gather selected file properties
						$('#' + metas + '').val(JSON.stringify({
							"name": file.name,
							"size": file.size,
							"type": file.type,
							"lastModified": file.lastModified
						}));

						// Gather selected file content
						var reader = new FileReader();
						reader.onload = function(event) {
							console.group('File Reader');

							// Debug received data
							console.log('Data type:', typeof event.target.result);
			
							// Encode content binary string and set as hidden field value
							$('#' + content + '').val(window.btoa(event.target.result));
			
							// Debug encoded data
							console.log('Image data:', window.btoa(event.target.result));

							console.groupEnd();
						};
						reader.readAsBinaryString(file);
						
						console.groupEnd();
					});
				}
			});
		}
	});

	// Remove passed data
	// jQuery.removeData($('.ui.ok.delete.manuscript'), 'id');

	// Modal actions
	/* $('.ui.delete.modal').modal({
		closable  : false,
		onDeny    : function(){
			// window.alert('Wait not yet!');
			// return false;
		},
		onApprove : function() {
			// window.alert('Approved!');
			$('.ui.ok.delete.manuscript').api({
				action: 'delete manuscript',
				urlData: {
					id: $('.ui.ok.delete.manuscript').data('id')
				}
			});

			// Remove passed data
			jQuery.removeData($('.ui.ok.delete.manuscript'), 'id');
		}
	}); */
});
