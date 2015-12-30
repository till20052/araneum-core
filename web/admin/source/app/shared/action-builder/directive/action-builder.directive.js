(function() {
    "use strict";

    var dataActionCache; // надо будет создать общею фабрику что то вроде dataCache, для кеширования данных 
    angular
        .module( 'app.action-builder' )
        .directive( 'actionBuilder', actionBuilder );

    actionBuilder.$inject = ['$compile', 'formDataService', 'creatorActionBuilder', 'RouteHelpers'];

    function actionBuilder( $compile, formDataService, creatorActionBuilder ) {
        var directive = {
            restrict: 'E',
            link: link
        };
        
        return directive;

        function link ( $scope, element, attrs ) {
            var type = element.data( 'item' ), // top or row
                builder = creatorActionBuilder.getBuilder( type ), // return top or row action service builder
                promise = formDataService.getPromise();

            if ( typeof type === "undefined" ) {
                element.remove();
                return false;
            }

            promise.then( getDataForActions );

            function getDataForActions ( response ) {
                dataActionCache = dataActionCache ? dataActionCache : response.action;

                var actions = dataActionCache,
                    actionsTemplate = '';

                if ( actions[ type ] === undefined ) {
                    return false;
                }

                builder.setData( actions[ type ], attrs.model, $scope );
                actionsTemplate = builder.getActionsTemplate();
                element.append( $compile( actionsTemplate )( $scope ) );
            }
        }
    }
})();
