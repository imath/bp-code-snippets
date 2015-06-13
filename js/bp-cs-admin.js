jQuery(document).ready(function($){
	
	$('a.bpcs-visu').hover(
	  function () {
		target = $(this).attr('href');
		$(target).addClass('redborder');
	  }, 
	  function () {
		target = $(this).attr('href');
	    $(target).removeClass('redborder');
	  });
	
	$('a.bpcs-visu').click( function(){return false;});
	
	$('#snippet_settings a.bpcs-help').click(function(){
		
		// let's click on help link !	
		if( $('#screen-meta').css('display', 'none') ) 
			$('#contextual-help-link').trigger('click');
		
		var link = $(this).attr('href'),
			panel, li;

		// Links
		$('.contextual-help-tabs .active').removeClass('active');
		li = link.replace('panel','link');
		$(li).addClass('active');
		
		// Panels
		panel = $( link );
		$('.help-tab-content').not( panel ).removeClass('active').hide();
		panel.addClass('active').show();
		
		return false;
	});
	
	$('#cs-enable-no').click(function(){
		if( $('#cs-ef-enable-yes').attr('checked', 'true') ) {
			alert(nogroup);
			$('#cs-ef-enable-no').attr('checked','true');
			$('input[type=radio][name=cs-ef-enable]').attr('disabled', true);
		}
	});
	
	$('#cs-enable-yes').click(function(){
		$('input[type=radio][name=cs-ef-enable]').attr('disabled',false);
	});
	
	$('#cs-oembed-yes').click(function(){
		$('input[type=radio][name=cs-iframe-activity]').attr('disabled',false);
	});
	
	$('#cs-oembed-no').click(function(){
		if( $('#cs-iframe-activity-yes').attr('checked', 'true') ) {
			alert(noembed);
			$('#cs-iframe-activity-no').attr('checked','true');
			$('input[type=radio][name=cs-iframe-activity]').attr('disabled', true);
		}
	});
	
	if( !$('#cs-enable-yes').attr('checked') )
		$('input[type=radio][name=cs-ef-enable]').attr('disabled', true);
		
	if( !$('#cs-oembed-yes').attr('checked') )
		$('input[type=radio][name=cs-iframe-activity]').attr('disabled', true);
	
});