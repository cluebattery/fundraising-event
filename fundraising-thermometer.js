	$(document).ready(function(){
		$thermOld = 777;
		$thermNew = 777;
		$toggle = 0;
	
        setInterval(function() {
			$thermOld = $thermNew;
			$.ajax({
				type: 'POST',
				url: "getBalances.php",
				data: {oldHeight: $thermOld},
				dataType: 'json',
				success: function(result){
					$("#thermometer").html(result.html);
					if ($toggle==0) {
                        $("#thermo").css('height', 777);
						$toggle = 1;
                    } else {
						position = {'height': result.height};
						$("#thermo").css('height', $thermOld).animate(position);
						$thermNew = result.height;
						if (result.balance >= 105000) {
                            $("#wow").fadeIn(3000,'swing');
						} else {
							$("#wow").css('display', 'none');
						}
					}
					
				}});
        }, 5000);
    });