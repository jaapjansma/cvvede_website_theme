jQuery(document).ready(function(){

	(function($) {

		$('#mobilemenu_trigger').click(function(e){
			e.preventDefault();
			$('body').toggleClass('mobilemenu-open');
		});
    
	    $('#teaser, #main').click(function(e){
			$('body').removeClass('mobilemenu-open');
	    });
	    
	    $("#mainnav").focusout(function() {
		    $('body').removeClass('mobilemenu-open');
	    });

	})(jQuery);

});