document.observe("dom:loaded", function() {

	new PeriodicalExecuter(function(pe) {
		$('attain-cookies').fade({ duration: 1.0, from: 1, to: 0 });
		/* Removed as this might be seen to be forcing the user to accept */
		//Mage.Cookies.expires  = new Date((new Date()).getTime() + 365*24*3600000);
		//Mage.Cookies.set('cookies_allowed', 'allowed');
		pe.stop();
	}, 20);
	
	$('attain-show-cookie').observe('click', function() {
		$('attain-cookies').show();
	});
	
	$('cookie-close').observe('click', function() {
		$('attain-cookies').hide();
	});
	
	$('cookie-allow').observe('click', function() {
		Mage.Cookies.expires  = new Date((new Date()).getTime() + 365*24*3600000);
		Mage.Cookies.set('cookies_allowed', 'allowed');
		$('attain-cookies').hide();
	});
	
	$('cookie-refuse').observe('click', function() {
		Mage.Cookies.clear('cookies_allowed');
		$('attain-cookies').hide();
	});
	
});