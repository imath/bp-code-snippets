jQuery(document).ready(function($){
	var old_text;
	$('.insertSnippet').click(function(){
		var sniptid = $(this).attr("id");
		theid = sniptid.split("-");
		
		if( $("#comment_content", top.document).length )
			var ta = "comment_content";

		if( $("#topic_text", top.document).length )
			ta = "topic_text";
			
		if( $("#reply_text", top.document).length )
			ta = "reply_text";
		
		if( $('textarea#content', top.document).length ){
			
			if( $("#blogpost_snippet_ids", top.document).length )
				$("#blogpost_snippet_ids", top.document).val($("#blogpost_snippet_ids", top.document).val()+theid[1]+',');
				
			var win = window.dialogArguments || opener || parent || top;
			win.send_to_editor('[snippet id="'+theid[1]+'"]');
			
			return false;
		}
			
	
		$("#"+ta, top.document).val($("#"+ta, top.document).val()+'[snippet id="'+theid[1]+'"]');
		
		if( $("#topic_new_snippet_ids", top.document).length )
			$("#topic_new_snippet_ids", top.document).val($("#topic_new_snippet_ids", top.document).val()+theid[1]+',');
			
		if( $("#comment_new_snippet_ids", top.document).length )
			$("#comment_new_snippet_ids", top.document).val($("#comment_new_snippet_ids", top.document).val()+theid[1]+',');
		
		self.parent.tb_remove();
		return false;
	});
	
	$('#submit_snippet_cancel').click(function(){
		self.parent.tb_remove();
		return false;
	});
	
	$('#purpose_action').click(function(){
		if( !old_text ) old_text = $('#snippet_purpose').val();
		$('#snippet_purpose').slideToggle();
	});
	
	$('#snippet_purpose').focus( function(){
		$('#snippet_purpose').val("");
	});
	
	$('#snippet_purpose').blur( function(){
		if( $('#snippet_purpose').val().length == 0 )
			$('#snippet_purpose').val(old_text);
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
});