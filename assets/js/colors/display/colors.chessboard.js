/* 
 * Chessbord class create a chessboard shape costumizable
 * @author Nisho
 */

// namespace:
this.colors = this.colors || {};

(function() {

    var ChessBoard = function(graphics){
        this.initialize(graphics);      
        if(this.colors.length == 0)
            this.colors = ["rgb(245,245,245)", "rgb(60,60,60)"];
    }
    
    ChessBoard.prototype = new createjs.Shape();    

    /**
     * Colors define colors to use
     */
    ChessBoard.prototype.colors = [];

    /**
     * Override draw metod
     * @param {type} width
     * @param {type} height
     * @returns {undefined}
     */
    ChessBoard.prototype.drawChessBoard = function(width, heigth, square){
        var alt = true;
        var rows = Math.ceil(heigth / square);
        var cols = Math.ceil(width / square);
        for(var j = 0; j < rows; j++){
            for(var i = 0; i < cols; i++){
                var color = this.colors[((alt)?i:i+this.colors.length -1) % this.colors.length];
                this.graphics
                    .beginFill(color)
                    .drawRect(i*square,j*square,square, square)
                    .endFill();
            }   
            alt = !alt;
        }
        return this;
    }
    
    colors.ChessBoard = ChessBoard;
}());
