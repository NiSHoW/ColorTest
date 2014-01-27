/* 
 * Color palette container for all color cloves
 * @author Nisho
 */
// namespace:
colors = colors || {};

(function() {

    var Palette = function(graphics){
        this.initialize(graphics);        
    }
    
    Palette.prototype = new createjs.Container();    

    /**
     * @property DisplayObject_initialize
     * @type Function
     * @private
     **/
    Palette.prototype.Container_initialize = Palette.prototype.initialize;

    /**
     * Initialization method.
     * @method initialize
     * @protected
    */
    Palette.prototype.initialize = function(graphics) {
        this.Container_initialize(graphics);

        if(this.colors.length == 0)
            this.colors = ["yellow", "green", "red", "blue", "gray"];   
        this._pointer = new colors.Pointer();
    };    

    /**
     * Colors define colors to use
     */
    Palette.prototype.colors = [];

    /**
     * Aplitude of segments
     */
    Palette.prototype.radius = 200;

    /**
     * Aplitude of segments
     */
    Palette.prototype.clovesNumber = 5;

    /**
     * Aplitude of segments
     */
    Palette.prototype.angle = 0;
    
    /**
     * Listeners of onSelectClove
     */
    Palette.prototype.cloves = [];
    
    /**
     * Aplitude of segments
     */
    Palette.prototype._pointer = null;    

    /**
     * Aplitude of segments
     */
    Palette.prototype._selectedClove = null;
    
    /**
     * Aplitude of segments
     */
    Palette.prototype.getSelectedClove = function(){
        return this._selectedClove;
    };

    /**
     * Listeners of onSelectClove
     */
    Palette.prototype._selectedCloveListeners = [];


    /**
     * Aplitude of segments
     */
    Palette.prototype.addOnSelectCloveListener = function(listener){
        if(listener.hasOwnProperty("onCloveSelected"))
            this._selectedCloveListeners.push(listener);
    };
    
    
    /**
     * 
     * @param {type} clove
     * @returns {undefined}
     */
    Palette.prototype.selectClove = function(clove){
        if(createjs.indexOf(this.children, this._pointer) !== -1){            
            this.removeChild(this._pointer);
        }
        
        //controllo clove addiacenti
        var index = createjs.indexOf(this.cloves, clove);
        var nearLeft = ((index - 1) < 0 ? this.cloves.length - 1: (index - 1));
        var nearRight = (index + 1) % this.cloves.length;
        
        var error = null;
        if(clove.color.final){
            error = "final";
        } else if(!this.cloves[nearLeft].color.active &&
           !this.cloves[nearRight].color.active){
            error = "no-adjacent";
        } 
        
        if(error !== null){
            for(var i = 0; i < this._selectedCloveListeners.length; i++){
                if(this._selectedCloveListeners[i].hasOwnProperty("onCloveSelectedError"))
                    this._selectedCloveListeners[i].onCloveSelectedError(error);
            }
            return;
        }
        
        
        if(this._selectedClove !== null){
            this._selectedClove.graphics.clear();
            this._selectedClove.drawClove(this.radius);
            
            if(clove.id === this._selectedClove.id) {                
                this._selectedClove = null;
                //update listener
                for(var i = 0; i < this._selectedCloveListeners.length; i++){
                    this._selectedCloveListeners[i].onCloveSelected(null);
                }
                return;
            }
        }
        
        this._selectedClove = clove;
        this.drawSelectedClove();
        
        //update listener
        for(var i = 0; i < this._selectedCloveListeners.length; i++){
            this._selectedCloveListeners[i].onCloveSelected(clove);
        }
    };
    
    
    /**
     * generate colors random
     * @param {type} num
     * @returns {undefined}
     */
    Palette.prototype.generateColors = function(num){
        this.colors = [];
        for(var i = 0; i < num; i++){
            this.colors[i] = ('00000'+(Math.random()*(1<<24)|0).toString(16)).slice(-6);
        }
    };
    
    
    /**
     * 
     * @param {type} clove
     * @returns {undefined}
     */
    Palette.prototype.drawSelectedClove = function(){                
        var angle = this._selectedClove.startAngle;        
        angle += (this._selectedClove.endAngle - this._selectedClove.startAngle) /2;        
        
        this._pointer.graphics.clear();    
        this._pointer.angle = angle;
        this._pointer.radiusPosition = this.radius + 20;
        this._pointer.drawPointer();                   
        this.addChild(this._pointer);        
    };        
     
    
    /**
     * Override draw metod
     * @param {type} width
     * @param {type} height
     * @returns {undefined}
     */
    Palette.prototype.drawColorPalette = function(radius){      
        var $instance = this;
        this.radius = radius || this.radius;
        var angle = (Math.PI*2) / this.clovesNumber;
                    
        if(this.getNumChildren() !== this.clovesNumber){
            delete this.cloves;
            this.cloves = [];
            this.removeAllChildren();
            
            for(var i = 0; i < this.clovesNumber; i++){            
                var startAngle = this.angle + angle*(i);
                var endAngle = this.angle + angle*(i+1);

                //generate clove
                var clove = new colors.Clove();
                clove.color = this.colors[i];
                clove.startAngle = startAngle;
                clove.endAngle = endAngle;
                clove.drawClove(radius);
                clove.on("click", function(evt){
                    $instance.selectClove(this);
                });

                this.cloves.push(clove);
                this.addChild(clove);
            }
        } else {
            for(var i = 0; i < this.getNumChildren(); i++){            
                var clove = this.getChildAt(i);
                clove.drawClove(radius);
            }
        }                  
                
        if(this._selectedClove !== null){                 
            this.drawSelectedClove();
        }
        
        return this;
    };
    
    
    colors.Palette = Palette;   
    
}());


