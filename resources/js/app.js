import "./bootstrap";
import Alpine from "alpinejs";

import * as Mirador from "mirador/dist/mirador.min.js";

window.Alpine = Alpine;

Alpine.data("manuscriptShow", (data = []) => ({
    miradorInstance: null,

    // See here for more details:
    // https://github.com/ProjectMirador/mirador/blob/master/src/config/settings.js
    init() {
        let collectionContent = {
            collection: null,
            manifest: null,
            mirador: {
                loaded: false,
            },
        };

        // See config options from here:
        // https://github.com/ProjectMirador/mirador/wiki/
        // https://github.com/ProjectMirador/mirador-2-wiki/wiki/Configuration-Guides
        // https://github.com/ProjectMirador/mirador-2-wiki/wiki/Complete-Configuration-API
        // https://github.com/ProjectMirador/mirador-2-wiki/wiki/Mirador-Initialization
        // https://github.com/ProjectMirador/mirador-2-wiki/wiki/Events-in-Mirador
        this.miradorInstance = new Mirador.viewer({
            id: "mirador", // id selector where Mirador should be instantiated
            // buildPath: "resources/frontend/js/mirador-v2.7.0/",
            // data: [
            //     {
            //         // manifestUri: mr.state.manifest['@id'],
            //         // manifest: mr.state.manifest,
            //         collectionContent: collectionContent,
            //         location: "SIB DH+",
            //     },
            // ],
            // openManifestsPage: false, // Open manifest selector on load
            // mainMenuSettings: {
            //     show: true,
            // },
            // showAddFromURLBox: false,
            // windowObjects: [
            //     {
            //         loadedManifest: collectionContent["@id"],
            //         // "canvasID": mr.state.manifest.sequences[0].canvases[0]['@id'],
            //         viewType: "ImageView",
            //         annotationLayer: false,
            //         bottomPanel: true,
            //         bottomPanelVisible: true,
            //         sidePanel: false,
            //         sidePanelVisible: false,
            //         annotationLayer: false,
            //         displayLayout: false,
            //     },
            // ],

            window: {
                //global window defaults
                allowClose: false, // Configure if windows can be closed or not
                allowFullscreen: false, // Configure to show a "fullscreen" button in the WindowTopBar
                allowMaximize: true, // Configure if windows can be maximized or not
                allowTopMenuButton: true, // Configure if window view and thumbnail display menu are visible or not
                allowWindowSideBar: true, // Configure if side bar menu is visible or not
                authNewWindowCenter: "parent", // Configure how to center a new window created by the authentication flow. Options: parent, screen
                sideBarPanel: "info", // Configure which sidebar is selected by default. Options: info, attribution, canvas, annotations, search
                defaultSidebarPanelHeight: 201, // Configure default sidebar height in pixels
                defaultSidebarPanelWidth: 235, // Configure default sidebar width in pixels
                defaultView: "single", // Configure which viewing mode (e.g. single, book, gallery) for windows to be opened in
                forceDrawAnnotations: false,
                hideWindowTitle: false, // Configure if the window title is shown in the window title bar or not
                highlightAllAnnotations: false, // Configure whether to display annotations on the canvas by default
                showLocalePicker: false, // Configure locale picker for multi-lingual metadata
                sideBarOpen: false, // Configure if the sidebar (and its content panel) is open by default
                switchCanvasOnSearch: true, // Configure if Mirador should automatically switch to the canvas of the first search result
                panels: {
                    // Configure which panels are visible in WindowSideBarButtons
                    info: true,
                    attribution: true,
                    canvas: true,
                    annotations: true,
                    search: true,
                    layers: true,
                },
                views: [
                    { key: "single", behaviors: ["individuals"] },
                    { key: "book", behaviors: ["paged"] },
                    { key: "scroll", behaviors: ["continuous"] },
                    { key: "gallery" },
                ],
                // elastic: {
                //     height: 400,
                //     width: 480,
                // },
            },
            thumbnails: {
                preferredFormats: ["jpg", "png", "webp", "tif"],
            },
            thumbnailNavigation: {
                defaultPosition: "far-bottom", // Which position for the thumbnail navigation to be be displayed. Other possible values are "far-bottom" or "far-right"
                displaySettings: true, // Display the settings for this in WindowTopMenu
                height: 130, // height of entire ThumbnailNavigation area when position is "far-bottom"
                width: 100, // width of one canvas (doubled for book view) in ThumbnailNavigation area when position is "far-right"
            },
            windows: [
                {
                    loadedManifest:
                    "http://localhost/iiif/GA019/manifest",
                    // "https://purl.stanford.edu/fg165hz3589/iiif/manifest",
                },
            ],
            // osdConfig: {
            //     maxZoomPixelRatio: 5,
            // },
            workspace: {
                showZoomControls: true,
                type: "mosaic", // Which workspace type to load by default. Other possible values are "elastic"
            },
        });
    },
}));

Alpine.start();
