jQuery(function($){
    $('#start_date,#end_date').datepicker({
        dateFormat : 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        maxDate: new Date()
    });
     checkStats($);
    $('#view_stats').click(function(){
         checkStats($);
    });
});

function checkStats($){
    data = {
            action : 'overview',
            start_date : $('#start_date').val(),
            end_date : $('#end_date').val()
        };
         $('#result').html('<div class="loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');
		 
        $.get(ajaxurl , data , function(response){
				 
                $('#result').html(response);
            })
}