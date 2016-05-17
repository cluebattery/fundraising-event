	$(document).ready(function(){
		
		
		//run transactions.php to check for new names on PayPal
		setInterval(function() {
			$.ajax({
				url: "transactions.php",
				data: {account:'1'},
				type: "POST",
				success: function(response){ 
					//console.log('updated transactions account 1');
					$.ajax({
						url: "transactions.php",
						data: {account:'2'},
						type: "POST",
						success: function(response){ 
							//no response back to this script required
							//console.log('updated transactions account 2');
						},
						fail: function() {
							//no response back to this script required
							//console.log('failed to update transactions account 2');
						}
					});
				},
				fail: function() {
					//console.log('failed to update transactions account 1');
				}
				});
		}, 20000);
	
	});