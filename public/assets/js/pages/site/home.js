(function() {

    $(document).ready(function(){
        $(window).resize()
        });

    $(window).resize(function(){
        var width = $(window).width();
        if(width <= 992){
            $('.contats-item').removeClass('col-sm-4').addClass('row-sm-4');
            $('.contats').removeClass('row').addClass('column');
        }
        else{
            $('.contats-item').removeClass('row-sm-4').addClass('col-sm-4');
            $('.contats').removeClass('column').addClass('row');
        }
     })

    $(".img-container1").mouseover(function () {
        $('.img-hov1').attr('src', $('.img-hov1').data("hover"));
      }).mouseout(function () {
        $('.img-hov1').attr('src', $('.img-hov1').data("src"));
      });

    $(".img-container2").mouseover(function () {
        $('.img-hov2').attr('src', $('.img-hov2').data("hover"));
      }).mouseout(function () {
        $('.img-hov2').attr('src', $('.img-hov2').data("src"));
      });

    $(".img-container3").mouseover(function () {
        $('.img-hov3').attr('src', $('.img-hov3').data("hover"));
      }).mouseout(function () {
        $('.img-hov3').attr('src', $('.img-hov3').data("src"));
      });

    $(".img-container4").mouseover(function () {
        $('.img-hov4').attr('src', $('.img-hov4').data("hover"));
      }).mouseout(function () {
        $('.img-hov4').attr('src', $('.img-hov4').data("src"));
      });


}).apply(this, [jQuery]);