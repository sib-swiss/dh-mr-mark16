"use strict";

// api code - Boot stuff when DOM is loaded
$(function () {
	// Client API Routes / Server Routes
	$.fn.api.settings.api = {
		'nakala download' : 'admin/download/{id}',
		'nakala sync' : 'admin/sync/{id}',
		'manuscript delete' : 'admin/delete/{id}',
		'manuscript image upload' : 'admin/edit/{id}',
		'manuscript update status' : 'admin/update/{id}',
		'partner add' : 'admin/edit/{id}',
		'partner image upload' : 'admin/edit/{id}',
		'clear cache' : 'admin/cache/clear'
	};

	// Debug Client API Routes
	console.group('Fomantic-UI API');
	console.log('Config:', (typeof $.fn.api.settings !== undefined ? $.fn.api.settings : null));
	console.log('Location:', UI.getCurrentLocation());
	console.groupEnd();

	// Client API Actions
	var parsedPath = UI.getCurrentLocation().pathname;

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
					.attr('src', 'data:image/png;base64,' + response.fileContent)
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

	// -- Manuscript partner add button
	$('.ui.form .submit.partner.details').api({
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
				/* // Delayed page reload
				var displayDelay = 1000;
				var showReloadTimeout = setTimeout(function () {
					// Dirty page reload...
					// TODO: improve tabs caching management
					clearTimeout(showReloadTimeout);
					if (UI.getValue('partner-uploaded') === 'true') {
						UI.createToast('Reloading page...');
						window.location.reload();
					}
				}, displayDelay); */

				UI.createToast('Updating displayed image...');

				// Delete holder.js image
				$('.image.deleteable:visible').remove();
				
				// Store currently displayed image
				var oldImageSrc = $('.image.updateable:visible').attr('src');

				// Update displayed image
				$('.image.updateable:visible')
					.attr('src', 'data:image/png;base64,' + response.fileContent)
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
			UI.storeValue('partner-added', true);
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
	$('.ui.download.manuscript.button').api({
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

	// -- Nakala sync link icon
	$('.tooltipped.sync.link i').api({
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
				UI.createToast('Updated manuscript <strong>' + atob(response.encodedId) + '</strong> to revision <strong>' + response.revision + '</strong>.');
				$(this).attr('data-revision', response.revision);
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

	// -- Manuscript status update links
	$('.styled.update.link').api({
		base: (parsedPath.includes('/htdocs') ? '/htdocs/' : '/'),
		method: 'POST',
		// Allows modifying settings before request, or cancelling request
		beforeSend: function(settings) {
			// cancel request if no id
			if(!$(this).data('id')) {
				return false;
			}
			settings.data.manuscript_id = $(this).data('id');
			settings.data.manuscript_published = ($(this).attr('data-published') === 'true' ? '0' : '1');
			return settings;
		},
		// Callback that occurs when request is made. Receives both the API success promise and the XHR request promise.
		onRequest: function(promise, xhr) {
			console.group('Fomantic-UI API');
			console.log('Request:', promise, xhr);
			console.groupEnd();

			if ($(this).api('is loading') === true) {
				$(this).text('Updating status...');
			}
		},
		// Allows modifying the server's response before parsed by other callbacks to determine API event success
		onResponse: function(response) {
			// make some adjustments to response
			// return response;
			console.group('Fomantic-UI API');
			console.log('Response:', response);
			console.groupEnd();
		},
		// Determines whether completed JSON response should be treated as successful
		successTest: function(response) {
			// test whether a JSON response is valid
			console.group('Fomantic-UI API');
			console.log('successTest:', response.success || false);
			console.groupEnd();
			return response.success || false;
		},
		// Callback after successful response, JSON response must pass successTest
		onSuccess: function(response, element, xhr) {
			// valid response and response.success = true
			console.group('Fomantic-UI API');
			console.log('onSuccess:', response, element, xhr);
			console.log('$(this):', $(this));
			console.groupEnd();

			UI.createToast(response.message, 'success');

			// Save update event
			// UI.storeValue('manuscript-updated', true);
		},
		// Callback on request complete regardless of conditions
		onComplete: function(response, element, xhr) {
			// always called after XHR complete
			console.group('Fomantic-UI API');
			console.log('onComplete:', response, element, xhr);
			console.log('$(this):', $(this));
			console.groupEnd();

			$(this).text('Status updated!');

			if (response.success === true) {
				var $this = $(this);

				$this.attr('data-published', response.published);

				if (response.published === true) {
					UI.createToast('Manuscript published');

					var delayedStatusUpdate = setTimeout(function () {
						$this
							.text('Published')
							.attr('data-status', 'Published');

						clearTimeout(delayedStatusUpdate);
					}, 500);
				}
				else {
					UI.createToast('Manuscript unpublished', 'warning');

					var delayedStatusUpdate = setTimeout(function () {
						$this
							.text('Not Published')
							.attr('data-status', 'Not Published');

						clearTimeout(delayedStatusUpdate);
					}, 500);
				}
			}
		},
		// Callback on failed response, or JSON response that fails successTest
		onFailure: function(response, element, xhr) {
			// request failed, or valid response but response.success = false
			console.group('Fomantic-UI API');
			console.error('onFailure:', response, element, xhr);
			console.groupEnd();

			UI.createToast(response.message, 'error');
		},
		// Callback on server error from returned status code, or XHR failure.
		onError: function(errorMessage, element, xhr) {
			// invalid response
			console.group('Fomantic-UI API');
			console.error('onError:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'error');
		},
		// Callback on abort caused by user clicking a link or manually cancelling request.
		onAbort: function(errorMessage, element, xhr) {
			// navigated to a new page, CORS issue, or user canceled request
			console.group('Fomantic-UI API');
			console.error('onAbort:', errorMessage, element, xhr);
			console.groupEnd();

			UI.createToast(errorMessage, 'warning');
		}
	});
	/* .state({
		onActivate: function() {
			if ($(this).api('was complete') === true) {
				$(this).state('flash text');
			}
		},
		text: {
			flash      : 'Status updated!'
		}
	}); */
	$('.styled.update.link').on('mouseenter', function (event) {
		console.group('Fomantic-UI - Custom Event');
		console.log('Received event:', event);
		console.groupEnd();

		if ($(this).api('is loading') !== true) {
			$(this).text('Change status');
		}
	});
	$('.styled.update.link').on('mouseleave', function (event) {
		console.group('Fomantic-UI - Custom Event');
		console.log('Received event:', event);
		console.groupEnd();

		if ($(this).api('is loading') !== true) {
			$(this).text($(this).attr('data-status'));
		}
	});

	// -- Clear cache button
	$('.item.cache-action').api({
		base: (parsedPath.includes('/htdocs') ? '/htdocs/' : '/'),
		stateContext: '.item.cache-action i.eraser.icon',
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
				// Nothing to do visually
			}
		},
		onSuccess: function(response, element, xhr) {
			// valid response and response.success = true
			console.group('Fomantic-UI API');
			console.log('onSuccess:', response, element, xhr);
			console.groupEnd();

			// UI.createToast(response.message, 'success');
			UI.createToast(String(response.message).replace(/(\r\n|\r|\n)/gm, '<br>'), 'success');
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
});
