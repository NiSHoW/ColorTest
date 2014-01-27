/* 
 * Main namespace for colors framework based on esaeljs
 * @author Nisho
 */

var colors = { REVISION: '1' };

/**
 * console replacement if not defined
 * @type @exp;self@pro;console
 */
self.console = self.console || {
    info: function () {},
    log: function () {},
    debug: function () {},
    warn: function () {},
    error: function () {}
};

(function() {        
    
    
    colors._id = 0;
    
    colors.generateID = function(){
        colors.id++;
        return colors._id;
    }
        
    /**
     * Extend an object 
     * @param {type} obj
     * @param {type} source
     * @returns {colors.extend.obj|Array.extend.obj}
     */
    colors.extend = function(obj, source) {
        // ECMAScript5 compatibility based on: http://www.nczonline.net/blog/2012/12/11/are-your-mixins-ecmascript-5-compatible/
        if (Object.keys) {
            var keys = Object.keys(source);
            for (var i = 0, il = keys.length; i < il; i++) {
                var prop = keys[i];
                Object.defineProperty(obj, prop, Object.getOwnPropertyDescriptor(source, prop));
            }
        } else {
            var safeHasOwnProperty = {}.hasOwnProperty;
            for (var prop in source) {
                if (safeHasOwnProperty.call(source, prop)) {
                    obj[prop] = source[prop];
                }
            }
        }
        return obj;
    };    
    
    
    /**
     * Array extension
     */
    Array.prototype.rotate = (function() {
        // save references to array functions to make lookup faster
        var push = Array.prototype.push,
            splice = Array.prototype.splice;

        return function(count) {
            var len = this.length >>> 0, // convert to uint
                count = count >> 0; // convert to int

            // convert count to value in range [0, len[
            count = ((count % len) + len) % len;

            // use splice.call() instead of this.splice() to make function generic
            push.apply(this, splice.call(this, 0, count));
            return this;
        };
    
    })();
    
    /**
     * Extension trim for String
     * @returns {String.prototype@call;replace}
     */
    String.prototype.trim = String.prototype.trim || function () {
	return this.replace( /^\s+|\s+$/g, '' );
    };
    
}());