(function($) {

    jQuery.cookie = function (key, value, options) {

        // key and at least value given, set cookie...
        if (arguments.length > 1 && String(value) !== "[object Object]") {
            options = jQuery.extend({}, options);

            if (value === null || value === undefined) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var ms = options.expires;
                var t = options.expires = new Date();
                t.setTime(t.getTime() + (ms * 60 * 1000));
            }

            value = String(value);

            return (document.cookie = [
                encodeURIComponent(key), 
                '=',
                options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        }

        // key and possibly options given, get cookie...
        options = value || {};
        
        var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
        
        return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
    };
    
    
    


        //var expMinutes = 10; //defined in php file to declare before this file using admin input    
        //var holdingSeconds = 1; //defined in php file to declare before this file using admin input    
        
        if($.cookie('popup_user_login') != 'yes') {
            $('#msbd-popup').delay(holdingSeconds).fadeIn('medium');
            
            $('.hide-me').click(function(e) {
                e.preventDefault();
                $('#msbd-popup').fadeOut('medium');
            });
        }    

        $.cookie('popup_user_login', 'yes', { path: '/', expires: expMinutes });
    
    
    
}(jQuery));



