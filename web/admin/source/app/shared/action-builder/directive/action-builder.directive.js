(function() {
    "use strict";

     // надо будет создать общею фабрику что то вроде dataCache, для кеширования данных 
     // чтобы можно было добавлять, извлекать и удалять данные
    var dataActionCache;

    angular
        .module( 'app.action-builder' )
        .directive( 'actionBuilder', actionBuilder );

    actionBuilder.$inject = ['$compile', '$location', 'formDataService', 'creatorActionBuilder', 'RouteHelpers'];

    function actionBuilder( $compile, $location, formDataService, creatorActionBuilder ) {
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

                if (dataActionCache.urlData === undefined)  {
                    dataActionCache.urlData =  $location.$$path;
                }
                
                if (dataActionCache.urlData !==  $location.$$path) {
                    dataActionCache.urlData =  $location.$$path;
                    dataActionCache = response.action;
                }

                var actions = dataActionCache,
                    actionsTemplate = '';

                if (actions[ type ] === undefined) {
                    return false;
                }

                builder.setData( actions[ type ], attrs.model, $scope );
                actionsTemplate = builder.getActionsTemplate();
                element.append( $compile( actionsTemplate )( $scope ) );
            }
        }
    }
})();
