(function(angular){

	'use strict';

	angular
		.module('araneum')
		.factory('UserAuth', [UserAuth]);

	function UserAuth(){

		return {
			$get: getInstance,
			setUserData: setUserData
		};

		function getInstance(){

		}

		function setUserData(data){
			console.log(data);
		}

	}

})(angular);
