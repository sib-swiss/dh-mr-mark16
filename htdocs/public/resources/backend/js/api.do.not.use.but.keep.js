"use strict";

// api code - Boot stuff when DOM is loaded
$(function () {
	// Client API Routes / Server Routes
	$.fn.api.settings.api = {
		'nakala download' : 'admin/download/{id}',
		'delete manuscript' : 'admin/delete/{id}',
		'manuscript image upload' : 'admin/edit/{id}',
		'partner image upload' : 'admin/edit/{id}'
	};

	// Debug Client API Routes
	console.group('Fomantic-UI API');
	console.log('Config:', (typeof $.fn.api.settings !== undefined ? $.fn.api.settings : null));
	console.log('Location:', UI.getCurrentLocation());
	console.groupEnd();

	// Client API Actions
	var parsedPath = UI.getCurrentLocation().pathname;

	// -- Manuscript partner image upload button
	$('.ui.form .ui.blue.upload.button').api({
		base: (parsedPath.includes('/htdocs') ? '/htdocs/' : '/'),
		method: 'POST',
		serializeForm: true,
		onResponse: function(response) {
			// make some adjustments to response
			// return response;
			console.group('Fomantic-UI API');
			console.log('Response:', response);
			console.groupEnd();
		},
		successTest: function(response) {
			// test whether a JSON response is valid
			console.group('Fomantic-UI API');
			console.log('successTest:', response.success || false);
			console.groupEnd();
			return response.success || false;
		},
		onComplete: function(response, element, xhr) {
			// always called after XHR complete
			console.group('Fomantic-UI API');
			console.log('onComplete:', response, element, xhr);
			console.groupEnd();

			if (response.success === true) {
				var displayDelay = 1000;
				var showReloadTimeout = setTimeout(function () {
					// Dirty page reload...
					// TODO: improve tabs caching management
					clearTimeout(showReloadTimeout);
					if (UI.getValue('partner-uploaded') === 'true') {
						UI.createToast('Reloading page...');
						window.location.reload();
					}
				}, displayDelay);

				// UI.createToast('Updating displayed image...');

				// Delete holder.js image
				// $('.image.deleteable:visible').remove();
				
				// Store currently displayed image
				// var oldImageSrc = $('.image.updateable:visible').attr('src');

				// Update displayed image
				/* $('.image.updateable:visible')
					.attr('src', 'data:image/jpeg;base64,' + response.fileContent)
					.css('margin', '0 auto')
					.css('width', '62%')
					.css('height', 'auto')
					.transition('fade in'); */

				// Store new displayed image
				// var newImageSrc = $('.image.updateable:visible').attr('src');

				// Check displayed image source
				/* if (oldImageSrc !== newImageSrc) {
					UI.createToast('Displayed image updated.', 'success');
				}
				else {
					UI.createToast('Displayed image not updated.', 'warning');
				} */
			}
		},
		onSuccess: function(response, element, xhr) {
			// valid response and response.success = true
			console.group('Fomantic-UI API');
			console.log('onSuccess:', response, element, xhr);
			console.groupEnd();

			UI.createToast(response.message, 'success');

			// Save update event
			UI.storeValue('partner-uploaded', true);
		},
		onFailure: function(response, element, xhr) {
			// request failed, or valid response but response.success = false
			console.group('Fomantic-UI API');
			console.error('onFailure:', response, element, xhr);
			console.groupEnd();

			UI.createToast(response.message, 'error');
		},
		onError: function(errorMessage, element, xhr) {
			// invalid response
			console.group('Fomantic-UI API');
			console.error('onError:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'error');
		},
		onAbort: function(errorMessage, element, xhr) {
			// navigated to a new page, CORS issue, or user canceled request
			console.group('Fomantic-UI API');
			console.error('onAbort:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'warning');
		}
	});

	// -- Manuscript folio image upload button
	$('.ui.form .submit.folio.image').api({
		base: (parsedPath.includes('/htdocs') ? '/htdocs/' : '/'),
		method: 'POST',
		serializeForm: true,
		onResponse: function(response) {
			// make some adjustments to response
			// return response;
			console.group('Fomantic-UI API');
			console.log('Response:', response);
			console.groupEnd();
		},
		successTest: function(response) {
			// test whether a JSON response is valid
			console.group('Fomantic-UI API');
			console.log('successTest:', response.success || false);
			console.groupEnd();
			return response.success || false;
		},
		onComplete: function(response, element, xhr) {
			// always called after XHR complete
			console.group('Fomantic-UI API');
			console.log('onComplete:', response, element, xhr);
			console.groupEnd();

			if (response.success === true) {
				UI.createToast('Updating displayed image...');

				// Delete holder.js image
				$('.image.deleteable:visible').remove();
				
				// Store currently displayed image
				var oldImageSrc = $('.image.updateable:visible').attr('src');

				// Update displayed image
				$('.image.updateable:visible')
					.attr('src', 'data:image/jpeg;base64,' + response.fileContent)
					.css('margin', '0 auto')
					.css('width', '62%')
					.css('height', 'auto')
					.transition('fade in');

				// Store new displayed image
				var newImageSrc = $('.image.updateable:visible').attr('src');

				// Check displayed image source
				if (oldImageSrc !== newImageSrc) {
					UI.createToast('Displayed image updated.', 'success');
				}
				else {
					UI.createToast('Displayed image not updated.', 'warning');
				}
			}
		},
		onSuccess: function(response, element, xhr) {
			// valid response and response.success = true
			console.group('Fomantic-UI API');
			console.log('onSuccess:', response, element, xhr);
			console.groupEnd();

			UI.createToast(response.message, 'success');

			// Save update event
			UI.storeValue('image-updated', true);
		},
		onFailure: function(response, element, xhr) {
			// request failed, or valid response but response.success = false
			console.group('Fomantic-UI API');
			console.error('onFailure:', response, element, xhr);
			console.groupEnd();

			UI.createToast(response.message, 'error');
		},
		onError: function(errorMessage, element, xhr) {
			// invalid response
			console.group('Fomantic-UI API');
			console.error('onError:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'error');
		},
		onAbort: function(errorMessage, element, xhr) {
			// navigated to a new page, CORS issue, or user canceled request
			console.group('Fomantic-UI API');
			console.error('onAbort:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'warning');
		}
	});

	// -- Manuscript partner image update button
	$('.ui.form .submit.partner.image').api({
		base: (parsedPath.includes('/htdocs') ? '/htdocs/' : '/'),
		method: 'POST',
		serializeForm: true,
		onResponse: function(response) {
			// make some adjustments to response
			// return response;
			console.group('Fomantic-UI API');
			console.log('Response:', response);
			console.groupEnd();
		},
		successTest: function(response) {
			// test whether a JSON response is valid
			console.group('Fomantic-UI API');
			console.log('successTest:', response.success || false);
			console.groupEnd();
			return response.success || false;
		},
		onComplete: function(response, element, xhr) {
			// always called after XHR complete
			console.group('Fomantic-UI API');
			console.log('onComplete:', response, element, xhr);
			console.groupEnd();

			if (response.success === true) {
				UI.createToast('Updating displayed image...');

				// Delete holder.js image
				$('.image.deleteable:visible').remove();
				
				// Store currently displayed image
				var oldImageSrc = $('.image.updateable:visible').attr('src');

				// Update displayed image
				$('.image.updateable:visible')
					.attr('src', 'data:image/jpeg;base64,' + response.fileContent)
					.transition('fade in');

				// Store new displayed image
				var newImageSrc = $('.image.updateable:visible').attr('src');

				// Check displayed image source
				if (oldImageSrc !== newImageSrc) {
					UI.createToast('Displayed image updated.', 'success');
				}
				else {
					UI.createToast('Displayed image not updated.', 'warning');
				}
			}
		},
		onSuccess: function(response, element, xhr) {
			// valid response and response.success = true
			console.group('Fomantic-UI API');
			console.log('onSuccess:', response, element, xhr);
			console.groupEnd();

			UI.createToast(response.message, 'success');

			// Save update event
			UI.storeValue('partner-updated', true);
		},
		onFailure: function(response, element, xhr) {
			// request failed, or valid response but response.success = false
			console.group('Fomantic-UI API');
			console.error('onFailure:', response, element, xhr);
			console.groupEnd();

			UI.createToast(response.message, 'error');
		},
		onError: function(errorMessage, element, xhr) {
			// invalid response
			console.group('Fomantic-UI API');
			console.error('onError:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'error');
		},
		onAbort: function(errorMessage, element, xhr) {
			// navigated to a new page, CORS issue, or user canceled request
			console.group('Fomantic-UI API');
			console.error('onAbort:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'warning');
		}
	});

	// -- Nakala download button
	$('.ui.green.download.button').api({
		base: (parsedPath.includes('/htdocs') ? '/htdocs/' : '/'),
		onResponse: function(response) {
			// make some adjustments to response
			// return response;
			console.group('Fomantic-UI API');
			console.log('Response:', response);
			console.groupEnd();
		},
		successTest: function(response) {
			// test whether a JSON response is valid
			console.group('Fomantic-UI API');
			console.log('successTest:', response.success || false);
			console.groupEnd();
			return response.success || false;
		},
		onComplete: function(response, element, xhr) {
			// always called after XHR complete
			console.group('Fomantic-UI API');
			console.log('onComplete:', response, element, xhr);
			console.groupEnd();

			if (response.success === true) {
				UI.createToast('Loading newly added manuscript...');
				window.location.href = response.baseHref + '/' + response.encodedId;
			}
		},
		onSuccess: function(response, element, xhr) {
			// valid response and response.success = true
			console.group('Fomantic-UI API');
			console.log('onSuccess:', response, element, xhr);
			console.groupEnd();

			UI.createToast(response.message, 'success');

			// Save add event
			// UI.storeValue('manuscript-added', true);
		},
		onFailure: function(response, element, xhr) {
			// request failed, or valid response but response.success = false
			console.group('Fomantic-UI API');
			console.error('onFailure:', response, element, xhr);
			console.groupEnd();

			UI.createToast(response.message, 'error');
		},
		onError: function(errorMessage, element, xhr) {
			// invalid response
			console.group('Fomantic-UI API');
			console.error('onError:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'error');
		},
		onAbort: function(errorMessage, element, xhr) {
			// navigated to a new page, CORS issue, or user canceled request
			console.group('Fomantic-UI API');
			console.error('onAbort:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'warning');
		}
	});

	// -- Manuscript delete button
	/* $('.ui.ok.delete.manuscript').api({
		base: (parsedPath.includes('/htdocs') ? '/htdocs/' : '/'),
		onResponse: function(response) {
			// make some adjustments to response
			// return response;
			console.group('Fomantic-UI API');
			console.log('Response:', response);

			// Remove passed data
			jQuery.removeData($('.ui.ok.delete.manuscript'), 'id');
			console.groupEnd();
		},
		successTest: function(response) {
			// test whether a JSON response is valid
			console.group('Fomantic-UI API');
			console.log('successTest:', response.success || false);
			console.groupEnd();
			return response.success || false;
		},
		onComplete: function(response, element, xhr) {
			// always called after XHR complete
			console.group('Fomantic-UI API');
			console.log('onComplete:', response, element, xhr);

			// Remove passed data
			jQuery.removeData($('.ui.ok.delete.manuscript'), 'id');
			console.groupEnd();
		},
		onSuccess: function(response, element, xhr) {
			// valid response and response.success = true
			console.group('Fomantic-UI API');
			console.log('onSuccess:', response, element, xhr);

			// Remove passed data
			jQuery.removeData($('.ui.ok.delete.manuscript'), 'id');
			console.groupEnd();
		},
		onFailure: function(response, element, xhr) {
			// request failed, or valid response but response.success = false
			console.group('Fomantic-UI API');
			console.error('onFailure:', response, element, xhr);

			// Remove passed data
			jQuery.removeData($('.ui.ok.delete.manuscript'), 'id');
			console.groupEnd();
		},
		onError: function(errorMessage, element, xhr) {
			// invalid response
			console.group('Fomantic-UI API');
			console.error('onError:', errorMessage, element, xhr);

			// Remove passed data
			jQuery.removeData($('.ui.ok.delete.manuscript'), 'id');
			console.groupEnd();
		},
		onAbort: function(errorMessage, element, xhr) {
			// navigated to a new page, CORS issue, or user canceled request
			console.group('Fomantic-UI API');
			console.error('onAbort:', errorMessage, element, xhr);

			// Remove passed data
			jQuery.removeData($('.ui.ok.delete.manuscript'), 'id');
			console.groupEnd();
		}
	}); */
});
