jQuery(function($){
 
   $('#to ,#from').datepicker({
        dateFormat : 'yy-mm-dd',
        maxDate: new Date()
    });
   checkStats($);
   $('#analytics_fetch').click(function(){
		checkStats($)
        });
		
		
});

function checkStats($){
    var accountId = $('#analytics_account_list').val(); 
    var from = $('#from').val(); 
    var to = $('#to').val(); 
		
    if(accountId == ''){
        alert("Please select an account to fetch for");
        return false;
    } else if(from == ''){
        alert("Please From date");
        return false;
    } else if(to == ''){
        alert("Please select To date");
        return false; 
    }
		
		
    var data = {
        'action': 'fetch_analytics_data',
        'accountId': accountId ,
        'from': from ,
        'to': to 
    };
    $.post(ajaxurl, data, function(response) {
        alert('Got this from the server: ' + response);
    });
}

