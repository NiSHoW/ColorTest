/*
* Matrix
*/

// namespace:
this.colors = this.colors || {};

(function() {

/**
 * Matrix 3 x 3 NOT for affine trasform 2d space
 *  | a | b | c |
 *  | d | e | f |
 *  | g | h | i |
 **/
var Matrix3  = function() {
    this.initialize();
};

// public properties:
    /**
     * Position (0, 0) 
     * @property a
     * @type Number
     **/
    Matrix3.prototype.a = 0;

    /**
     * Position (0, 1) 
     * @property b
     * @type Number
     **/
    Matrix3.prototype.b = 0;

    /**
     * Position (0, 2) 
     * @property c
     * @type Number
     **/
    Matrix3.prototype.c = 0;

    /**
     * Position (0, 0) 
     * @property a
     * @type Number
     **/
    Matrix3.prototype.d = 0;

    /**
     * Position (0, 1) 
     * @property b
     * @type Number
     **/
    Matrix3.prototype.e = 0;

    /**
     * Position (0, 2) 
     * @property c
     * @type Number
     **/
    Matrix3.prototype.f = 0;
    
    /**
     * Position (0, 0) 
     * @property a
     * @type Number
     **/
    Matrix3.prototype.g = 0;

    /**
     * Position (0, 1) 
     * @property b
     * @type Number
     **/
    Matrix3.prototype.h = 0;

    /**
     * Position (0, 2) 
     * @property c
     * @type Number
     **/
    Matrix3.prototype.i = 0;    

// constructor:
    /**
     * Initialization method. Can also be used to reinitialize the instance.
     * @method initialize
     * @return {Matrix3} This instance. Useful for chaining method calls.
    */
    Matrix3.prototype.initialize = function() {
        return this;
    };

// public methods:

    /**
     * Sets the properties of the matrix to those of an identity matrix (one that applies a null transformation).
     * @method identity
     * @return {Matrix2D} This matrix. Useful for chaining method calls.
     **/
    Matrix3.prototype.identity = function() {
        this.a = this.e = this.i = 1;
        this.b = this.c = this.d = this.f = this.g = this.h = 0;
        return this;
    };

    /**
     * Calculate determiant
     */
    Matrix3.prototype.setAll = function(array){
        this.a = array[0];
        this.b = array[1];
        this.c = array[2];
        this.d = array[3];
        this.e = array[4];
        this.f = array[5];
        this.g = array[6];
        this.h = array[7];
        this.i = array[8];
    }

    /**
     * Calculate determiant
     *  | a | b | c | a | b
     *  | d | e | f | d | e
     *  | g | h | i | g | h
     *  
     *  det = aei + bfg + cdh - (ceg + afh + bdi)
     */
    Matrix3.prototype.determinat = function(){
        return (this.a * this.e * this.i) +
               (this.b * this.f * this.g) +
               (this.c * this.d * this.h) - (
                    (this.c * this.e * this.g) +
                    (this.a * this.f * this.h) +
                    (this.b * this.d * this.i)
               );
    }

    /**
     * Inverts the matrix, causing it to perform the opposite transformation.
     * @method invert
     * @return {Matrix2D} This matrix. Useful for chaining method calls.
     *  
     * 
     * 
     * 
     **/
    Matrix3.prototype.invert = function() {
        var determ = this.determinat();
	var a = (1 / determ) * ((this.e * this.i) - (this.h * this.f));
	var b = -(1 / determ) * ((this.b * this.i) - (this.h * this.c));
	var c = (1 / determ) * ((this.b * this.f) - (this.e * this.c));
	var d = -(1 / determ) * (this.d * this.i - this.g * this.f);
	var e = (1 / determ) * (this.a * this.i - this.g * this.c);
	var f = -(1 / determ) * (this.a * this.f - this.d * this.c);
	var g = (1 / determ) * (this.d * this.h - this.g * this.e);
	var h = -(1 / determ) * (this.a * this.h - this.g * this.b);
	var i = (1 / determ) * (this.a * this.e - this.d * this.b);
        
        this.setAll([a, b, c, d, e, f, g, h, i]);
        
    };

    /**
     * Returns true if the matrix is an identity matrix.
     * @method isIdentity
     * @return {Boolean}
     **/
    Matrix3.prototype.isIdentity = function() {
            return 
                this.a === 1 && 
                this.b === 0 && 
                this.c === 0 && 
                this.d === 0 && 
                this.e === 1 &&
                this.f === 0 && 
                this.g === 0 && 
                this.h === 0 && 
                this.i === 1;
    };

    /**
     * Copies all properties from the specified matrix to this matrix.
     * @method copy
     * @param {Matrix2D} matrix The matrix to copy properties from.
     * @return {Matrix2D} This matrix. Useful for chaining method calls.
    */
    Matrix3.prototype.copy = function(matrix) {
        this.a = matrix.a;
        this.b = matrix.b;
        this.c = matrix.c;
        this.d = matrix.d;
        this.e = matrix.e;
        this.f = matrix.f;
        this.g = matrix.g;
        this.h = matrix.h;
        this.i = matrix.i;
        return this;
    };

    /**
     * Returns a clone of the Matrix2D instance.
     * @method clone
     * @return {Matrix2D} a clone of the Matrix2D instance.
     **/
    Matrix3.prototype.clone = function() {
        return (new Matrix3()).copy(this);
    };

    /**
     * Returns a string representation of this object.
     * @method toString
     * @return {String} a string representation of the instance.
     **/
    Matrix3.prototype.toString = function() {
        return "[Matrix3 ("+
            "a="+this.a+" "+
            "b="+this.b+" "+
            "c="+this.c+" "+
            "d="+this.d+" "+
            "e="+this.e+" "+
            "f="+this.f+" "+
            "g="+this.g+" "+
            "h="+this.h+" "+
            "i="+this.i+" "+
        ")]";
    };

    colors.Matrix3 = Matrix3;
}());
