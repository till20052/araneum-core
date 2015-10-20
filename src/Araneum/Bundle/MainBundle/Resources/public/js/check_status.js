$(document).ready(function(){

	var checkStatus = function(url){
		$.get(url, function(response){
			console.log(response);
		}, 'json')
	}

	$('table.table > tbody').click(function(e){
		var a = $(e.target);

		if(a.tagName != 'A'){
			a = $(a).parents('a[data-action]');
		}

		if($(a).attr('data-action') == 'check_status'){
			checkStatus($(a).attr('data-href'));
		}
	});

});