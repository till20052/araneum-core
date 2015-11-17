(function (angular) {

	'use strict';

	angular
		.module('araneum')
		.service('User', ['$sessionStorage', User]);

	function User($storage) {

		var user = {
			name: '',
			email: '',
			picture: '',
			settings: {},
			isAuthorized: false
		};

		data($storage.user || {});

		return {
			set: set,
			get: get,
			getName: getName,
			getEmail: getEmail,
			setSettings: setSettings,
			setAsAuthorized: setAsAuthorized,
			isAuthorized: isAuthorized,
			data: data
		};

		function set($key, $value) {
			user[$key] = $value;
			$storage.user = user;
			return this;
		}

		function get(key) {
			return user[key] || null;
		}

		function getName() {
			return get('name');
		}

		function getEmail(){
			return get('email');
		}

		function setSettings(settings){
			return set('settings', settings);
		}

		function getSettings(){
			return get('settings');
		}

		function setAsAuthorized(){
			user.isAuthorized = true;
		}

		function isAuthorized(){
			return user.isAuthorized;
		}

		function data(data){
			angular.forEach(data, function (value, key) {
				set(key, value);
			});
		}
	}



})(angular);