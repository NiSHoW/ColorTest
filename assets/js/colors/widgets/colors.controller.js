/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// namespace:
this.colors = this.colors || {};

(function() {

    var controllers_id = 0;        

    function generateId(){
        controllers_id++;
        return controllers_id;
    }

    /**
     * CONTROL ITEM
     * @param {type} options
     * @returns {_L11.Control}
     */
    var Control = function(options){ 
        this.options = colors.extend({}, this.defaultControlOptions);
        this.options = colors.extend(this.options, options);
    }

    Control.prototype.defaultControlOptions = {
        defaultValue: 5, 
        range: "max",
        min: 1,
        max: 10,
        step: 1,
        colorChangeListener: null,
        controlTemplate: '<div class="control large-12 columns">\
            <a id="slider-#{id}-min" class="mins-button button large-1 columns" href="#"><<</a>\
            <div id="slider-#{id}" data-tooltip class="large-8 large-offset-1 columns has-tip" title="#{defaultValue}"></div>\
            <a id="slider-#{id}-plus" class="plus-button button large-1 columns" href="#">>></a>\
        </div>',
        plus: function(control, color){},
        mins: function(control, color){},
        onchange: function(control, color){}
    };

    Control.prototype.id = null;
    
    Control.prototype.options = {};
    
    Control.prototype.plusContainer = null;
    
    Control.prototype.minsContainer = null;
    
    Control.prototype.sliderContainer = null;    
    
    Control.prototype.create = function(id, index){
        this.id = id+'-'+index;
        var controlTpl = this.options.controlTemplate;
        controlTpl = controlTpl.replaceAll('#{id}', this.id);   
        for(var prop in this.options){
            controlTpl = controlTpl.replaceAll('#{'+prop+'}', this.options[prop]);   
        }        
        return controlTpl;
    }    

    colors.Control = Control;

    /**
     * CONTROLLER CLASS
     * @param {type} options
     * @returns {_L11.Controller}
     */
    var Controller = function(options){    
        this.options = colors.extend({}, this.defaultOptions);
        this.options = colors.extend(this.options, options);
        this.id = generateId();
        this.color = this.options.color; 
        this.activeControls = [];
        this.enabled = true;
    };  
     

    Controller.prototype.defaultOptions = {
        color: null,
        template: '<div id="controller-#{id}" class="panel color-edit-container">\
            <div class="color-value-container">\
                <div id="color-value-#{id}" class="alert-box radius secondary"> \
                    <span style="background-color:#{cssrgb}" class="colore #{debug}">\
                </span><span id="color-text-#{id}" class="color-text #{debug}">#{color}</span>\
            </div/>\
            <div class="row color-controls-container">#{controls}</div/>\
        </div>',
        noControl: '<div class="control large-12 columns">\
            Colore di riferimento\
        </div>', 
        controls: []
    };    
    
    Controller.prototype.options = {};
    
    Controller.prototype.colorChangeListener = null;        
    
    Controller.prototype.disable = function(index){
        index = (index === undefined) ? -1 : index;
        if(index > -1){
            var control = this.activeControls[index];
            $(control.plusContainer).addClass("disabled");
            $(control.minsContainer).addClass("disabled");
            $(control.sliderContainer).slider("disable");
        } else {        
            for(var i = 0; i < this.activeControls.length; i++){
                var control = this.activeControls[i];
                $(control.plusContainer).addClass("disabled");
                $(control.minsContainer).addClass("disabled");
                $(control.sliderContainer).slider("disable");
            }
            this.enabled = false;
        }
    };
    
    Controller.prototype.enable = function(index){
        index = (index === undefined) ? -1 : index;
        if(index > -1){
            var control = this.activeControls[index];
            $(control.plusContainer).removeClass("disabled");
            $(control.minsContainer).removeClass("disabled");
            $(control.sliderContainer).slider("enable");
        } else {
            for(var i = 0; i < this.activeControls.length; i++){
                var control = this.activeControls[i];
                $(control.plusContainer).removeClass("disabled");
                $(control.minsContainer).removeClass("disabled");
                $(control.sliderContainer).slider("enable");
            }    
            this.enabled = true;
        }
    };
    
    Controller.prototype.create = function(container){
        var $this = this;
        var color = this.color;
        var controlsHtml = '';
        if(color.final || this.options.controls.length === 0){
            controlsHtml += this.options.noControl;
        } else {
            this.options.controls.forEach(function(options, index){
                var control = new colors.Control(options);
                controlsHtml += control.create($this.id, index);
                $this.activeControls.push(control);                
            });
        }               
                
        var tpl = this.options.template;
        tpl = tpl.replaceAll('#{id}', this.id); 
        tpl = tpl.replace('#{controls}', controlsHtml);        
        tpl = tpl.replace('#{cssrgb}', color.cssRGB());
        tpl = tpl.replace('#{color}', color.toString());
        tpl = tpl.replaceAll('#{debug}', ((color.debug)? 'debug':''));
        
        $(container).append(tpl);  

        this.colorChangeListener = {
            onColorChange: function(color){
                $('#color-value-'+$this.id+' .colore').css('background-color', color.cssRGB());
                $('#color-text-'+$this.id).html(color.toString());
            }
        }
        color.addOnColorChangeListeners(this.colorChangeListener);
        
        //enable all control
        this.activeControls.forEach(function(control, index){
            control.plusContainer = $('#slider-'+control.id+'-plus');
            control.minsContainer = $('#slider-'+control.id+'-min');
            control.sliderContainer = $('#slider-'+control.id+'');
            control.plusContainer.on('click', function(event){
                event.preventDefault();
                control.options.plus.apply($this, [control, color]);
            });  
            control.minsContainer.on('click', function(event){
                event.preventDefault();
                control.options.mins.apply($this, [control, color]);
            });     
            control.sliderContainer.slider({
                range: control.options.range,
                min: control.options.min,
                max: control.options.max,
                step: control.options.step,
                value: control.options.defaultValue,
                numPages: (control.options.max - control.options.min) / 5,
                slide: function( event, ui ) {
                    control.options.onchange.apply($this, [ui, control, color]);
                }
            });            
        });
    };    

    colors.Controller = Controller;

})();