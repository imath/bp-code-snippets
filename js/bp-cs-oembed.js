jQuery(document).ready(function($) {
	
	$('.copy-code').click( function () {
		
		$(this).parent().find('.copy-link-snippet').hide();
		$(this).parent().find('.copy-shortcode-snippet').hide();
		
		if( $('body').attr('id') == 'bp-cs-embed' )
			$(this).parent().find('.copy-embed-snippet').slideToggle();
		else 
			$(this).parent().find('.copy-embed-snippet').show();
			
		$(this).parent().find('.snipt-code').focus();
		return false;
	});

	$('.copy-link').click( function () {
		
		$(this).parent().find('.copy-embed-snippet').hide();
		$(this).parent().find('.copy-shortcode-snippet').hide();
		
		if( $('body').attr('id') == 'bp-cs-embed' )
			$(this).parent().find('.copy-link-snippet').slideToggle();
		else
			$(this).parent().find('.copy-link-snippet').show();
			
		$(this).parent().find('.snipt-perma').focus();
		return false;
	});
	
	$('.copy-shortcode').click( function () {
		
		$(this).parent().find('.copy-embed-snippet').hide();
		$(this).parent().find('.copy-link-snippet').hide();
		
		if( $('body').attr('id') == 'bp-cs-embed' )
			$(this).parent().find('.copy-shortcode-snippet').slideToggle();
		else
			$(this).parent().find('.copy-shortcode-snippet').show();
		
		shortcode = $(this).parent().find('.copy-shortcode-snippet .snipt-shortcode').val();
		shortcode = shortcode.replace(/″/g,'"');
		shortcode = shortcode.replace(/“/g,'"');
		shortcode = shortcode.replace(/”/g,'"');
		$(this).parent().find('.copy-embed-snippet .snipt-shortcode').val(shortcode);
		
		$(this).parent().find('.snipt-shortcode').focus();
		
		return false;
	});

	$.fn.selectRange = function(start, end) {
	    return this.each(function() {
	        if(this.setSelectionRange) {
	            this.focus();
	            this.setSelectionRange(start, end);
	        } else if(this.createTextRange) {
	            var range = this.createTextRange();
	            range.collapse(true);
	            range.moveEnd('character', end);
	            range.moveStart('character', start);
	            range.select();
	        }
	 });
	};

	$('.snipt-code').focus( function() {
		$(this).selectRange( 0, $(this).val().length );
	});
	$('.snipt-code').click( function() {
		$(this).selectRange( 0, $(this).val().length );
	});

	$('.snipt-perma').focus( function() {
		$(this).selectRange( 0, $(this).val().length );
	});
	$('.snipt-perma').click( function() {
		$(this).selectRange( 0, $(this).val().length );
	});
	
	$('.snipt-shortcode').focus( function() {
		$(this).selectRange( 0, $(this).val().length );
	});
	$('.snipt-shortcode').click( function() {
		$(this).selectRange( 0, $(this).val().length );
	});
	
});