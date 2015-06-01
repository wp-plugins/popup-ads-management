(function($) {    
    
    $(".msbd-popadsm").on("click", ".handlediv", function(e){       
        e.preventDefault();        
        $(this).parent().toggleClass("closed");
    });
    
}(jQuery));
