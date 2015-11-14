(function(angular) {
	'use strict';

	angular
		.module('araneum')
		.service('UserAuth', [UserAuth]);

	function UserAuth(){

		return {
			user: user
		};

		function user(){

		}

	}

})(angular);

/**
 * @ngdoc service
 * @name UserAuth
 * @description
 * _Please update the description and dependencies._
 *
 * */
angular.module('araneum')
    .service('UserAuth', function(){

    this.testMethod = function() {

    }

});

