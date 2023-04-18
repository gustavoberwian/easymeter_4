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

}).apply(this, [jQuery]);