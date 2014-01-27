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
    var Color = function(lab) {
        this.lab = {L: lab.L, a:lab.a, b:lab.b};
        this.rgb = {r: 0, g:0, b:0};
        this.xyz = {x: 0, y:0, z:0};
        this.labMid = {L: lab.L, a:lab.a, b:lab.b};
        this._activeFilters = [];
        this.convert(this.convertor.CielabToRGB);
        this.generateGrayscaleColor();
    };    
        
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
     * Activate filter for an object
     * @param {type} filter
     * @returns {undefined}
     */
    Color.prototype.addFilter = function(filter){     
        console.info("add filter", filter);
        this._activeFilters.push(filter);
        if(filter.init){
            console.info("call init filter");
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
                console.info("call unload filter");
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
                        
        init: function(){
            this.alphaL = 1;
            console.info("init", this, this.alphaL);
        },
        
        unload: function(){
            console.info("call unload");
            delete this.alphaL;
            this.lab = {
                L: this.labMid.L,
                a: this.labMid.a,
                b: this.labMid.b
            };
            
            this.convert(this.convertor.CielabToRGB);
        },
        
        increaseLighting: function(value){
            //se ho raggiunto il valore massimo ritorno
            if(+(this.alphaL) * +(this.labMid.L) >= 100){
                return;
            }
            
            //alfa aumenta per rendere il colore sempre più vicino al bianco
            this.alphaL = this.alphaL + (value/100);                
            // se va oltre 1 riporta a 1
            var Lris = (+(this.alphaL) * +(this.labMid.L));
            this.lab.L = Math.min(100, +(Lris));

            this.convert(this.convertor.CielabToRGB);
        },
        
        decreaseLighting: function(value){
            
            console.info(this, this.alphaL);
            
            //se ho raggiunto il valore massimo ritorno
            if(+(this.alphaL) * +(this.labMid.L) <= 0){
                return;
            }
            
            //alfa aumenta per rendere il colore sempre più vicino al bianco
            this.alphaL = this.alphaL - (value/100);                
            // se va oltre 1 riporta a 1
            var Lris = (+(this.alphaL) * +(this.labMid.L));
            this.lab.L = Math.max(0, +(Lris));

            this.convert(this.convertor.CielabToRGB);
        },
     
    }
    
    
    /**
     * Saturation Filters
     */
    Color.BlackWhite = {
        
        init: function(){
            this.alphaS = 1;
            this.alphaW = 1;            
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
        
        increaseWhite: function(value){
            
            if(this.alphaS === 1){

		//alfa aumenta per rendere il colore sempre più vicino al bianco
                this.alphaW = this.alphaW - (value/100);                
		// se va oltre 1 riporta a 1
                if(this.alphaW < 0) this.alphaW = 0;

		var Lris = (+(this.alphaW) * +(this.labMid.L)) + (1 - +(this.alphaW)) * +(this.labMax.L);
		var aris = (+(this.alphaW) * +(this.labMid.a)) + (1 - +(this.alphaW)) * +(this.labMax.a);
		var bris = (+(this.alphaW) * +(this.labMid.b)) + (1 - +(this.alphaW)) * +(this.labMax.b);
                
                this.lab = {
                    L: +(Lris),
                    a: +(aris),
                    b: +(bris)
                };

                this.convert(this.convertor.CielabToRGB);
            }            
        },
        
        decreaseWhite: function(value){
            
            if(this.alphaS === 1){

		//alfa aumenta per rendere il colore sempre più vicino al bianco
                this.alphaW = +(this.alphaW) + (value/100);                
		// se va oltre 1 riporta a 1
                if(this.alphaW > 1) this.alphaW = 1;

		var Lris = (+(this.alphaW) * +(this.labMid.L)) + (1 - +(this.alphaW)) * +(this.labMax.L);
		var aris = (+(this.alphaW) * +(this.labMid.a)) + (1 - +(this.alphaW)) * +(this.labMax.a);
		var bris = (+(this.alphaW) * +(this.labMid.b)) + (1 - +(this.alphaW)) * +(this.labMax.b);

                this.lab = {
                    L: +(Lris),
                    a: +(aris),
                    b: +(bris)
                };

                this.convert(this.convertor.CielabToRGB);
            }      
        },
        
        increaseBlack: function(value){
            
            if(this.alphaW === 1){
                
                this.alphaS = this.alphaS - (value/100);
                if(this.alphaS < 0) this.alphaS = 0;

                var Lris = (this.alphaS * +(this.labMid.L)) + (1 - this.alphaS) * +(this.labMin.L);  
                var aris = (this.alphaS * +(this.labMid.a)) + (1 - this.alphaS) * +(this.labMin.a);
                var bris = (this.alphaS * +(this.labMid.b)) + (1 - this.alphaS) * +(this.labMin.b);

                this.lab = {
                    L: +(Lris),
                    a: +(aris),
                    b: +(bris),
                };
                
                this.convert(this.convertor.CielabToRGB);
            }
        },
        
        decreaseBlack: function(value){
            if(this.alphaW === 1){

                this.alphaS = this.alphaS + (value/100);
                if(this.alphaS > 1) this.alphaS = 1;
                
                var Lris = (this.alphaS * +(this.labMid.L)) + (1 - this.alphaS) * +(this.labMin.L);
                var aris = (this.alphaS * +(this.labMid.a)) + (1 - this.alphaS) * +(this.labMin.a);
                var bris = (this.alphaS * +(this.labMid.b)) + (1 - this.alphaS) * +(this.labMin.b);
                
                this.lab = {
                    L: +(Lris),
                    a: +(aris),
                    b: +(bris),
                };

                this.convert(this.convertor.CielabToRGB);                
            }
        }   
        
    }

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

        monitor : (function(){
            
            //dati monitor QUATO TFT mio
            var xR = 0.64, //0.644,
                yR = 0.33, //0.324,
                zR = 0.03, // 1 - 0.644 - 0.324,
                xG = 0.30, //0.298,
                yG = 0.60, //0.61,
                zG = 0.1, // 1 - 0.298 - 0.61,
                xB = 0.15, // 0.141,
                yB = 0.06, // 0.061,
                zB = 0.79, // 1 - 0.141 - 0.061,
                xW = 0.3127, //0.3144,   //0.313
                yW = 0.329, //0.33,     //0.329
                zW = 0.3583, //1 - 0.3144 - 0.33,
                gamma_R = 2.2,
                gamma_G = 2.2,
                gamma_B = 2.2;

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
                
            return {
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
        })(),

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
    
    Color.prototype.convertor = Color.Convertor;
    
    //export color
    colors.Color = Color;
    
}());