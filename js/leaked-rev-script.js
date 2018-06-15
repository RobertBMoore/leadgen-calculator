	function confirmForm()
	{
		var ordersPerMonth = document.getElementById('orders-per-month');
		var RevenuePerMonth = document.getElementById('revenue-per-month');
		
		if (Number(parseFloat(ordersPerMonth.value)) == ordersPerMonth.value && Number(parseFloat(RevenuePerMonth.value)) == RevenuePerMonth.value)
		{
			document.getElementById('client_error_msg').style.display = 'none';
			return true;
		}
		
		document.getElementById('client_error_msg').style.display = 'block';
		
		return false;
	}
	
	function confirmEmail()
	{
		var drg_regex = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		var emailAddress = document.getElementById('leaked_rev_email');
		var ordersPerMonth = document.getElementById('orders-per-month');
		var RevenuePerMonth = document.getElementById('revenue-per-month');
		var makeCall = 1;
		
		if (Number(parseFloat(ordersPerMonth.value)) == ordersPerMonth.value && Number(parseFloat(RevenuePerMonth.value)) == RevenuePerMonth.value)
		{
			document.getElementById('client_error_msg').style.display = 'none';
		}
		else
		{
			makeCall = 0;
			document.getElementById('client_error_msg').style.display = 'block';
		}
		
		if (drg_regex.test(emailAddress.value))
		{
			document.getElementById('valid_email').style.display = 'none';
			document.getElementById('my_results').style.display = 'none';
			document.getElementById('processing_results').style.display = 'block';
			
			if (makeCall)
			{
				/* Ajax call to PHP... */
				setTimeout(function() {
					$.ajax({
						type: "POST",
						url: "http://blog.digitalresearchgroup.org/leaked_rev_calc.php",
						data: 	"orders-per-month=" + ordersPerMonth.value + 
								"&revenue-per-month=" + RevenuePerMonth.value +
								"&email=" + emailAddress.value.replace(/\+/g, 'cs_plus'),
						success: function(html) {
							var leaked_rev_data = JSON.parse(html);
							document.getElementById('abandonsWithEmail').innerHTML = leaked_rev_data.abandonsWithEmail;
							document.getElementById('recoveredOrders').innerHTML = leaked_rev_data.recoveredOrders;
							document.getElementById('recoveredRev').innerHTML = leaked_rev_data.recoveredRevDisplay;
							document.getElementById('recoveredRev').title = leaked_rev_data.recoveredRev;
							document.getElementById('roi').innerHTML = leaked_rev_data.ROI;
							//document.getElementById('leadEmails').innerHTML = leaked_rev_data.leadEmails;
							//document.getElementById('probablePlanPrice').innerHTML = leaked_rev_data.probablePlanPrice;
							
							document.getElementById('roi_inputs').style.display = 'none';
							//document.getElementById('display_results').style.display = 'block';
							document.getElementById('results_headline_text').style.display = 'block';
							document.getElementById('results_container').style.display = 'block';

							document.getElementById('calc_results_message1').style.display = 'none';
							document.getElementById('calc_results_message2').style.display = 'block';

						}
					});
				}, 1000);
				
				return true;
			}
		}
		else
		{
			document.getElementById('valid_email').style.display = 'block';
		}
		
		return false;
	}
	
	function sampleSubmit()
	{
		var drg_regex = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		var firstName = document.getElementById('sample_firstname');
		var email = document.getElementById('sample_email');
		
		if (firstName.value.length > 0 && drg_regex.test(email.value))
		{
			alert('form submitted');
			$.ajax({
				type: "POST",
				url: "php/sample_request.php",
				data: 	"firstName=" + firstName.value + 
						"&email=" + email.value.replace(/\+/g, 'cs_plus'),
				success: function(html) {
					
					alert(html);
					
					var cartstats_data = JSON.parse(html);
					
					if (cartstats_data.contact_sync)
						console.log(cartstats_data.contact_sync);
					
					if (cartstats_data.response)
						console.log(cartstats_data.response);
					
					document.getElementById('sample_form').innerHTML = '<div style="background-color: #F1F1F1; border-radius: 5px 5px 5px 5px;-moz-border-radius: 5px 5px 5px 5px; -webkit-border-radius: 5px 5px 5px 5px; color: #2C3E50; font-size: 14px; padding: 10px 16px; box-shadow: 0 0 4px rgb(151, 195, 222);"><strong>Thanks!</strong> In the next 5-10 minutes, you\'ll receive the first of 3 emails with [EMAIL#1] in the subject line.  The link to your e-book, including bonuses, will be in the third email.</div>';
					document.getElementById('sample_request_error').style.display = 'none';
				}
			});
		}
		else
		{
			document.getElementById('sample_request_error').style.display = 'block';
		}
	}
