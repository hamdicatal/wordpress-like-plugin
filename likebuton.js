jQuery(document).ready(function($){
	
	jQuery("#Begenbutonu").click(function(){
	
		var yaziid = $(this).attr('yaziid');
		var path = window.location.hostname;
		 var posting = $.post("http://" + path + "/wp-content/plugins/deneme/like.php", { id: yaziid} );
		 
		
		 posting.done(function( data ) {
			/*var content = $( data ).find( "#content" );
			$( "#result" ).empty().append( content );*/
			jQuery("#Begenbutonu").html(data);
		});
	
	});
	
});

