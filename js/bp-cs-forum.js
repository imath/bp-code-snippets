jQuery(document).ready(function($) {
	if( $('body').hasClass('directory') ){
		$('.thickbox').click( function() {
			if( !$('#topic_group_id').val() ){
				alert(bpcs_message_select_group);
				return false;
			} else{
				var new_tb = '';
				if( $(this).attr('href').match(/item_id=&/) )
					new_tb = $(this).attr('href').replace( /item_id=&/, 'item_id=' + $('#topic_group_id').val()+'&' );
					
				if( $(this).attr('href').match(/item_id=\d&/) )
					new_tb = $(this).attr('href').replace( /item_id=\d&/, 'item_id=' + $('#topic_group_id').val()+'&' );
				
				$(this).attr('href', new_tb);
			}
		})
	}
});