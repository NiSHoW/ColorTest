/* 
 * Create a clove of circle 
 * @author Nisho
 */
// namespace:
colors = colors || {};

(function() {

    /**
     * Segment part of color palette
     * @param {type} graphics
     * @returns {_L9.Clove}
     */
    var Clove = function(graphics){
        this.id = colors.generateID();
        this.initialize(graphics);        
    }
    
    //EXTENDS SHAPE
    Clove.prototype = new createjs.Shape();    
    
    Clove.prototype.color = 'white';    
    
    Clove.prototype.segments = 2;
    
    Clove.prototype.amplitude = 6;
    
    Clove.prototype.radius = 200; 
    
    Clove.prototype.startAngle = 0; 
    
    Clove.prototype.endAngle = Math.PI*2/5;
    
    Clove.prototype.span = 10;
    /**
     * Override draw metod
     * @param {type} width
     * @param {type} height
     * @returns {undefined}
     */
    Clove.prototype.drawClove = function(radius){
        
        this.radius = radius || this.radius;
                        
        var matrix = new createjs.Matrix2D();
        var center = new createjs.Point(0, 0);
        var numSeg = Math.ceil(radius/this.segments);
        var color = this.color;   
        if(this.color instanceof colors.Color){
            color = this.color.cssRGB();
        }

        matrix.rotate(this.startAngle);
           
        var pt = {x: 0, y:0 };          
        this.graphics.beginStroke(color).beginFill(color);        

        var y = 0;
        //draw first sinusoid
        for(var x = 0; x <= radius;x++){      
            y = Math.sin(x*Math.PI*2/numSeg)*this.amplitude;
            pt = matrix.transformPoint(x, y);
            this.graphics.lineTo(pt.x, pt.y);
        }

        //draw arc
        this.graphics.arc(center.x, center.y, radius, this.startAngle, this.endAngle);      
        
        matrix.identity();            
        matrix.rotate(this.endAngle);                                    

        //draw second sinusoid
        for(var x = radius; x > -1; x--){
            y = Math.sin(x*Math.PI*2/numSeg)*this.amplitude;
            pt = matrix.transformPoint(x, y);
            this.graphics.lineTo(pt.x, pt.y);
        }

        this.graphics.closePath();    

        return this;
        
    }
    
    
    Clove.prototype.updateClove = function(){
        this.graphics.clear();
        this.drawClove(this.radius);
    }

    colors.Clove = Clove;
    
}());

