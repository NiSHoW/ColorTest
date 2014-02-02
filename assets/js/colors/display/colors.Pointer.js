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
    var Pointer = function(graphics){
        this.id = colors.generateID();
        this.initialize(graphics);        
    }
    
    //EXTENDS SHAPE
    Pointer.prototype = new createjs.Shape();    
    /**
     * @property DisplayObject_initialize
     * @type Function
     * @private
     **/
    Pointer.prototype.Shape_initialize = Pointer.prototype.initialize;

    /**
     * Initialization method.
     * @method initialize
     * @protected
    */
    Pointer.prototype.initialize = function() {
        this.Shape_initialize();
    };
    
    
    Pointer.prototype.backgroundColor = '#222222';     
    
    Pointer.prototype.borderColor = '#333333';   
    
    Pointer.prototype.borderSize = 2;   
    
    Pointer.prototype.angle = 0;
    
    Pointer.prototype.radiusPosition = 220;

    /**
     * Override draw metod
     * @param {type} width
     * @param {type} height
     * @returns {undefined}
     */
    Pointer.prototype.drawPointer = function(){
                        
        var matrix = new createjs.Matrix2D();        
        matrix.rotate(this.angle);
        
//        console.info(this.angle, (this.angle * (180 / Math.PI))+"°");
        
        var pt = matrix.transformPoint(this.radiusPosition + 20, 0);   
        this.graphics.setStrokeStyle(this.borderSize, 2, 2);
        this.graphics.beginStroke(this.borderColor).beginFill(this.backgroundColor);        
        this.graphics.drawCircle(pt.x, pt.y, 5);
        
//        this.graphics.setStrokeStyle(this.borderSize, 2, 2);
//        this.graphics.beginStroke(this.borderColor).beginFill(this.backgroundColor);        
//        
//        var pt = matrix.transformPoint(this.radiusPosition + 30, - 12);
//        this.graphics.lineTo(pt.x, pt.y);        
//        
//        var pt = matrix.transformPoint(this.radiusPosition + 25, 0);
//        this.graphics.lineTo(pt.x, pt.y);
//        
//        var pt = matrix.transformPoint(this.radiusPosition + 30, + 12);
//        this.graphics.lineTo(pt.x, pt.y);
        
        this.graphics.closePath();    

        return this;
        
    }

    colors.Pointer = Pointer;
    
}());

