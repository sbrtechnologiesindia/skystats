$=jQuery;
jQuery(function($){
   
  
	doFreqStuff();
    checkStats($);
    $('#analytics_fetch').click(function(){
		
		checkStats($);
        
       
    });
	 
});

jQuery(function($) {
    $('#frequency').change(function(){
		doFreqStuff();
	});	
	
});

function doFreqStuff(){
		$('#to').datepicker( "destroy" );
		$('#from').datepicker( "destroy" );
		
		if($('#frequency').val() == 'monthly'){
		$( "#to ,#from" ).val("");
			$('body').append('<div style="display:none" id="styleDiv"><style>.ui-datepicker-calendar {display: none;}</style></div>');
			var today = new Date();
			$('#to ,#from').datepicker( {
				
				changeMonth: true,
				changeYear: true, 
				dateFormat : 'yy-mm-dd',
			 
			});
			$( "#to ,#from" ).datepicker( "option", "showButtonPanel", true );
			$( "#to ,#from" ).datepicker( "option", "maxDate",  new Date(today.getFullYear(), today.getMonth() + 1 , 0) );
			$( "#from" ).datepicker( "option", "onClose", function(dateText, inst) { 
					
					var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
					var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
					$(this).datepicker('setDate', new Date(year, month, 1));
				} );
			$( "#to" ).datepicker( "option", "onClose", function(dateText, inst) { 
					
					var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
					 month++;
					var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
					int_d = new Date(year, month, 1);
					
					d = new Date(int_d - 1);
					$(this).datepicker('setDate', d);
				} );
				 
		} else{
				$('#styleDiv').remove();
				
				$('#to ,#from').datepicker({
					dateFormat : 'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					maxDate : new Date()
					
				});
			
				 
		}
	
	}

function checkStats($){
     var from = $('#from').val(); 
        var to = $('#to').val(); 
        var frequency = $('#frequency').val(); 
        if (!dtParse(from)) {
            alert("Enter valid from date!");
            return false;
        }		
       
		if (!dtParse(to)) {
            alert("Enter valid from date.");
            return false;
        }
		
        if(from == ''){
            alert("Please select From date.");
            return false;
        } else if(to == ''){
            alert("Please select To date.");
            return false; 
        }
		
		if(frequency == 'monthly'){
			var diff = dtDiff(from,to);
			if(diff < 32){
				alert("Please pick at-least two months to draw the monthly graph.");
            return false; 
			}
		}
		 $('#result').html('<div class="loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');
        var data = {
            'action': 'fetch_analytics_data',
           
            'from': from ,
            'to': to ,
			'frequency': frequency
        };
        $.post(ajaxurl, data, function(response) {
            $('#result').html(response);
           
        });
}




function dtParse(dt) {
    var s = dt.split('-');


    if ((typeof s[0] == 'undefined') || (typeof s[1] == 'undefined') || (typeof s[2] == 'undefined')) {
        return false;
    } else {
        s[0] = parseInt(s[0]);
        s[1] = parseInt(s[1]);
        s[2] = parseInt(s[2]);
        if ((s[0] == 0) || (s[1] == 0) || (s[2] == 0)) {
            return false;
        }
        var ret = new Date(s[0], s[1] - 1, s[2]);
        return !isNaN(ret.getTime());
    }
}

function dtDiff(f, u) {
    var s = f.split('-');
    var t = u.split('-');


    if ((typeof s[0] == 'undefined') || (typeof s[1] == 'undefined') || (typeof s[2] == 'undefined') || (typeof t[0] == 'undefined') || (typeof t[1] == 'undefined') || (typeof t[2] == 'undefined')) {
        return 0;
    } else {
        s[0] = parseInt(s[0]);
        s[1] = parseInt(s[1]);
        s[2] = parseInt(s[2]);
        t[0] = parseInt(t[0]);
        t[1] = parseInt(t[1]);
        t[2] = parseInt(t[2]);

        if ((s[0] == 0) || (s[1] == 0) || (s[2] == 0) || (t[0] == 0) || (t[1] == 0) || (t[2] == 0)) {
            return 0;
        }
        var d1 = new Date(s[0], s[1] - 1, s[2]);
        var d2 = new Date(t[0], t[1] - 1, t[2]);
        if (isNaN(d1.getTime()) || isNaN(d2.getTime())) {
            return 0;

        }
        var diff = (d2.getTime() - d1.getTime()) / 1000 / 60 / 60 / 24;

        return diff;
    }
}