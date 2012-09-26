
	$(document).ready(function(){
		jQuery(".nwwrapper button").click(function() {
			jQuery("#campaignmonitor-subscribe-form input:submit").click();
		});

		//campaign monitor
		$('#campaignmonitor-subscribe-form').submit(function(e) {
			e.preventDefault();
			var submission = $(this).serializeArray();
			submission['0'].value = "Subscriber";

			var base = 'http://localhost/oldcounty/';
			var url = base+'sites/libraries/campaignmonitor/samples/subscriber/addsubscribe.php';
			$.post(url, submission,
				function(data) {
					obj = JSON.parse(data);

					if(obj.RESULT == "OK"){
						var message = '<h2>Thanks for your interest in the old county!</h2><p>You are going to receive news from us by email, only interesting stuff, promise...</p>';
					}
					else{
						var message = '<h2>We got a problem!</h2><p>An error has occured, we will request you try and subscribe again soon.</p>';
					}
					$('.nwwrapper').html(message);
				}
			);
		});
	});

