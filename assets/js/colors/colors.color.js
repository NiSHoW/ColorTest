/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// namespace:
this.colors = this.colors || {};

(function() {

    /**
     * Color class
     * @returns {undefined}
     */
    var Color = function(lab, id) {
        this.id = id;
        this.lab = {L: lab.L, a:lab.a, b:lab.b};
        this.rgb = {r: 0, g:0, b:0};
        this.xyz = {x: 0, y:0, z:0};
        this.labMid = {L: lab.L, a:lab.a, b:lab.b};
        this._activeFilters = [];
        this._onColorChangeListeners = [];
        this.convert(this.convertor.CielabToRGB);
        this.generateGrayscaleColor();
    };    
        
    Color.prototype.debug = false;
        
    /**
     * Cielab Variable
     */
    Color.prototype.labMid = {
        L: 0,
        a: 0,
        b: 0
    };    

    /**
     * Cielab Variable
     */
    Color.prototype.labMin = {
        L: 0,
        a: 0,
        b: 0
    };        
    
    
    /**
     * Cielab Variable
     */
    Color.prototype.labMax = {
        L: 0,
        a: 0,
        b: 0
    };    
        
        
    Color.prototype.active = false;
    
    Color.prototype.inactiveColor = {
        r: 0,
        g: 0,
        b: 0
    };

    Color.prototype.final = false;
    
    Color.prototype.generateGrayscaleColor = function(){
        //var gray = (this.rgb.r + this.rgb.g + this.rgb.b) / 3; //avarage        
        var gray = (this.rgb.r * 0.299 + this.rgb.g * 0.587 + this.rgb.b * 0.114); //luma
        this.inactiveColor = {
            r: Math.round(gray),
            g: Math.round(gray),
            b: Math.round(gray)
        };
    };
    
    /**
     * Return css rgb string
     * @returns {String}
     */
    Color.prototype.cssRGB = function() {
        if(this.active){
            return "rgb("+
                Math.min(this.rgb.r, 255)+","+
                Math.min(this.rgb.g, 255)+","+
                Math.min(this.rgb.b, 255)+
            ")";
        }
        
        return "rgb("+
            Math.min(this.inactiveColor.r, 255)+","+
            Math.min(this.inactiveColor.g, 255)+","+
            Math.min(this.inactiveColor.b, 255)+
        ")";
    };    
        
    /**
     * return hex rapresentation of rgb
     * @returns {String}
     */
    Color.prototype.cssHEX = function() {
        if(this.active){
            return "#" + 
                (255*this.rgb.r < 16 ? "0" : "") + Math.round(255*this.rgb.r).toString(16) +
                (255*this.rgb.g < 16 ? "0" : "") + Math.round(255*this.rgb.g).toString(16) + 
                (255*this.rgb.b < 16 ? "0" : "") + Math.round(255*this.rgb.b).toString(16);
        }
        
        return "#" + 
                (255*this.inactiveColor.r < 16 ? "0" : "") + Math.round(255*this.inactiveColor.r).toString(16) +
                (255*this.inactiveColor.g < 16 ? "0" : "") + Math.round(255*this.inactiveColor.g).toString(16) + 
                (255*this.inactiveColor.b < 16 ? "0" : "") + Math.round(255*this.inactiveColor.b).toString(16);
    }
    
    
    /**
     * Get alpha channel
     * @returns {Number}
     */
    Color.prototype.getAlphaChannel = function() { 
        return this.alpha; 
    };    
    
    /** 
     * Debug 
     */
    Color.prototype.toString = function() {
        var H = 0, C = 0;
        if(this.debug){
            C = Math.sqrt(Math.pow(this.lab.a, 2)+ Math.pow(this.lab.b, 2));
            if(this.lab.a < 0 && this.lab.b > 0 ){
               H = 180 + Math.atan(this.lab.b/this.lab.a) * 180 / Math.PI;
            } else if( this.lab.a < 0 && this.lab.b < 0 ){
               H = 180+Math.atan(this.lab.b/this.lab.a)*180/Math.PI;
            }else if( this.lab.a > 0 && this.lab.b < 0){ 
               H = 360 + Math.atan(this.lab.b/this.lab.a) * 180/Math.PI;
            }else {
               H = Math.atan(this.lab.b/this.lab.a) *180 / Math.PI;
            }
            
            return "L:"+(parseFloat(this.lab.L).toFixed(2))+", "+
               "a:"+(parseFloat(this.lab.a).toFixed(2))+", "+
               "b:"+(parseFloat(this.lab.b).toFixed(2))+", "+
               "C:"+(parseFloat(C).toFixed(2))+", "+
               "H:"+(parseFloat(H).toFixed(2));

        }
        
        return "L:"+(parseFloat(this.lab.L).toFixed(2))+", "+
               "a:"+(parseFloat(this.lab.a).toFixed(2))+", "+
               "b:"+(parseFloat(this.lab.b).toFixed(2));        
        
    };

    /**
     * Convert formats
     * @param {type} method
     * @returns {undefined}
     */
    Color.prototype.convert = function(method) {        
        method.apply(this);            
    };    

    /**
     * Aplitude of segments
     */
    Color.prototype.addOnColorChangeListeners = function(listener){
        if(listener.hasOwnProperty("onColorChange"))
            this._onColorChangeListeners.push(listener);
    };
    
    Color.prototype.fireOnColorChange= function(){        
        for(var i = 0; i < this._onColorChangeListeners.length; i++){
            if(this._onColorChangeListeners[i].hasOwnProperty("onColorChange"))
                this._onColorChangeListeners[i].onColorChange(this);
        }
    };    
    
    
    /**
     * Activate filter for an object
     * @param {type} filter
     * @returns {undefined}
     */
    Color.prototype.addFilter = function(filter){     
        this._activeFilters.push(filter);
        if(filter.init){
            filter.init.apply(this);
        }
        
    };

    /**
     * Remove filter
     * @param {type} filter
     * @returns {undefined}
     */
    Color.prototype.removeFilter = function(filter){
        var index = createjs.indexOf(this._activeFilters, filter);
        if( index !== -1){
            if(this._activeFilters[index].unload){
                this._activeFilters[index].unload.apply(this);
            }
            this._activeFilters.splice(index, 1);
        }
    };
    
    /**
     * Has filter
     * @param {type} filter
     * @returns {undefined}
     */
    Color.prototype.hasFilter = function(filter){
        if( createjs.indexOf(this._activeFilters, filter) !== -1){
            return true;
        }
        return false;
    };

    /**
     * Applica filtro
     * @param {type} method
     * @returns {undefined}
     */
    Color.prototype.applyFilter = function(functionName, value) {
        if(this.final) {
            console.warn("Final color can't be changed");    
            return;            
        }
        
        for(var i = 0; i < this._activeFilters.length; i++){
            if(this._activeFilters[i].hasOwnProperty(functionName)){
                var filter = this._activeFilters[i];
                return filter[functionName].apply(this, [value]);
            }
        }
        
        console.warn("No filter function present in active filtres");
    };
    

    /**
     * Saturation Filters
     */
    Color.Lighting = {
                        
        init: function(){},
        
        unload: function(){
            this.lab = {
                L: this.labMid.L,
                a: this.labMid.a,
                b: this.labMid.b
            };
            
            this.convert(this.convertor.CielabToRGB);
        },
        
        increaseLighting: function(value){
            //se ho raggiunto il valore massimo ritorno
            if(+(this.lab.L) + +(value) > 100){
                return;
            }
            
            // se va oltre 1 riporta a 1
            var Lris = +(this.lab.L) + +(value);
            this.lab.L = Math.max(0, +(Lris));
            this.convert(this.convertor.CielabToRGB);
        },
        
        decreaseLighting: function(value){

            //se ho raggiunto il valore massimo ritorno
            if(+(this.lab.L) - +(value) < 0){
                return;
            }
            
            // se va oltre 1 riporta a 1
            var Lris = +(this.lab.L) - +(value);
            this.lab.L = Math.max(0, +(Lris));
            this.convert(this.convertor.CielabToRGB);
        },
        
        setLighting: function(value){           
            
            this.lab.L = Math.max(0, Math.min(100, value));

            this.convert(this.convertor.CielabToRGB);
        }        
     
    }
    
    
    /**
     * Saturation Filters
     */
    Color.BlackWhite = {
        
        init: function(){
            this.alphaS = 1;
            this.alphaW = 1;     
            this.alphaBW = 0;     
        },
        
        unload: function(){
            delete this.alphaS;
            delete this.alphaW;
            this.lab = {
                L: this.labMid.L,
                a: this.labMid.a,
                b: this.labMid.b
            };
            
            this.convert(this.convertor.CielabToRGB);
        },    

        
        _setColor: function(){
            if(this.alphaBW >= 0){
                                
                var alpha = Math.abs(this.alphaBW);
		var Lris = (+(1 - alpha) * +(this.labMid.L)) + (alpha * +(this.labMax.L));
		var aris = (+(1 - alpha) * +(this.labMid.a)) + (alpha * +(this.labMax.a));
		var bris = (+(1 - alpha) * +(this.labMid.b)) + (alpha * +(this.labMax.b));
                
                this.lab = {
                    L: +(Lris),
                    a: +(aris),
                    b: +(bris)
                };
                
                this.convert(this.convertor.CielabToRGB);
            } else {
                var alpha = Math.abs(this.alphaBW);
                var Lris = (+(1 - alpha) * +(this.labMid.L)) + (alpha * +(this.labMin.L));  
                var aris = (+(1 - alpha) * +(this.labMid.a)) + (alpha * +(this.labMin.a));
                var bris = (+(1 - alpha) * +(this.labMid.b)) + (alpha * +(this.labMin.b));

                this.lab = {
                    L: +(Lris),
                    a: +(aris),
                    b: +(bris)
                };
                
                this.convert(this.convertor.CielabToRGB);                
            }           
        },
        
        
        increaseBlackWhite: function(value){            
            this.alphaBW = this.alphaBW + (value/100);
            if(this.alphaBW > 1) this.alphaBW = 1;
            Color.BlackWhite._setColor.call(this);
        },
        
        decreaseBlackWhite: function(value){
            this.alphaBW = this.alphaBW - (value/100);
            if(this.alphaBW < -1) this.alphaBW = -1;
            Color.BlackWhite._setColor.call(this);
        },
        
        setBlackWhite: function(value){           
            this.alphaBW = (value/100);
            if(this.alphaBW < -1) this.alphaBW = -1;
            if(this.alphaBW > 1) this.alphaBW = 1;
            Color.BlackWhite._setColor.call(this);
        }

    };

    /**
     * Expose filters to color class
     */
    Color.prototype.filters = {
        Lighting: Color.Lighting, 
        BlackWhite: Color.BlackWhite
    };
    

    /**
     * Function for converts colors
     */
    Color.Convertor = {

        monitor : {}, 
        
        calibrate: function(xR, yR, zR,
                            xG, yG, zG,
                            xB, yB, zB,
                            xW, yW, zW,
                            gamma_R, gamma_G, gamma_B ){
        
            //matrice originale
            var matrix = new colors.Matrix3();
            matrix.setAll([xR, xG, xB, yR, yG, yB, zR, zG ,zB]);            
            //matrice inversa
            var invers = matrix.clone();
            invers.invert();      
                            
            var WX = (xW / yW) * 100,
                WY = (yW / yW) * 100,
                WZ = (zW / yW) * 100;
                
            //matrice per la normalizzazione
            var mr = invers.a * WX + invers.b * WY + invers.c * WZ,
                mg = invers.d * WX + invers.e * WY + invers.f * WZ,
                mb = invers.g * WX + invers.h * WY + invers.i * WZ;
                
            //matrice per trasf. da RGB a XYZ
            matrix.setAll([
                matrix.a * mr,
                matrix.b * mg,
                matrix.c * mb,
                matrix.d * mr,
                matrix.e * mg,
                matrix.f * mb,
                matrix.g * mr,
                matrix.h * mg,
                matrix.i * mb               
            ]);
            
            //ricalcola il determiante normalizzato            
            //matrice  inversa per trasf. da XYZ a RGB 
            invers.copy(matrix).invert();
                
            Color.Convertor.monitor = {
                WX: WX,
                WY: WY,
                WZ: WZ,
                gamma_R: gamma_R,
                gamma_G: gamma_G,
                gamma_B: gamma_B,
                //matrix
                matrix : matrix,
                invers: invers                
            };            
        },        

        /**
         * Calculates HSL Color
         * RGB must be normalized
         * Must be executed in a Color object context
         * http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript 
         */
        RGBToHSL: function() {
            //     
            var r = this.rgb.r,
                g = this.rgb.g,
                b = this.rgb.b,
                max = Math.max(r, g, b), min = Math.min(r, g, b);        
            this.hsl.l = (max + min) / 2;    
            if(max == min){
                this.hsl.h = this.hsl.s = 0; // achromatic
            } else {
                var d = max - min;
                this.hsl.s = this.hsl.l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch(max){
                    case r: this.hsl.h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: this.hsl.h = (b - r) / d + 2; break;
                    case b: this.hsl.h = (r - g) / d + 4; break;
                }
                this.hsl.h /= 6;
            }
            
            this.fireOnColorChange();
        },

        /**
         * Calculates RGB color (nomalized)
         * HSL must be normalized
         * Must be executed in a Color object context
         * http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript
         */
        HSLToRGB: function() {
            var h = this.hsl.h,
                s = this.hsl.s,
                l = this.hsl.l,
                hue2rgb = function(p, q, t){
                    if(t < 0) t += 1;
                    if(t > 1) t -= 1;
                    if(t < 1/6) return p + (q - p) * 6 * t;
                    if(t < 1/2) return q;
                    if(t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                    return p;
                };
            if(s == 0) {
                this.rgb.r = this.rgb.g = this.rgb.b = l; // achromatic
            } else {
                var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                var p = 2 * l - q;
                this.rgb.r = hue2rgb(p, q, h + 1/3);
                this.rgb.g = hue2rgb(p, q, h);
                this.rgb.b = hue2rgb(p, q, h - 1/3);
            }
            
            this.fireOnColorChange();
        },
        
        /**
         * Convert cielab to xyz
         * @returns {_L20.Color.Convertor.xyz}
         */
        CielabToXYZ: function() {   
            
            var monitor = colors.Color.Convertor.monitor;            

            var Y = (+(this.lab.L) + 16) / 116;
            var X = Y + (+(this.lab.a) / 500);
            var Z = Y - (+(this.lab.b) / 200);

            if(X > (24 / 116)){
                X = monitor.WX *  Math.pow(X, 3);
            } else {
                X = monitor.WX * (X - 16 / 116) * (108 / 841);
            }

            if(Y > (24 / 116)){
                Y = monitor.WY * Math.pow(Y, 3);
            } else {
                Y = monitor.WY * (Y - 16 / 116) * 108 / 841;
            }

            if(Z > (24 / 116)){
                Z = monitor.WZ * Math.pow(Z, 3);
            } else {
                Z = monitor.WZ * (Z - 16 / 116) * (108 / 841);
            }

            //check range
            if( X > monitor.WX ) X = monitor.WX;
            if( X < 0 )  X = 0;
            if( Y > monitor.WY ) Y = monitor.WY;
            if( Y < 0 ) Y = 0;
            if( Z > monitor.WZ ) Z = monitor.WZ;
            if( Z < 0 ) Z = 0;                                               
            
            this.xyz = {
               x: +(X),
               y: +(Y),
               z: +(Z)
            };
            
            return this.xyz;
        },
        
        /**
         * Convert xyz to gbb
         * @returns {_L20.Color.Convertor.rgb}
         */
        XYZToRGB: function() {        

            var monitor = colors.Color.Convertor.monitor;
            
            var x = this.xyz.x;
            var y = this.xyz.y;
            var z = this.xyz.z;
            
            var R1 = +(x) * +(monitor.invers.a) + 
                     +(y) * +(monitor.invers.b) + 
                     +(z) * +(monitor.invers.c);
            var G1 = +(x) * +(monitor.invers.d) + 
                     +(y) * +(monitor.invers.e) + 
                     +(z) * +(monitor.invers.f);
            var B1 = +(x) * +(monitor.invers.g) + 
                     +(y) * +(monitor.invers.h) + 
                     +(z) * +(monitor.invers.i);
            
            if(R1 < 0) R1 = 0;
            if(G1 < 0) G1 = 0;
            if(B1 < 0 )B1 = 0;
            
            R1 = Math.round((Math.pow(R1,(1 / monitor.gamma_R))) * 255);
            G1 = Math.round((Math.pow(G1,(1 / monitor.gamma_G))) * 255);
            B1 = Math.round((Math.pow(B1,(1 / monitor.gamma_B))) * 255);            
            
            this.rgb.r = Math.min(Math.max(R1, 0), 255);
            this.rgb.g = Math.min(Math.max(G1, 0), 255);
            this.rgb.b = Math.min(Math.max(B1, 0), 255);
            
            this.fireOnColorChange();

            return this.rgb;

        },     
        
        /**
         * Convert cielab to rgb
         * @returns {_L20.Color.Convertor@pro;Convertor@call;XYZToRGB}
         */
        CielabToRGB: function(){
            this.convert(colors.Color.Convertor.CielabToXYZ);
            return this.convert(colors.Color.Convertor.XYZToRGB);
        }

    };
    
            
    (function(){            
            Color.Convertor.calibrate(
                0.64,   // xR
                0.33,   // yR
                0.03,   // zR
                0.30,   // xG
                0.60,   // yG
                0.1,    // zG
                0.15,   // xB
                0.06,   // yB
                0.79,   // zB
                0.3127, // xW
                0.329,  // yW
                0.3583, // zW
                2.2, //gamma_R
                2.2, // gamma_G
                2.2  //gamma_B
            );
    })();
    
    
    Color.prototype.convertor = Color.Convertor;
    
    //export color
    colors.Color = Color;
    
}());