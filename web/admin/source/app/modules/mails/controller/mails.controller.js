(function (ng) {
    'use strict';

    ng.module('app.mails')
        .controller('MailsController', MailsController);

    MailsController.$inject = ['$scope'];
    function MailsController($scope) {
        $scope.title = 'mails.MAILS';
        $scope.titleClass = 'icon-globe-alt mr';
    }
})
(angular);