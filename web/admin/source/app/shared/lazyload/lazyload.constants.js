(function () {
    'use strict';

    angular
        .module('app.lazyload')
        .constant('APP_REQUIRES', {
            // jQuery based and standalone scripts
            scripts: {
                'whirl': ['/admin/vendor/whirl/dist/whirl.css'],
                'classyloader': ['/admin/vendor/jquery-classyloader/js/jquery.classyloader.min.js'],
                'animo': ['/admin/vendor/animo.js/animo.js'],
                'fastclick': ['/admin/vendor/fastclick/lib/fastclick.js'],
                'modernizr': ['/admin/vendor/modernizr/modernizr.js'],
                'animate': ['/admin/vendor/animate.css/animate.min.css'],
                'skycons': ['/admin/vendor/skycons/skycons.js'],
                'icons': ['/admin/vendor/fontawesome/css/font-awesome.min.css',
                    '/admin/vendor/simple-line-icons/css/simple-line-icons.css'],
                'weather-icons': ['/admin/vendor/weather-icons/css/weather-icons.min.css'],
                //'sparklines':         ['/admin/vendor/sparklines/jquery.sparkline.min.js'],
                'wysiwyg': ['/admin/vendor/bootstrap-wysiwyg/bootstrap-wysiwyg.js',
                    '/admin/vendor/bootstrap-wysiwyg/external/jquery.hotkeys.js'],
                'slimscroll': ['/admin/vendor/slimScroll/jquery.slimscroll.min.js'],
                'screenfull': ['/admin/vendor/screenfull/dist/screenfull.js'],
                'vector-map': ['/admin/vendor/ika.jvectormap/jquery-jvectormap-1.2.2.min.js',
                    '/admin/vendor/ika.jvectormap/jquery-jvectormap-1.2.2.css'],
                'vector-map-maps': ['/admin/vendor/ika.jvectormap/jquery-jvectormap-world-mill-en.js',
                    '/admin/vendor/ika.jvectormap/jquery-jvectormap-us-mill-en.js'],
                'loadGoogleMapsJS': ['/admin/vendor/gmap/load-google-maps.js'],
                'flot-chart': ['/admin/vendor/Flot/jquery.flot.js'],
                'flot-chart-plugins': ['/admin/vendor/flot.tooltip/js/jquery.flot.tooltip.min.js',
                    '/admin/vendor/Flot/jquery.flot.resize.js',
                    '/admin/vendor/Flot/jquery.flot.pie.js',
                    '/admin/vendor/Flot/jquery.flot.time.js',
                    '/admin/vendor/Flot/jquery.flot.categories.js',
                    '/admin/vendor/flot-spline/js/jquery.flot.spline.min.js'],
                // jquery core and widgets
                'jquery-ui': ['/admin/vendor/jquery-ui/ui/core.js',
                    '/admin/vendor/jquery-ui/ui/widget.js'],
                // loads only jquery required modules and touch support
                'jquery-ui-widgets': ['/admin/vendor/jquery-ui/ui/core.js',
                    '/admin/vendor/jquery-ui/ui/widget.js',
                    '/admin/vendor/jquery-ui/ui/mouse.js',
                    '/admin/vendor/jquery-ui/ui/draggable.js',
                    '/admin/vendor/jquery-ui/ui/droppable.js',
                    '/admin/vendor/jquery-ui/ui/sortable.js',
                    '/admin/vendor/jqueryui-touch-punch/jquery.ui.touch-punch.min.js'],
                'moment': ['/admin/vendor/moment/min/moment-with-locales.min.js'],
                'inputmask': ['/admin/vendor/jquery.inputmask/dist/jquery.inputmask.bundle.min.js'],
                'flatdoc': ['/admin/vendor/flatdoc/flatdoc.js'],
                'codemirror': ['/admin/vendor/codemirror/lib/codemirror.js',
                    '/admin/vendor/codemirror/lib/codemirror.css'],
                // modes for common web files
                'codemirror-modes-web': ['/admin/vendor/codemirror/mode/javascript/javascript.js',
                    '/admin/vendor/codemirror/mode/xml/xml.js',
                    '/admin/vendor/codemirror/mode/htmlmixed/htmlmixed.js',
                    '/admin/vendor/codemirror/mode/css/css.js'],
                'taginput': ['/admin/vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.css',
                    '/admin/vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js'],
                'filestyle': ['/admin/vendor/bootstrap-filestyle/src/bootstrap-filestyle.js'],
                'parsley': ['/admin/vendor/parsleyjs/dist/parsley.min.js'],
                'fullcalendar': ['/admin/vendor/fullcalendar/dist/fullcalendar.min.js',
                    '/admin/vendor/fullcalendar/dist/fullcalendar.css'],
                'gcal': ['/admin/vendor/fullcalendar/dist/gcal.js'],
                'chartjs': ['/admin/vendor/Chart.js/Chart.js'],
                'morris': ['/admin/vendor/raphael/raphael.js',
                    '/admin/vendor/morris.js/morris.js',
                    '/admin/vendor/morris.js/morris.css'],
                'loaders.css': ['/admin/vendor/loaders.css/loaders.css'],
                'spinkit': ['/admin/vendor/spinkit/css/spinkit.css']
            },
            // Angular based script (use the right module name)
            modules: [
                {
                    name: 'toaster', files: ['/admin/vendor/angularjs-toaster/toaster.js',
                    '/admin/vendor/angularjs-toaster/toaster.css']
                },
                {
                    name: 'localytics.directives',
                    files: [
                        '/admin/vendor/chosen_v1.2.0/chosen.jquery.min.js',
                        '/admin/vendor/chosen_v1.2.0/chosen.min.css',
                        '/admin/vendor/angular-chosen-localytics/chosen.js'
                    ]
                },
                {
                    name: 'ngDialog', files: ['/admin/vendor/ngDialog/js/ngDialog.min.js',
                    '/admin/vendor/ngDialog/css/ngDialog.min.css',
                    '/admin/vendor/ngDialog/css/ngDialog-theme-default.min.css']
                },
                {name: 'ngWig', files: ['/admin/vendor/ngWig/dist/ng-wig.min.js']},
                {
                    name: 'ngTable', files: ['/admin/vendor/ng-table/dist/ng-table.min.js',
                    '/admin/vendor/ng-table/dist/ng-table.min.css']
                },
                {name: 'ngTableExport', files: ['/admin/vendor/ng-table-export/ng-table-export.js']},
                {
                    name: 'angularBootstrapNavTree',
                    files: ['/admin/vendor/angular-bootstrap-nav-tree/dist/abn_tree_directive.js',
                        '/admin/vendor/angular-bootstrap-nav-tree/dist/abn_tree.css']
                },
                {
                    name: 'htmlSortable', files: ['/admin/vendor/html.sortable/dist/html.sortable.js',
                    '/admin/vendor/html.sortable/dist/html.sortable.angular.js']
                },
                {
                    name: 'xeditable', files: ['/admin/vendor/angular-xeditable/dist/js/xeditable.js',
                    '/admin/vendor/angular-xeditable/dist/css/xeditable.css']
                },
                {name: 'angularFileUpload', files: ['/admin/vendor/angular-file-upload/angular-file-upload.js']},
                {
                    name: 'ngImgCrop', files: ['/admin/vendor/ng-img-crop/compile/unminified/ng-img-crop.js',
                    '/admin/vendor/ng-img-crop/compile/unminified/ng-img-crop.css']
                },
                {
                    name: 'ui.select', files: ['/admin/vendor/angular-ui-select/dist/select.js',
                    '/admin/vendor/angular-ui-select/dist/select.css']
                },
                {name: 'ui.codemirror', files: ['/admin/vendor/angular-ui-codemirror/ui-codemirror.js']},
                {
                    name: 'angular-carousel', files: ['/admin/vendor/angular-carousel/dist/angular-carousel.css',
                    '/admin/vendor/angular-carousel/dist/angular-carousel.js']
                },
                {
                    name: 'ngGrid', files: ['/admin/vendor/ng-grid/build/ng-grid.min.js',
                    '/admin/vendor/ng-grid/ng-grid.css']
                },
                {name: 'infinite-scroll', files: ['/admin/vendor/ngInfiniteScroll/build/ng-infinite-scroll.js']},
                {
                    name: 'ui.bootstrap-slider',
                    files: ['/admin/vendor/seiyria-bootstrap-slider/dist/bootstrap-slider.min.js',
                        '/admin/vendor/seiyria-bootstrap-slider/dist/css/bootstrap-slider.min.css',
                        '/admin/vendor/angular-bootstrap-slider/slider.js']
                },
                {
                    name: 'ui.grid', files: ['/admin/vendor/angular-ui-grid/ui-grid.min.css',
                    '/admin/vendor/angular-ui-grid/ui-grid.min.js']
                },
                {
                    name: 'textAngular', files: ['/admin/vendor/textAngular/dist/textAngular.css',
                    '/admin/vendor/textAngular/dist/textAngular-rangy.min.js',
                    '/admin/vendor/textAngular/dist/textAngular-sanitize.js',
                    '/admin/vendor/textAngular/src/globals.js',
                    '/admin/vendor/textAngular/src/factories.js',
                    '/admin/vendor/textAngular/src/DOM.js',
                    '/admin/vendor/textAngular/src/validators.js',
                    '/admin/vendor/textAngular/src/taBind.js',
                    '/admin/vendor/textAngular/src/main.js',
                    '/admin/vendor/textAngular/dist/textAngularSetup.js'
                ], serie: true
                },
                {
                    name: 'angular-rickshaw', files: ['/admin/vendor/d3/d3.min.js',
                    '/admin/vendor/rickshaw/rickshaw.js',
                    '/admin/vendor/rickshaw/rickshaw.min.css',
                    '/admin/vendor/angular-rickshaw/rickshaw.js'], serie: true
                },
                {
                    name: 'angular-chartist', files: ['/admin/vendor/chartist/dist/chartist.min.css',
                    '/admin/vendor/chartist/dist/chartist.js',
                    '/admin/vendor/angular-chartist.js/dist/angular-chartist.js'], serie: true
                },
                {name: 'ui.map', files: ['/admin/vendor/angular-ui-map/ui-map.js']},
                {
                    name: 'datatables', files: ['/admin/vendor/datatables/media/css/jquery.dataTables.css',
                    '/admin/vendor/datatables/media/js/jquery.dataTables.js',
                    '/admin/vendor/angular-datatables/dist/angular-datatables.js'], serie: true
                },
                {
                    name: 'angular-jqcloud', files: ['/admin/vendor/jqcloud2/dist/jqcloud.css',
                    '/admin/vendor/jqcloud2/dist/jqcloud.js',
                    '/admin/vendor/angular-jqcloud/angular-jqcloud.js']
                },
                {
                    name: 'angularGrid', files: ['/admin/vendor/ag-grid/dist/angular-grid.css',
                    '/admin/vendor/ag-grid/dist/angular-grid.js',
                    '/admin/vendor/ag-grid/dist/theme-dark.css',
                    '/admin/vendor/ag-grid/dist/theme-fresh.css']
                },
                {
                    name: 'ng-nestable', files: ['/admin/vendor/ng-nestable/src/angular-nestable.js',
                    '/admin/vendor/nestable/jquery.nestable.js']
                },
                {name: 'akoenig.deckgrid', files: ['/admin/vendor/angular-deckgrid/angular-deckgrid.js']},
                {
                    name: 'oitozero.ngSweetAlert', files: ['/admin/vendor/sweetalert/dist/sweetalert.css',
                    '/admin/vendor/sweetalert/dist/sweetalert.min.js',
                    '/admin/vendor/angular-sweetalert/SweetAlert.js']
                },
                {
                    name: 'bm.bsTour', files: ['/admin/vendor/bootstrap-tour/build/css/bootstrap-tour.css',
                    '/admin/vendor/bootstrap-tour/build/js/bootstrap-tour-standalone.js',
                    '/admin/vendor/angular-bootstrap-tour/dist/angular-bootstrap-tour.js'], serie: true
                },
                {
                    name: 'ui.knob', files: ['/admin/vendor/angular-knob/src/angular-knob.js',
                    '/admin/vendor/jquery-knob/dist/jquery.knob.min.js']
                },
                {name: 'easypiechart', files: ['/admin/vendor/jquery.easy-pie-chart/dist/angular.easypiechart.min.js']},
                {
                    name: 'colorpicker.module',
                    files: ['/admin/vendor/angular-bootstrap-colorpicker/css/colorpicker.css',
                        '/admin/vendor/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.js']
                }
            ]
        })
    ;

})();