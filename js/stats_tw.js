jQuery(function($){

	$('#twitter_fetch').click(function(){
		$('#result').html('<span class="loader"></span>');
		
		var data = { 'action': 'fetch_twitter_data',};
	$.post(ajaxurl, data, function(response) {
		$('#result').html(response);		var aAsc = [];		function sortTable(e) {		console.log(e);		var nr = $(e.currentTarget).index();		aAsc[nr] = aAsc[nr]=='asc'?'desc':'asc';		$(this).attr('class' , aAsc[nr]);		$('.widefat>tbody>tr').tsort('td:eq('+nr+')[abbr]',{order:aAsc[nr]});		}		$('.widefat thead th').on('click',sortTable);
			});
	});
	 
});

function showTweets(url){
url = encodeURIComponent(url);
var twt_url = "https://twitter.com/search?q="+url+"&src=typd&f=realtime";
window.open(twt_url,'_blank');
}