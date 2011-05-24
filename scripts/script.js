/*  ------------------------------------
	Globals
------------------------------------- */


/*  ------------------------------------
	On Load
------------------------------------- */
$(document).ready(function(){
	
	// png fix
	DD_belatedPNG.fix('.png');
	
	// setup sortable tables
	
	
	
	$('#file_upload').uploadify({
	    'uploader' 	 		: 'scripts/uploadify/uploadify.swf',
	    'script'   			: 'scripts/uploadify/uploadify.php',
	    'cancelImg' 		: 'scripts/uploadify/cancel.png',
	    'folder'    		: '/yayforimages/uploads/',
	    'auto'   	   		: false, 
	    'fileExt'	   		: '*.jpg;*.gif;*.png',
	    'multi'				: true,
	    'fileDataName' 		: 'fileArr',
	    'expressInstall' : 'scripts/uploadify/expressInstall.swf',
	    'onError': function (event, queueID ,fileObj, errorObj) {
	    	alert(errorObj.info);
	    },
	    'onComplete'  : function(event, ID, fileObj, response, data) {
     		 alert(response);
	    }
	    
	  });
	
});

	  




/*  ------------------------------------
	Notes
----------------------------------------

Init tablesorter:

	$('.name of table class').tablesorter({ widgets: ['zebra'] });

	
Create a qtip

	$('.qtip').qtip({
		content: 	'qtip content',
		show: 		'mouseover', hide: 'mouseout',
		style: 		{ name: 'cream', tip: true, 'font-family': '"Lucida Grande", "Lucida Fax", Arial', 'font-size': '12px' },
		position: 	{ corner: { target: 'bottomRight', tooltip: 'topLeft' } }
		exclusive:	true,
	});	


Create a dynamic qtip:

	$('.qtip').each(function(){
		this_content = $(this).parents().find('.target_content').html();	
		$(this).qtip({ ... });
	});


Do something ajaxy:

	$.ajax({
		type:		"GET",
		url: 		"actions/some_action.php",
		data: 		{ q: var },
		success: 	function(data){ ... }
	});
	

If the user clicks anywhere and it's not this thing, hide this thing

	$(document).bind('click', function(e) {
		if ( ! $(e.target).hasClass("#this_thing_container") ){ $('#this_thing').hide(); }
	});
	
------------------------------------- */