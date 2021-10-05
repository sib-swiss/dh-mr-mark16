/**
* Main client side code
* Jonathan Barda / SIB - 2020
*/
"use strict";

/* Create main object */
var mr = window.mr || {};

/* App config */
mr.lang = 'en';
mr.state = {
    collection: null,
    manifest: null,
    mirador: {
        loaded: false
    },
};
mr.api = {
    iiif: {
        version: '2-1'
    },
    text: {
        version: '2-1'
    }
};

/* IFFE */
(function () {
    "use strict";

    console.group('Loading');
    console.log('Loading app.js');
    console.groupEnd();

    /* Fix '$' not defined when mirador jquery-migrate is loaded */
    window.jQ = window.jQ || (typeof $.noConflict !== 'undefined' ? $.noConflict(true) : $);

    /* App methods */
    mr.setCollection = function(data) {
        if (data) {
            mr.state.collection = data;
        }
    }
    mr.setManifest = function(data) {
        if (data) {
            mr.state.manifest = data;
        }
    }

    // Functions 'limit', 'debounce' and 'throttle' are comming from:
    // https://github.com/jashkenas/underscore/commit/9e3e067f5025dbe5e93ed784f93b233882ca0ffe#diff-0f36b362a0b81d6f4d4bfd8a7413c75d

    // Internal function used to implement `_.throttle` and `_.debounce`.
    mr.limit = function(func, wait, debounce) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var throttler = function() {
                timeout = null;
                func.apply(context, args);
            };
            if (debounce) clearTimeout(timeout);
            if (debounce || !timeout) timeout = setTimeout(throttler, wait);
        };
    };

    // Returns a function, that, when invoked, will only be triggered at most once
    // during a given window of time.
    mr.throttle = function(func, wait) {
        return mr.limit(func, wait, false);
    };

    // Returns a function, that, as long as it continues to be invoked, will not
    // be triggered. The function will be called after it stops being called for
    // N milliseconds.
    mr.debounce = function(func, wait) {
        return mr.limit(func, wait, true);
    };

    mr.getBaseURL = function() {
        // Parse current URL
        var parsedURL = new URL(window.location.href);

        // Rewrite parsed URL
        var baseURL  = parsedURL.protocol + '//';
            baseURL += parsedURL.host + (parsedURL.hostname === 'localhost' ? '/htdocs/' : '/');

        console.group('URL Parser');
        console.log('Parsed URL:', parsedURL);
        console.log('Base URL:', baseURL);
        console.groupEnd();

        return baseURL;
    }

    /**
     * get URL for view document.
     * ex: /htdocs/view
     */
    mr.getViewUrl = function() {
        let url= mr.getBaseURL() + 'view';
        return url;
    };

    /**
     * get URL for IIIF API
     * ex. /htdocs/api/iiif/2
     */
    mr.getApiUrl = function() {
        let apiURLv2 = mr.getBaseURL() + 'api/iiif/'
            // + (typeof apiUrlVersion !== 'undefined' && apiUrlVersion ? apiUrlVersion:'2');
            + (typeof apiUrlVersion !== 'undefined' && apiUrlVersion ? apiUrlVersion : mr.api.iiif.version);
        return apiURLv2;
    };

    mr.getFolio = function(manuscriptID, manuscriptFolioID, manuscriptFolioLang) {
        // Show loader
        jQ('#small-iframe-loading-status').removeClass('hide');

        // Get iframe
        // var iframe_element = document.getElementsByTagName('iframe')[0];
        var $iframe = jQ('.small-iframe');

        // Remove current content
        $iframe.removeAttr('src');

        // Console output
        console.group('App - TextLoader');

        // Get current manuscript ID
        // var manuscriptID = parsedURL.searchParams.get('id');
        var decodedManuscriptID = String(atob(manuscriptID)).toUpperCase();
        console.log('Manuscript ID:', manuscriptID);
        console.log('Decoded manuscript:', decodedManuscriptID);

        // Get manuscript folio ID
        if (typeof manuscriptFolioID !== 'undefined') {
            // var manuscriptFolioID = parsedURL.searchParams.get('folio');
            var decodedFolioID = atob(manuscriptFolioID);
            console.log('Manuscript folio ID:', manuscriptFolioID);
            console.log('Decoded manuscript folio:', decodedFolioID);
        }

        var apiURLv1 = mr.getBaseURL() + 'api/mr/v' 
            // + (typeof apiUrlVersion !== 'undefined' && apiUrlVersion ? apiUrlVersion : '1')
            + (typeof apiUrlVersion !== 'undefined' && apiUrlVersion ? apiUrlVersion : mr.api.text.version)
            + '?id=' + manuscriptID;
        console.log('API URL v1:', apiURLv1);

        // End console group
        console.groupEnd();

        // Get related manuscript JSON data
        jQ.getJSON(apiURLv1)
            .done(function (data) {
                console.group('API - Text');
                console.log('Returned manuscript:', data);
                
                // Parsing returned folios
                var totalFolios = data.folios.length;

                console.log('Total folios:', totalFolios);
                
                if (typeof manuscriptFolioID !== 'undefined') {
                    console.info('Lookup for defined folio...', decodedFolioID, data.folios);

                    jQ.each(data.folios, function (index, folio) {
                        if (folio === decodedFolioID) {
                            console.info('Taking selected folio:');
                            console.info(' - Folio index: ', index);
                            console.info(' - Folio name: ', folio);

                            // Build folio URL
                            var folioURL = mr.getViewUrl() + '?id=' + manuscriptID + '&folio=' + btoa(folio) + '&alter=true';
                            console.log('Encoded folio ID:', btoa(folio));
                            console.log('Folio URL:', folioURL);

                            // Set active tab
                            jQ('.tabs .tab').removeClass('active');
                            if (folio.includes('_ENG')) {
                                jQ('.tabs .tab.english').addClass('active');
                            }
                            else if (folio.includes('_FRA')) {
                                jQ('.tabs .tab.french').addClass('active');
                            }
                            else if (folio.includes('_GER')) {
                                jQ('.tabs .tab.german').addClass('active');
                            }
                            else {
                                jQ('.tabs .tab.diplomatic').addClass('active');
                            }
    
                            // Now display corresponding folio
                            console.info('Displaying folio...');
                            // iframe_element.src = folioURL;
                            $iframe.attr('src', folioURL);
                        }
                    });
                }
                else {
                    console.info('Lookup for default folio...');
                    if (typeof manuscriptFolioLang !== 'undefined') {
                        console.info('Should isolate language:', manuscriptFolioLang);
                    }

                    // Set active tab
                    // jQ('.tabs .tab').removeClass('active');
                    // jQ('.tabs .tab.diplomatic').addClass('active');

                    jQ.each(data.folios, function (index, folio) {
                        var $tabs = jQ('.tabs .tab');

                        if (!folio.includes('_ENG') && !folio.includes('_FRA')) {
                            console.info('Taking first folio found:');
                            console.info(' - Folio index: ', index);
                            console.info(' - Folio name: ', folio);

                            // Build folio URL
                            var folioURL = mr.getViewUrl() +'?id=' + manuscriptID + '&folio=' + btoa(folio) + '&alter=true';
                            console.log('Encoded folio ID:', btoa(folio));
                            console.log('Folio URL:', folioURL);

                            // Set active tab
                            // jQ('.tabs .tab').removeClass('active');
                            $tabs.removeClass('active');
                            jQ('.tabs .tab.diplomatic').addClass('active');

                            // Now display first folio
                            console.info('Displaying folio...');
                            // iframe_element.src = folioURL;
                            $iframe.attr('src', folioURL);

                            // Leave the loop
                            return false;
                        }
                        else if (typeof manuscriptFolioLang !== 'undefined' && folio.includes(manuscriptFolioLang)) {
                            console.info('Taking related folio found:');
                            console.info(' - Folio index: ', index);
                            console.info(' - Folio name: ', folio);

                            // Parse folio name
                            // var parsedFolioName = String(folio).split('.');
                            // var folioExtension = parsedFolioName[(parsedFolioName.length-1)];
                            // var newFolioName = String(folio).replace('.' + folioExtension, '_' + manuscriptFolioLang + '.' + folioExtension);
                            var newFolioName = folio;
                            console.info(' - Composed folio name: ', newFolioName);

                            // Build folio URL
                            var folioURL = mr.getViewUrl() + '?id=' + manuscriptID + '&folio=' + btoa(newFolioName) + '&alter=true';
                            console.log('Encoded folio ID:', btoa(newFolioName));
                            console.log('Folio URL:', folioURL);

                            // Set active tab
                            // jQ('.tabs .tab').removeClass('active');
                            $tabs.removeClass('active');
                            if (folio.includes('_ENG')) {
                                jQ('.tabs .tab.english').addClass('active');
                            }
                            else if (folio.includes('_FRA')) {
                                jQ('.tabs .tab.french').addClass('active');
                            }
                            else {
                                jQ('.tabs .tab.diplomatic').addClass('active');
                            }

                            // Now display first folio
                            console.info('Displaying folio...');
                            // iframe_element.src = folioURL;
                            $iframe.attr('src', folioURL);

                            // Leave the loop
                            return false;
                        }
                    });
                }

                // Hide loading status
                $iframe.one('load', function(event) {
                    console.group('iframe');
                    console.log('iframe loaded.', event);
                    jQ('#small-iframe-loading-status').addClass('hide');
                    console.groupEnd();
                });

                console.groupEnd();
            })
            .fail(function (jqxhr, textStatus, error) {
                console.group('API');
                console.error('[' + textStatus + '],', error, jqxhr);
                console.groupEnd();
            });
    }
    mr.getObject = function() {
        console.group('App');
        console.log('Main object:', mr);
        console.groupEnd();

        return mr;
    }
    mr.getState = function() {
        // console.group('App');
        console.log('Current state:', mr.state);
        // console.groupEnd();

        return mr.state;
    }
    mr.loadViewer = function () {
        // See config options from here:
        // https://github.com/ProjectMirador/mirador-2-wiki/wiki
        // https://github.com/ProjectMirador/mirador-2-wiki/wiki/Configuration-Guides
        // https://github.com/ProjectMirador/mirador-2-wiki/wiki/Complete-Configuration-API
        // https://github.com/ProjectMirador/mirador-2-wiki/wiki/Mirador-Initialization
        // https://github.com/ProjectMirador/mirador-2-wiki/wiki/Events-in-Mirador

        // Create Mirador instance
        var mirador = Mirador({
            id: "mirador",
            buildPath: "resources/js/mirador-v2.7.0/",
            data: [{
                // manifestUri: mr.state.manifest['@id'],
                // manifest: mr.state.manifest,
                collectionContent: mr.state.collection,
                location: "SIB DH+"
            }],
            /* manifestsPanel: { // Open collection tree browser on load
                name: "Collection tree browser",
                module: "CollectionTreeManifestsPanel"
            }, */
            "openManifestsPage": false, // Open manifest selector on load
            "mainMenuSettings": {
                'show': true
            },
            "showAddFromURLBox": false,
            windowObjects: [{
                "loadedManifest": mr.state.manifest['@id'],
                // "canvasID": mr.state.manifest.sequences[0].canvases[0]['@id'],
                "viewType": "ImageView",
                "annotationLayer": false,
                "bottomPanel": true,
                "bottomPanelVisible": true,
                "sidePanel": false,
                "sidePanelVisible": false,
                "annotationLayer": false,
                "displayLayout": false
            }],

            // See here for more details:
            // https://github.com/ProjectMirador/mirador/blob/v2.7.0/js/src/settings.js
            windowSettings: {
                "annotationLayer": false,
                "bottomPanel": true,
                "bottomPanelVisible": true,
                "sidePanel": false,
                "sidePanelVisible": false,
                "annotationLayer": false,
                "displayLayout": false
            }
        });

        if (mirador) {
            console.group('Mirador');
            console.log('Mirador IIIF Viewer loaded.', mirador);
            console.info('Subscribe to events.');

            // Subscribe to 'windowAdded' event
            // It will be fired by Mirador when the window is created
            // Once fired, a 'click' event will be attached to each generated thumbnails
            // The 'click' event will then change displayed folio according to the one displayed on Mirador
            mirador.eventEmitter.subscribe('windowAdded', function (event, windowAdded) {
                console.group('Mirador - Window Event');
                console.log('Mirador [windowAdded] event.', event, windowAdded);

                // Avoid excessive processing by filtering on hardcoded Mirador slotAddress
                // Without this filtering, it will create an infinite loop on multiple manuscripts selection display
                if (typeof windowAdded.slotAddress !== 'undefined' && windowAdded.slotAddress === 'row1') {

                    console.info('Looking for thumbnails...');

                    if (jQ('img.thumbnail-image').length > 0) {
                        console.info('Thumbnails found! Attaching click event.');

                        // Debouncing 'click' event to avoid excessive calls from Mirador 'windowUpdated' / 'windowAdded' events
                        // Doing so will fire the event only once instead of several times

                        // jQ('img.thumbnail-image').on('click', function (event) {
                        jQ('img.thumbnail-image').on('click', mr.debounce(function (event) {
                            var canvasID = jQ(this).attr('data-image-id');
                            var canvasTitle = jQ(this).attr('title').replace(' ', '_');
                            var manuscriptID = canvasTitle.split('_')[0];
                            var folioID = canvasTitle.split('_')[1];
                            var encodedManuscriptID = btoa(manuscriptID);

                            console.group('Mirador - Thumbnail Event (debounced)');
                            console.log('Mirador click event received:', event);
                            console.log('Canvas ID:', canvasID);
                            console.log('Canvas Title:', canvasTitle);
                            console.log('Manuscript ID:', manuscriptID);
                            console.log('Selected folio:', folioID);
                            console.log('Encoded Manuscript ID:', encodedManuscriptID);

                            // Detect active language tab
                            console.info('Detecting active language tabs...');
                            if (jQ('.tabs .tab.active').length > 0) {
                                console.log('Found active language tab:', jQ('.tabs .tab.active'));
                                console.log(' - Classes:', jQ('.tabs .tab.active').attr('class'));

                                // Get language from active tab and set iframe URL
                                var currentTabLang;
                                if (jQ('.tabs .tab.active').hasClass('english')) {
                                    currentTabLang = 'ENG';
                                    console.info('Current tab language:', currentTabLang);

                                    var newFolio = manuscriptID + '_' + folioID + '_' + currentTabLang + '.html';

                                    if (typeof folioID !== 'undefined') {
                                        console.info('Loading folio [' + manuscriptID + ' / ' + newFolio + ']...', btoa(manuscriptID), btoa(newFolio));
                                        mr.getFolio(btoa(manuscriptID), btoa(newFolio));
                                    }
                                    else {
                                        console.info('Loading default folio for [' + manuscriptID + ']...', btoa(manuscriptID), undefined, currentTabLang);
                                        mr.getFolio(btoa(manuscriptID), undefined, currentTabLang);
                                    }
                                }
                                else if (jQ('.tabs .tab.active').hasClass('french')) {
                                    currentTabLang = 'FRA';
                                    console.info('Current tab language:', currentTabLang);

                                    var newFolio = manuscriptID + '_' + folioID + '_' + currentTabLang + '.html';

                                    if (typeof folioID !== 'undefined') {
                                        console.info('Loading folio [' + manuscriptID + ' / ' + newFolio + ']...', btoa(manuscriptID), btoa(newFolio));
                                        mr.getFolio(btoa(manuscriptID), btoa(newFolio));
                                    }
                                    else {
                                        console.info('Loading default folio for [' + manuscriptID + ']...', btoa(manuscriptID), undefined, currentTabLang);
                                        mr.getFolio(btoa(manuscriptID), undefined, currentTabLang);
                                    }
                                }
                                else {
                                    currentTabLang = 'DIPLO';
                                    console.info('Current tab language:', currentTabLang);

                                    var newFolio = manuscriptID + '_' + folioID + '.html';

                                    if (typeof folioID !== 'undefined') {
                                        console.info('Loading folio [' + manuscriptID + ']...', btoa(manuscriptID), btoa(newFolio));
                                        mr.getFolio(btoa(manuscriptID), btoa(newFolio));
                                    }
                                    else {
                                        console.info('Loading folio [' + manuscriptID + ']...', btoa(manuscriptID), undefined);
                                        mr.getFolio(btoa(manuscriptID), undefined);
                                    }
                                }

                                // Set tab language
                                jQ('.tabs .tab').each(function (index, tab) {
                                    console.log('Resetting tab index:', index);
                                    if (jQ(tab).hasClass('english')) {
                                        // Reset tab action
                                        var newEncodedFolioID = btoa(manuscriptID + '_' + folioID + '_ENG.html');
                                        if (typeof folioID !== 'undefined') {
                                            jQ('.tabs .tab.english').attr('onclick', "mr.getFolio('" + btoa(manuscriptID) + "', '" + newEncodedFolioID + "');");
                                        }
                                        else {
                                            jQ('.tabs .tab.english').attr('onclick', "mr.getFolio('" + btoa(manuscriptID) + "', undefined, 'ENG');");
                                        }
                                    }
                                    else if (jQ(tab).hasClass('french')) {
                                        // Reset tab action
                                        var newEncodedFolioID = btoa(manuscriptID + '_' + folioID + '_FRA.html');
                                        if (typeof folioID !== 'undefined') {
                                            jQ('.tabs .tab.french').attr('onclick', "mr.getFolio('" + btoa(manuscriptID) + "', '" + newEncodedFolioID + "');");
                                        }
                                        else {
                                            jQ('.tabs .tab.french').attr('onclick', "mr.getFolio('" + btoa(manuscriptID) + "', undefined, 'FRA');");
                                        }
                                    }
                                    else if (jQ(tab).hasClass('german')) {
                                        // Reset tab action
                                        var newEncodedFolioID = btoa(manuscriptID + '_' + folioID + '_GER.html');
                                        if (typeof folioID !== 'undefined') {
                                            jQ('.tabs .tab.german').attr('onclick', "mr.getFolio('" + btoa(manuscriptID) + "', '" + newEncodedFolioID + "');");
                                        }
                                        else {
                                            jQ('.tabs .tab.german').attr('onclick', "mr.getFolio('" + btoa(manuscriptID) + "', undefined, 'GER');");
                                        }
                                    }
                                    else {
                                        // Reset tab action
                                        var newEncodedFolioID = btoa(manuscriptID + '_' + folioID + '.html');
                                        if (typeof folioID !== 'undefined') {
                                            jQ('.tabs .tab.diplomatic').attr('onclick', "mr.getFolio('" + btoa(manuscriptID) + "', '" + newEncodedFolioID + "');");
                                        }
                                        else {
                                            jQ('.tabs .tab.diplomatic').attr('onclick', "mr.getFolio('" + btoa(manuscriptID) + "');");
                                        }
                                    }
                                });
                            }

                            console.groupEnd(); 
                        // });
                        }, 100));
                    }
                }

                console.groupEnd();
            });

            // Suscribe to 'windowUpdated' event
            // This will be fired when current Mirador window is updated and when navigation arrows are clicked
            // Using this event is quite tricky as it is fired several times
            mirador.eventEmitter.subscribe('windowUpdated', function (event, windowUpdated) {
                console.group('Mirador - Window Event');
                console.log('Mirador [windowUpdated] event.', event, windowUpdated);

                // Avoid excessive processing by filtering on hardcoded Mirador slotAddress
                // Without this filtering, it will create an infinite loop on multiple manuscripts selection display
                if (typeof windowUpdated.slotAddress !== 'undefined' && windowUpdated.slotAddress === 'row1') {

                    console.info('Thumbnails should be loaded.');

                    if (jQ('img.thumbnail-image').length > 0) {
                        console.info('Thumbnails found! Looking for property change...', jQ('img.thumbnail-image'));

                        jQ('img.thumbnail-image').each(function (index, thumbnail) {
                            if (jQ(thumbnail).hasClass('highlight')) {
                                var canvasID = jQ(thumbnail).attr('data-image-id');
                                var canvasTitle = jQ(thumbnail).attr('title').replace(' ', '_');
                                var manuscriptID = canvasTitle.split('_')[0];
                                var folioID = canvasTitle.split('_')[1];
                                var encodedManuscriptID = btoa(manuscriptID);

                                console.group('Mirador - Navigation Event');
                                console.log('Thumbnail changed.', index, jQ(thumbnail));
                                console.log('Thumbnail properties:');
                                console.log(' - Canvas ID:', canvasID);
                                console.log(' - Canvas Title:', canvasTitle);
                                console.log(' - Manuscript ID:', manuscriptID);
                                console.log(' - Selected folio:', folioID);
                                console.log(' - Encoded Manuscript ID:', encodedManuscriptID);
    
                                // Parse current URL
                                var parsedURL = new URL(window.location.href);
                                console.log(' - Parsed URL:', parsedURL);
                                
                                // Trigger 'click' on corresponding thumbnail
                                // The 'click' event will be debounced to avoid excessive firing from Mirador
                                jQ(thumbnail).trigger('click');
    
                                console.groupEnd();
                            }
                        });
                    }

                }
                
                console.groupEnd();

                /* else {
                    // Close console group to avoid infinite incrementation
                    console.groupEnd();

                    // Leaving if given 'row' is not related to the hardcoded one
                    return false;
                } */
            });

            console.groupEnd();
        }
    }

    /* Init when DOM is ready */
    jQ(function () {
        console.group('App');
        console.log('Initial state:', mr);

        // Parse current URL
        var parsedURL = new URL(window.location.href);
        console.log('Parsed URL:', parsedURL);

        /* Init wait counter to load Mirador */
        if (String(parsedURL.pathname).includes('show')) {
            console.info('Waiting for required resources to be loaded...');
            var waiting = setInterval(function () {
                if (mr.state.collection !== null && mr.state.manifest !== null) {
                    console.group('App - Timer');
                    console.info('Resources loaded, loading viewer...');
                    console.groupEnd();
    
                    // Call Mirador
                    mr.loadViewer();
    
                    // Stop waiting loop
                    clearInterval(waiting);
                }
            }, 200);
        }

        console.groupEnd();
    });
})()