jQuery(function($){

    $('#fb_fetch').click(function(){
        $('#result').html('<span class="loader"></span>');
		
        var data = {
            'action': 'fetch_fb_data'
        };
        $.post(ajaxurl, data, function(response) {
            $('#result').html(response);
            
        });
    });
	 
});