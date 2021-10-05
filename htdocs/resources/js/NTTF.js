/*  This script was extracted from NTTF HTML files */
(function () {
var myJQ = $.noConflict(true);
// not used in mr
// var myTranscription = '#34a7ee19-7375-45a9-b581-985aa5f1bcab';

var displayOptions = {
	hyphen : true
};

window.addEventListener("message", function(event) {
	displayOptions = event.data;
	updateDisplayFromOptions();
}, false);


function updateDisplayFromOptions() {
	myJQ('body').find('span.nobreak').attr('data-after', displayOptions.hyphen?'-':'');
}


myJQ(document).ready(function loaded() {
    console.log('loaded()');
	try {
		if (parent && parent.displayOptions) {
			// see if parent has a dispayOptions object for us
			displayOptions = parent.displayOptions;
			// send events which might be interesting up to parent
			myJQ(document).mousemove(function(event) {
				window.postMessage({
					type : event.type,
					pageX : event.pageX,
					pageY : event.pageY,
					offsetX : event.offsetX,
					offsetY : event.offsetY,
				}, '*');
				parent.postMessage({
					type : event.type,
					pageX : event.pageX,
					pageY : event.pageY,
					offsetX : event.offsetX,
					offsetY : event.offsetY,
				}, '*');
			});
		}
	}
	catch(err) {}
	
	// This line was modified to fit in mr 
	myJQ('body').on('mouseenter', '.verse_number', function () {
        console.log('.verse_number hover in', event);
		myJQ(this).prev('.verse_marker').addClass('showVerseMark');
	}).on('mouseleave', '.verse_number', function () {
        console.log('.verse_number hover out');
		myJQ(this).prev('.verse_marker').removeClass('showVerseMark');
	});

    // INTF KO
	//myJQ(myTranscription).find('.seg-other-marker').add('.note-marker').add('.marginalia-marker').add('span.app').hover(function() {
	
	// This line was modified to fit in mr 
	myJQ('body').on('mouseenter', '.seg-other-marker, .note-marker, .marginalia-marker, span.app', function() {
		var m = myJQ('body').find('#'+myJQ(this).attr('data-id'));
		var pos = myJQ(this).position();
		var title = myJQ(this).attr('title');
		if (title && title.length) title += '; ';
		var subtype = myJQ(this).attr('data-subtype');
		if (subtype && subtype.length) {
			if (title.length) title += '; ';
			title += decodeURIComponent(subtype);
		}
		m.find('[title]').each(function() {
			title += '; ' + myJQ(this).attr('title');
		})
/*
		m.css('top', (pos.top + $(this).outerHeight()+7) + 'px')
			.css('left', (pos.left -7) + "px")
*/
		m.css('top', (pos.top - $(m).outerHeight()-7) + 'px')
			.css('left', (pos.left -7) + "px")
			.show();
		myJQ(this).attr('title', title);
	}).on('mouseleave', '.seg-other-marker, .note-marker, .marginalia-marker, span.app', function() {
		myJQ('body').find('#'+myJQ(this).attr('data-id')).hide();
		myJQ(this).attr('title', '');
	});
	
	var destination = myJQ('body').find('.folioBreak').first();
	myJQ('body').find('.seg-margin-pagetop').each(function() {
		$(destination).after($(this));
	});

	updateDisplayFromOptions();
});

})();