(function( $ ) {
	'use strict';

	console.log(data_js)

	$(document).ready(function() {

		$('#coinmarket_from_number').bind('change keyup', function(e){
			e.preventDefault();
			let data_arr = get_data();
			let qqq = calculate_rate_to( data_arr );

		});
		$('#coinmarket_to_number').bind('change keyup', function(e){
			e.preventDefault();
			let data_arr = get_data();
			let qqq = calculate_rate_from( data_arr );
		});

		$('#coinmarket_from_select').bind('change keyup', function(e){
			e.preventDefault();
			let data_arr = get_data();
			let qqq = calculate_rate_to( data_arr );
		});
		$('#coinmarket_to_select').bind('change keyup', function(e){
			e.preventDefault();
			let data_arr = get_data();
			let qqq = calculate_rate_to( data_arr );
		});


		function get_data(){
			let dataarr = [];

			let input_from = $('#coinmarket_from_number').val();
			let input_to = $('#coinmarket_to_number').val();
			let select_from = $('#coinmarket_from_select').val();
			let select_to = $('#coinmarket_to_select').val();
			let price_from = $('#coinmarket_from_select option:selected').attr('data-price');
			let price_to = $('#coinmarket_to_select option:selected').attr('data-price');
			let symbol_from = $('#coinmarket_from_select option:selected').attr('data-symbol');
			let symbol_to = $('#coinmarket_to_select option:selected').attr('data-symbol');

			dataarr = ({
				input_from:input_from,
				input_to:input_to,
				select_from:select_from,
				select_to:select_to,
				price_from:price_from,
				price_to:price_to,
				symbol_from:symbol_from,
				symbol_to:symbol_to
			});
			return dataarr;

		}

		function calculate_rate_to( data_arr ){
			let input_to =  ( data_arr.input_from * data_arr.price_to) / data_arr.price_from;
			$('#coinmarket_to_number').val('');
			$('#coinmarket_to_number').val(input_to);
			// return input_to;
			$.ajax({
				url: data_js.ajax_url,
				type: 'POST',
				data: {
					action: 'save_history',
					input_from: data_arr.input_from,
					input_to:    input_to,
					symbol_from: data_arr.symbol_from,
					symbol_to: data_arr.symbol_to,
					price_from:   data_arr.price_from,
					price_to: data_arr.price_to,
				},
			})
			.done(function(response) {
				// console.log('response', response);
				try {
					console.log( response );
				}
				catch (err) {
					console.log('err', err);
				}
			})

		}

		function calculate_rate_from( data_arr ){
			let input_from =  ( data_arr.input_to * data_arr.price_from) / data_arr.price_to;
			$('#coinmarket_from_number').val('');
			$('#coinmarket_from_number').val(input_from);
			// return input_from;
			$.ajax({
				url: data_js.ajax_url,
				type: 'POST',
				data: {
					action: 'save_history',
					input_from: input_from,
					input_to:    data_arr.input_to,
					price_from:   data_arr.price_from,
					price_to: data_arr.price_to,
					symbol_from: data_arr.symbol_from,
					symbol_to: data_arr.symbol_to,
				},
			})
			.done(function(response) {
				// console.log('response', response);
				try {
					console.log( response );
				}
				catch (err) {
					console.log('err', err);
				}
			})
		}




	});

})( jQuery );
