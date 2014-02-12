/**
 * $.pinger
 * 
 * If your page runs into an iframe hosted by another domain, you may want to keep the session open.
 * This plugin automates the "ping URL" process and provides some options.
 * 
 * The pinger will ask the given URL every 'interval' minutes if it detects
 * some activity by listening to the events listed in 'listen' parameter.
 * 
 * Have a look to the 'defaults' variable below for further details about available parameters and default values.
 * 
 * Example:
 * Ping Google Logo every 5 minutes and launch the first ping right now:
 * 	$.pinger({
 * 		interval: 5
 * 		url: "http://www.google.co.uk/images/logos/ps_logo2.png",
 * 		pingNow: true
 * 	});
 * 
 * Initialize pinger without listening to events. Update activity on demand.
 * 	$.pinger({
 * 		url: "http://www.google.co.uk/images/logos/ps_logo2.png",
 * 		listen: null
 * 	});
 * 	...
 * 	$.pinger.now('manual ping');
 */
(function($){
 
    var defaults = {
        interval: 10, // pings the given URL every 'interval' MINUTES. Set to 0 for manual ping only
        url: null, // the URL to ping
        listen: ["click", "keydown"], // events to listen for updating activity
        pingNow: false,	// If true, sends a ping request just after init
        beforeSend: null, // Callback function, called before ping (should return true. false will cancels ping query)
        callback: false, // Callback function, called after ping query callback received
        maxTime: 0
    };
	
    var options = {};
    var startTime, lastUpdate, checkInterval, iTime, pingImg, _maxTime, _pingerLogs = true;
	
    /* Public methods */
    var methods = {
        init: function( settings ) {
            startTime = (new Date()).getTime();
            _maxTime = +(options.maxTime * 60 * 1000);	
            options = $.extend(true, defaults, settings);
 
            if (!options.url) {
                $.error( 'jQuery.pinger: url parameter is mandatory');
                return;
            }
            
            if ( options.interval > 0 ) {
                lastUpdate = 0;
                iTime = (options.interval * 60 * 1000);			
                checkInterval = setInterval( function(){
                    if (_maxTime > 0 && startTime + _maxTime < (new Date()).getTime() ) {
                        stop('timeout');
                    } else {
                        ping('interval');
                    }
                }, iTime);

                if (options.listen && $.isArray(options.listen) && options.listen.length > 0) { 
                    $(document).bind(options.listen.join('.pinger '), function(event) {
                        update(event.type);
                    });	
                }

                if (options.pingNow) {
                    ping('init');
                }
            }
        },
        
        
        /*
         * $.pinger.now(param)
         * Manual activity update
         * param : some message to log
         */
        now: function (param) {
            ( options.interval && options.interval > 0 ) ? update(param) : ping(param);
        },
        
        /*
         * $.pinger.destroy();
         * destroy pinger
         */
        destroy: function() {
            stop('destroy');
        }
    };
 
    /* Private Methods */
    function update(param) {
        lastUpdate = (new Date()).getTime();
    }
	
    function ping(param) {        
        if (!options.beforeSend || options.beforeSend.apply(this, arguments)) {
            $.post(options.url + "?" + (new Date().getTime()), function(){
                if (options.callback) {
                    options.callback.apply(this, arguments);
                }
            });
        }
    }
	
    function stop(param) {
        if (options.listen && $.isArray(options.listen) && options.listen.length > 0) {
            $(document).unbind(options.listen.join('.pinger '));
        }
        clearInterval(checkInterval);
    }
	
    /* Plugin entry point */
    $.pinger = function( method ) {
        // Method calling logic
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call(arguments, 1));
        } else if ( typeof method === 'object' || !method ) {
            return methods.init.apply(this, arguments );
        } else {
            $.error( 'Method ' + method + ' does not exist on jQuery.pinger');
            return this;
        }
    };
})(jQuery);