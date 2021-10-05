/* Init when DOM is ready */
$(function () {
    /* Tooltips */
    $('[data-toggle="tooltip"]').tooltip();

    /* Modals */
    $('[data-toggle="modal"]').modal({
    // $('#abstract-content').modal({
        keyboard: false,
        show: false
    });

    // ********************
    // iiif viewer
    // ********************
    var manifest_link = $("#manifest").text();
    var collection_link = "data/iiif-collection.json";

    $(function () {
        mirador = Mirador({
            id: "mirador",
            buildPath: "resources/mirador/",
            data: [{ collectionUri: collection_link, location: "SIB DH+" }],
            "mainMenuSettings": {
                'show': true
            },
            "showAddFromURLBox": false,
            windowObjects: [{
                "loadedManifest": manifest_link,
                //   canvasID: "http://iiif/id/and/uri/of/your/canvas/canvas-id",
                "viewType": "ImageView",
                "annotationLayer": false,
                "sidePanel": false,
                "sidePanelVisible": false,
                "annotationLayer": false,
                "displayLayout": false
            }]
        });

        TextViewer('#tei-viewer', mirador.eventEmitter);
    });
});

/**
* EventEmitter v4.0.3 - git.io/ee
* Oliver Caldwell
* MIT license
*/

// ********************
// EventEmitter
// ********************
(function (e) { "use strict"; function t() { } function i(e, t) { if (r) return t.indexOf(e); var n = t.length; while (n--) if (t[n] === e) return n; return -1 } var n = t.prototype, r = Array.prototype.indexOf ? !0 : !1; n.getListeners = function (e) { var t = this._events || (this._events = {}); return t[e] || (t[e] = []) }, n.addListener = function (e, t) { var n = this.getListeners(e); return i(t, n) === -1 && n.push(t), this }, n.on = n.addListener, n.removeListener = function (e, t) { var n = this.getListeners(e), r = i(t, n); return r !== -1 && (n.splice(r, 1), n.length === 0 && (this._events[e] = null)), this }, n.off = n.removeListener, n.addListeners = function (e, t) { return this.manipulateListeners(!1, e, t) }, n.removeListeners = function (e, t) { return this.manipulateListeners(!0, e, t) }, n.manipulateListeners = function (e, t, n) { var r, i, s = e ? this.removeListener : this.addListener, o = e ? this.removeListeners : this.addListeners; if (typeof t == "object") for (r in t) t.hasOwnProperty(r) && (i = t[r]) && (typeof i == "function" ? s.call(this, r, i) : o.call(this, r, i)); else { r = n.length; while (r--) s.call(this, t, n[r]) } return this }, n.removeEvent = function (e) { return e ? this._events[e] = null : this._events = null, this }, n.emitEvent = function (e, t) { var n = this.getListeners(e), r = n.length, i; while (r--) i = t ? n[r].apply(null, t) : n[r](), i === !0 && this.removeListener(e, n[r]); return this }, n.trigger = n.emitEvent, typeof define == "function" && define.amd ? define(function () { return t }) : e.EventEmitter = t })(this);

// ********************
// TextViewer
// Handles the update of the html upon image events
// Author J.B. Dugied, credits to J.J. van Zundert
// ********************
function TextViewer(element_id, mirador_event_emitter) {
    // This protects all other code to whatever we are doing.
    // 'Private' methods should be properties of this variable.    
    var _text_viewer = {};

    // This protects any elements outside out context to whatever we are doing.
    // All bindings from this object should be to an element descendant from _this.
    var _this = $(element_id);

    // This is a local event emitter to synchronise GUI stuff of the text viewer only.
    var _event_emitter = new EventEmitter();
    var folio = 1;

    // Listener to : Highlight clicked tab and load text
    _event_emitter.addListener('tab_clicked', function (tab) {
        var other_tabs = _this.find('.text_viewer .tabs .tab').toArray().filter(function (any_tab) {
            return !(any_tab === tab);
        })
        $(other_tabs).removeClass('active');
        if (!$(tab).hasClass('active')) { $(tab).addClass('active') };

        // gets the text of the tab
        var text_type_selected = (_text_viewer.text_types.filter(function (text_type) {
            return $(tab).hasClass(text_type);
        }))[0];

        _text_viewer.get_text(text_type_selected);
    });

    // Listener to : Load text on image change
    _event_emitter.addListener('image_id_set', function () {
        //console.log('Notified image_id_set');
        var selected_tab = _this.find('.text_viewer .tabs .tab.active').first();
        var text_type_selected = (_text_viewer.text_types.filter(function (text_type) {
            return $(selected_tab).hasClass(text_type);
        }))[0];
        _text_viewer.get_text(text_type_selected);
    });

    // Function to : Gets the text for a type (diplomatic, english, xml)
    _text_viewer.get_text = function (text_type) {
        if (_text_viewer.image_id) {
            // Mirador requires an URI for image id. We trick it by encoding the folio number as "http://n".  
            folio = _text_viewer.image_id.replace('http://', '');
        }

        // var manuscript_metadata = $("div.manuscript-metadata");
        var id = $("div.manuscript-metadata-id").text();
        console.log('id=', id);
        var folio_metadata = $("div.manuscript-metadata > div.folio-metadata:nth-child(" + folio + ")");

        if (text_type == 'diplomatic') {
            var html = folio_metadata.find("div.folio-metadata-or").text();
            console.log('html=', html);
            xhr_url = _text_viewer.xhr_url + '/' + id + '/' + html;
        } else {
            var html_en = folio_metadata.find("div.folio-metadata-en").text();
            console.log('html=', html);
            xhr_url = _text_viewer.xhr_url + '/' + id + '/' + html_en;
        }

        console.log('xhr_url=', xhr_url);
        
        $.get(xhr_url, function (data, status) {
            var content_pane = _this.find('.content_pane');
            content_pane.html($(data));
        });
    }

    // 'instance' variables
    _text_viewer.window_id = null;
    _text_viewer.text_types = ['diplomatic', 'english'];
    _text_viewer.xhr_url = 'data/manuscripts';
    
    // Listen to : click to trigger a custom event
    _this.find('.text_viewer .tabs .tab').click(function () {
        _event_emitter.emitEvent('tab_clicked', $(this));
    });

    // Listen to : Mirador windowUpdated, to get the window_id. 
    // This event is emitted multiple times - TODO check that is does not have side effects. 
    mirador_event_emitter.subscribe('windowUpdated', function (event, windowUpdate) {
        if (_text_viewer.window_id == undefined) {
            _text_viewer.window_id = windowUpdate.id;
            // listen to : Mirador image changes, to get the image_id
            mirador_event_emitter.subscribe('SET_CURRENT_CANVAS_ID.' + _text_viewer.window_id, function (event, image_id) {
                _text_viewer.image_id = image_id;
                _event_emitter.emitEvent('image_id_set');
            });
        }
    });

    // At initial display Mirador emits no event, so we get the first folio's first tab and show it
    _text_viewer.get_text('diplomatic');

    // disable english tab if no translation available
    var folio_1_english_metadata = $("div.manuscript-metadata > div.folio-metadata:first > div.folio-metadata-en");
    if (folio_1_english_metadata.text() == '') {
        _text_viewer.text_types = ['diplomatic'];
        $(".english").hide();
        $(".diplomatic").addClass("single");
        
        //$("ul.tabs").hide();
    }
    return _text_viewer;
} // TextViewer
