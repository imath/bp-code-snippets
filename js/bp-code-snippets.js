jQuery(document).ready(function($){
	if ( '-1' == window.location.toString().indexOf('new') )
	        $('div#new-snippet').hide();
	 else{
		$('div#new-snippet').show();
		$('#snippet_title').focus();
	}
	
	if ('-1' != window.location.toString().indexOf('s-reply') )
		$('#s-reply #comment_content').focus();
		
	if( $('#s-reply').length ){
		
		if ('-1' != window.location.toString().indexOf('s-reply') )
			$('#s-reply #comment_content').focus();
		
		$('.snippet-comment-reply').click( function(){
			$('#s-reply #comment_content').focus();
		});
	}
	
	$.cookie( 'bp-snippets-fav-oldestpage', 1, {path: '/'} );       
	
	$('a.show-hide-new-snippet').click( function() {
		
		if ( !$('div#new-snippet').length )
		            return false;

		if ( $('div#new-snippet').is(":visible") )
			$('div#new-snippet').slideUp(200);
		else
		    $('div#new-snippet').slideDown(200, function() { $('#snippet_title').focus(); } );

		        return false;
		
	});

	/* Cancel the posting of a new forum topic */
	$('input#submit_snippet_cancel').click( function() {
		
		if ( !$('div#new-snippet').length )
       		return false;

		$('div#new-snippet').slideUp(200);
		return false;
		
	});
	
	/* handle submit */
	$('form#snippets-form').submit( function() {
		
		if( $('form#snippets-form #snippet_title').val().length < 3 ) {
			alert(bpcs_message_title);
			$('form#snippets-form #snippet_title').focus();
			return false;
		}
		if( $('form#snippets-form #bp_cs_source').val() == 0 ) {
			alert(bpcs_message_cat);
			$('form#snippets-form #bp_cs_source').focus();
			return false;
		}
		if( $('form#snippets-form #snippet_purpose').val().length < 3 ) {
			alert(bpcs_message_desc);
			$('form#snippets-form #snippet_purpose').focus();
			return false;
		}
		if( $('form#snippets-form #snippet_content').val().length < 3 ) {
			alert(bpcs_message_content);
			$('form#snippets-form #snippet_content').focus();
			return false;
		}
		
		return true;
	});
	
	/* handle group change */
	$('#snippet_group_id').change( function() {
		bp_cs_filter_language_for_group($(this).val());
	});
	
	$('.sniptf').live( 'click', function() {
		if($(this).hasClass('snippet-fav')) type = 'fav';
		if($(this).hasClass('snippet-unfav')) type = 'unfav';
		
		var parent = $(this).parent().parent().parent();
        var parent_id = parent.attr('id').substr( 8, parent.attr('id').length );

		$(this).addClass('loading');
		favnonce = $(this).attr('href').split('_wpnonce=');
		
		bp_cs_add_snippet_fav( parent_id, favnonce[1], type, $(this) );
		
		if($('div.item-list-tabs ul li#snippets-favs').length){
			if(type == 'fav'){
				if(!$('div.item-list-tabs ul li#snippets-favs span').html().length)
					$('div.item-list-tabs ul li#snippets-favs span').html(0);
				$('div.item-list-tabs ul li#snippets-favs span').html( Number( $('div.item-list-tabs ul li#snippets-favs span').html() ) + 1 );
			} else {
				$('div.item-list-tabs ul li#snippets-favs span').html( Number( $('div.item-list-tabs ul li#snippets-favs span').html() ) - 1 );
			}

			if( $('#snippets-favs').hasClass('selected') ){
				parent.slideUp(100);
			}
		}
		
		if($('#fav_counter-'+parent_id).length){
			if(type == 'fav'){
				$('#fav_counter-'+parent_id+' span').html( Number( $('#fav_counter-'+parent_id+' span').html() ) + 1 );
			} else {
				$('#fav_counter-'+parent_id+' span').html( Number( $('#fav_counter-'+parent_id+' span').html() ) - 1 );
			}
		}
		
		if($('div.item-list-tabs ul li#snippets-favs-personal-li').length){
			if( $('#snippets-favs-personal-li').hasClass('selected') ){
				parent.slideUp(100);
			}
		}
		
		return false;
	});
	
	$('.snippets-load-more a').click( function() {
		
		bp_cs_fav_load_more( $(this) );
	
		return false;
		
	});
	
	$('.snippets-load-more-live a').live('click', function() {
		
		bp_cs_fav_load_more( $(this) );
	
		return false;
		
	});
	
	/* comment moderation */
	
	$('.comment-delete-link').click( function() {
		
		var parent = $(this).parent().parent().parent();
        var parent_id = parent.attr('id').substr( 16, parent.attr('id').length );
		var snippet_id = $('.snippet-entry').attr('id').substr( 8, $('.snippet-entry').attr('id').length );

		$(this).addClass('loading');
		comnonce = $(this).attr('href').split('_wpnonce=');
		
		bp_cs_del_snippet_comment( parent_id, snippet_id, comnonce[1], $(this) );
		
		if( $('.snippet-meta .snippet-comment-reply').length ){
			$('.snippet-meta .snippet-comment-reply span').html( Number( $('.snippet-meta .snippet-comment-reply span').html() ) - 1 );
		}
		
		parent.slideUp(100);
		
		return false;
	});
	
	/* handling category filtering change */
	
	$('#snippets-filter-by').change( function() {
		if( $('body').hasClass('favs') ){
			$('div.item-list-tabs li.selected').addClass('loading');
			
			jQuery.cookie('bp-snippets-fav-oldestpage', 1, {path: '/'} );
		
			var data = {
			     action:'snippet_fav_cat',
			     snptcat: $('#snippets-filter-by').val()
			 };
			
			if( $('body').hasClass('my-account') )
				parent = $('.snippet');
			else parent = $('#snippets-dir-list');
			
			parent.html("");
			
			 $.post(ajaxurl, data, function(response) {
				$('div.item-list-tabs li.selected').removeClass('loading');
				//$('#snippets-dir-list').html("");
		      	/*jQuery.cookie( 'bp-snippets-fav-oldestpage', oldest_page, {path: '/'} );*/
			    parent.html(response);
			 });
			
			
			return false;
		}
		else $('#snippet-form-filter').submit();
	});
	
	
	/* if snippets are filtered.. let's add the cat to the seach */
	$('#snippets_search_submit').click( function() {
		if( $('#snippets-filter-by').val() != "all" ){
			$('#search-snippets-cat').attr('disabled', false);
			$('#search-snippets-cat').val( $('#snippets-filter-by').val() );
		} else {
			$('#search-snippets-cat').attr('disabled', 'disabled');
		}
	})
	
	if( $('body').hasClass('favs') ){
		if( $('#search-snippets-form').length )
			$('#search-snippets-form').hide();
	}
	
	$('.copy-code').click( function () {
		
		$(this).parent().find('.copy-link-snippet').hide();
		$(this).parent().find('.copy-shortcode-snippet').hide();
		$(this).parent().find('.copy-embed-snippet').slideToggle();
		
		iframe = $(this).parent().find('.copy-embed-snippet textarea').val();
		iframe = fix_quotes_element( iframe );
		$(this).parent().find('.copy-embed-snippet textarea').val(iframe);
		
		$(this).parent().find('.snipt-code').focus();
		return false;
	});
	
	function fix_quotes_element( val ) {
		val = val.replace(/″/g,'"');
		val = val.replace(/“/g,'"');
		val = val.replace(/”/g,'"');
		return val;
	}
	
	$('.copy-link').click( function () {
		
		$(this).parent().find('.copy-shortcode-snippet').hide();
		$(this).parent().find('.copy-embed-snippet').hide();
		$(this).parent().find('.copy-link-snippet').slideToggle();
		$(this).parent().find('.snipt-perma').focus();
		return false;
	});
	
	$('.copy-shortcode').click( function () {
		
		$(this).parent().find('.copy-embed-snippet').hide();
		$(this).parent().find('.copy-link-snippet').hide();
		$(this).parent().find('.copy-shortcode-snippet').slideToggle();
		
		shortcode = $(this).parent().find('.copy-shortcode-snippet .snipt-shortcode').val();
		shortcode = fix_quotes_element( shortcode );
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

/**** Ajax ****/

function bp_cs_filter_language_for_group(groupid){
	var data = {
      action: 'bp_cs_filter_language_dd',
      group: groupid
    };

    jQuery.post(ajaxurl, data, function(response) {
		
		if(response.indexOf('{') != -1) {
			
			cs_available = jQuery.parseJSON(response);
			
			jQuery('#bp_cs_source option').each(function() {
				
				if(jQuery(this).val() != 0) jQuery(this).remove();
				
			});
			
			jQuery.each(cs_available, function(label, val) {
				
			    jQuery('#bp_cs_source').append(
			        jQuery('<option></option>').val(val).html(label)
			    );
			
			});
			
		}
      
    });
}

function bp_cs_add_snippet_fav(snippet_id, fav_nonce, type, eleclass){
	var current = eleclass;
	
	if(type == "fav") untype = 'unfav';
	if(type == "unfav") untype = 'fav';
	
	var data = {
      action: 'snippet_mark_'+type,
      snippet: snippet_id,
	  nonce: fav_nonce
    };

    jQuery.post(ajaxurl, data, function(response) {
		var snipt_fav = jQuery.parseJSON(response);
		current.removeClass('loading');
		current.html(snipt_fav['label']);
		current.attr('href', snipt_fav['url']);
		current.attr('title', snipt_fav['title']);
		current.removeClass('snippet-'+type);
		current.addClass('snippet-'+untype);
    });
}

function bp_cs_fav_load_more(eleclass){
	if ( null == jQuery.cookie('bp-snippets-fav-oldestpage') )
        jQuery.cookie('bp-snippets-fav-oldestpage', 1, {path: '/'} );

    var oldest_page = ( jQuery.cookie('bp-snippets-fav-oldestpage') * 1 ) + 1;
	
	eleclass.addClass('loading');
	
	var data = {
      action:'snippet_fav_loadmore',
      acpage: oldest_page,
	  snptcat:jQuery('#snippets-filter-by').val()
    };

    jQuery.post(ajaxurl, data, function(response) {
		eleclass.removeClass('loading');
        jQuery.cookie( 'bp-snippets-fav-oldestpage', oldest_page, {path: '/'} );
        jQuery("#content ul.snippet-list").append(response);
		eleclass.parent().hide();
    });
}

function bp_cs_del_snippet_comment(comment_id, snippet_id, com_nonce, eleclass){
	var current = eleclass;
	
	var data = {
      action: 'snippet_delete_comment',
      snippet_comment: comment_id,
	  snippet: snippet_id,
	  nonce: com_nonce
    };

    jQuery.post(ajaxurl, data, function(response) {
		current.removeClass('loading');
		if(response !=1 ){
			alert(response);
		}
    });
}