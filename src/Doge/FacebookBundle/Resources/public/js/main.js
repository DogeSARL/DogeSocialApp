$(document).ready(function(){
  $('.slide_home').bxSlider({
  	mode: 'fade',
  	pager: false,
  	adaptiveHeight: false,
  	controls: false,
  	auto: true
  });
});


$('.openpop').click(function(e){
    e.preventDefault();
    var div = $(this).attr('href');
    $('.popup_participation').each(function(){
        $(this).removeClass('show');
    });
    $(div).addClass('show');
    $('.popup_participation .close').click(function(f){
        f.preventDefault();
        $('.show').removeClass('show');
    })
});