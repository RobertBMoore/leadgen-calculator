var leakedRevenueForm = {

	config: {
		// todo rename property
		post  : 'http://digitalresearchgroup.org/leaked_rev_optin/leaked_rev_calc.php',
		valid : true,
		button: {
			// Next Button Selector
			next  : '.leaked-optin__next',
			// Submit Button Selector
			submit: '.leaked-optin__submit',
		},

		field: {
			email         : '[name="leaked_optin[email]"]', 
			ordersPerMonth: '[name="leaked_optin[orders_per_month]"]',
			monthlyRevenue: '[name="leaked_optin[monthly_revenue]"]'
		},
		value: {
			email         : '', 
			ordersPerMonth: '',
			monthlyRevenue: ''

		}

	},

	init: function() {

		// Event Listeners
		$(this.config.button.next).on('click', function(){

			// Reset valid
			leakedRevenueForm.config.valid = true;
			$('.leaked-optin__error-message').css({'display':'none'}).html('');
			leakedRevenueForm.validate.field();

		});


		$(this.config.button.submit).on('click', function(){

			// Reset valid
			leakedRevenueForm.config.valid = true;
			$('.leaked-optin__error-message').css({'display':'none'}).html('');
			leakedRevenueForm.validate.email();

		});

		// Only Allow Numbers in text fields
		$(this.config.field.ordersPerMonth + ',' + this.config.field.monthlyRevenue)
			.on('change paste keypress', function(e) {
				if(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
					e.preventDefault();
					return false;
				}
		});

	},

	validate: {

		email: function() {

			var emailRegex = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			var selector  = $(leakedRevenueForm.config.field.email);

			if(!emailRegex.test(selector.val())) {
				$('.leaked-optin__error-message').css({'display':'block'})
						.html('Please use correct Email format');

				// Add red border around text field
				selector.css({'border': '2px solid #ff0000'});
			} else {
				leakedRevenueForm.config.value.email   = $(leakedRevenueForm.config.field.email).val();
				leakedRevenueForm.submit();
			}

		},
		field: function() {

			var field  = leakedRevenueForm.config.field;
			var fields = [field.ordersPerMonth, field.monthlyRevenue];


			for(var fieldsAmount = fields.length - 1; fieldsAmount >= 0; fieldsAmount--) {

				var selector = $(fields[fieldsAmount]);

				// Replace everything that is not a number with nothing
				var filtered = selector.val().replace(/[^0-9]/gi, '');

				if(filtered === '') {

					// Add red border around text field
					selector.css({'border': '2px solid #ff0000'});

					$('.leaked-optin__error-message').css({'display':'block'})
						.html('Fields must be Numbers');

					leakedRevenueForm.config.valid = false;

				} else {

					// Check if greater than 0
					if(parseInt(filtered) <= 0) {

						// todo et CSS in Stylesheet and add class here
						// Add red border around text field
						selector.css({'border': '2px solid #ff0000'});

						$('.leaked-optin__error-message').css({'display':'block'})
						.html('Fields must be Greater than 0');

						leakedRevenueForm.config.valid = false;

					} else {
						selector.css({'border': 'none'});
					}
					
				}



			}

			if(leakedRevenueForm.config.valid) {

				// todo loop through and set
				leakedRevenueForm.config.value.monthlyRevenue = $(leakedRevenueForm.config.field.monthlyRevenue).val();
				leakedRevenueForm.config.value.ordersPerMonth = $(leakedRevenueForm.config.field.ordersPerMonth).val();


				$('.leaked-optin__form').fadeOut(100, function() {
					$('.leaked-optin__loading-icon').fadeIn(200);

					setTimeout(function(){
						$('.leaked-optin__fields')
							.css({'display':'none'});
						$('.leaked-optin__next').css({'display':'none'});
						$('.leaked-optin__submit').css({'display':'inline-block'});

						// Show Email
						$('.leaked-optin__email-field').css({'display':'block'});
						$('.leaked-optin__loading-icon').fadeOut(0);
						$('.leaked-optin__form').fadeIn(200)
					}, 1200);
				});


				
			}

		}

	},

	submit : function() {

		$('.leaked-optin__form').fadeOut(100, function() {
			$('.leaked-optin__loading-icon').fadeIn(200, function() {

			$.ajax({
	            type: "POST",
	            url : leakedRevenueForm.config.post,
	            data: {
	                "email"             : leakedRevenueForm.config.value.email,
	                "orders-per-month"  : leakedRevenueForm.config.value.monthlyRevenue,
	                "revenue-per-month" : leakedRevenueForm.config.value.ordersPerMonth 
	            },
	            dataType: "json",
	            success: function(html) {

	            	// todo display success message
	                console.log('response');
	                console.log(html);
	            },
				error: function(request, status, error) {

					// todo create function that handles error displaying
					$('.leaked-optin__loading-icon').fadeOut(100, function() {
						$('.leaked-optin__form').fadeIn(200, function() {
							$('.leaked-optin__error-message').css({'display':'block'})
							.html('Oops something went wrong!');
						});

					});

				}
        	});

			});
		});
	
		


	}

};



leakedRevenueForm.init();