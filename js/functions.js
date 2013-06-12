(function($) {
    // $() will work as an alias for jQuery() inside of this function
    var aparecer = function(){
		$('.image-attachment').hide().fadeIn('slow');
	}
	setTimeout(aparecer,0);
})(jQuery);

jQuery(document).ready(function($) {
    // $() will work as an alias for jQuery() inside of this function
    $('li.gallery-item').click(function(e){
    	e.preventDefault();
    	console.log('Hago click');
    	var url = $(this).children().attr('href');
    	console.log(url);
    	window.open(url, '_blank', 'width='+screen.width+',height='+screen.height);
    });
});