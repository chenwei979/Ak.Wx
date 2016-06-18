

$(function(){

    /*图片轮播初始化*/
    try{
        var unslider = $('.swiper-container').unslider({
            speed: 500,
            delay: 5000,
            dots: false,
            fluid: false,
            complete: function(){
                $('.swiper-index .active-num i').html(data.i + 1);
            }
        });
        var data = unslider.data('unslider');
        $('.swiper-index .active-num i').html(data.i + 1);
        $('.swiper-index .all-num').html('/0' + data.max.length);
        $('.unslider-arrow').click(function() {
            var fn = this.className.split(' ')[1];
            unslider.data('unslider')[fn]();
        });
    }catch(e){

    }

    //var swiper = new Swiper('.swiper-container', {
    //    onInit: function(swiper){
    //        $('.swiper-index .active-num i').html(swiper.activeIndex+1);
    //        $('.swiper-index .all-num').html('/0' + swiper.slides.length);
    //    },
    //    pagination: '.swiper-pagination',
    //    paginationClickable: '.swiper-pagination',
    //    nextButton: '.swiper-button-next',
    //    prevButton: '.swiper-button-prev',
    //    spaceBetween: 0,
    //    onSlideChangeEnd: function(swiper){
    //        $('.swiper-index .active-num i').html(swiper.activeIndex+1);
    //    }
    //});
    /*经典案例导航*/
    $('.case-nav .d-menu a').on('click', function(){
        $('.case-nav .d-menu a').removeClass('current');
        $(this).addClass('current');
    })

})
