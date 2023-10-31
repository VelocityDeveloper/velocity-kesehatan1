jQuery(function($) {
    if($('.entry-content table').length > 0){
        $('.entry-content table').each(function(i, obj) {
            $(this).addClass('table');
            $(this).after( "<div class='table-reponsive table"+i+"'></div>" );
            $(this).appendTo(".table"+i+"");
            $(this).find('thead').addClass('table-dark');
        });
    }
});