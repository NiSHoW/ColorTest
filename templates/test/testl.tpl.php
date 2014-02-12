<?php 
$this->sections->title = "TestL";
?>
<div id="controls" class="small-4 columns panel">

    <div id="controls-container"></div>
    
    <div  id="save-container" class="panel large-12 small-12">
        <div class="large-12 small-12">
            <a id="save" href="#" class="button large-12 small-12 disabled">REGISTRA DATI</a>
        </div>
    </div>      
        
</div>

<div id="demoCanvas" class="small-8 columns"></div>

<ol class="joyride-list" data-joyride>
  <li data-text="Prossimo" data-options="tip_animation:fade">
    <h4>Istruzioni</h4>
    <p> L'esperimento analizza il rapporto tra colore e forma e in particolare il
        ruolo giocato dal colore nella percezione di profondit&agrave; delle superfici 
        visive.</p>
    <p> Il compito dei partecipanti &egrave; modificare il colore di quattro quadranti 
        di un cerchio, facendo uso delle barre laterali, in modo che tutti 
        appaiano complanari, ovvero nessuno "sopra" o "sotto" un altro, o pi&ugrave; 
        vicino o pi&ugrave; lontano dall'osservatore degli altri.</p>
    <p> Ogni colore pu&ograve; essere modificato pi&ugrave; volte prima di salvare, 
        osservare il cerchio con sguardo globale ad una certa distanza, 
        senza fissare un settore particolare, per vedere se tutti i settori nel 
        loro insieme sono sullo stesso piano, ed eventualmente correggere il 
        colore che appare pi&ugrave; lontano o vicino</p>
    <p> Non ci sono risposte sbagliate n&eacute; tempi di reazione, si richiede invece
        di svolgere il compito con accuratezza. 
    </p>
  </li>  
  <li data-id="controls-container" data-button="Prossimo" 
      data-options="tip_location:right;tip_offset_left=30;nub_position:left;tip_animation:fade;">
    <h4>Cambia i valori</h4>
    <p> Qui puoi visualizzare i colori da modificare</p>
    <p> Usa la barra per aumentare e diminuire la luminosit&agrave, puoi
        usare anche i pulsanti laterali per fare piccole variazioni.</p>
    <p>
        <strong>Ricorda che puoi selezionare solo colori adiacenti a un colore attivo
        Una volta modificato un colore vedrai attivarsi i controlli dei colori vicini.</strong>
    </p>        
  </li>
  <li data-id="save-container" data-button="Prossimo" data-options="tip_location:top;nub_position:bottom-right;in_window:true;tip_animation:fade">
    <h4>Per salvare i dati</h4>
    <p>Ricordati di premere sul bottone sottostante per salvare i dati per terminare la sessione dell'esperimento!</p>
  </li>
  <li id="endride" data-button="Fine" data-options="tip_location:right;tip_offset_left=30;nub_position:left;tip_animation:fade;">
    <h4>Ora puoi iniziare</h4>
    <p>Puoi partire da questo colore per iniziare!</p>
  </li>  
</ol>

<script src="<?php echo $this->basePath; ?>assets/js/foundation/foundation.joyride.js"></script>
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/easeljs-0.7.0.min.js"></script>        
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/colors/colors.js"></script>    
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/colors/colors.matrix.js"></script>   
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/colors/colors.color.js"></script>   
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/colors/widgets/color.slider.js"></script>   
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/colors/widgets/colors.controller.js"></script>   
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/colors/display/colors.chessboard.js"></script>    
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/colors/display/colors.clove.js"></script>    
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/colors/display/colors.Pointer.js"></script>          
<script type="text/javascript" src="<?php echo $this->basePath; ?>assets/js/colors/display/colors.palette.js"></script>                
<script>
    //create app namespace
    window.app = window.app || {};
    window.app = colors.extend(window.app, {
        rotateAngles: [36/180*Math.PI, 24/180*Math.PI, 18/180*Math.PI],
        selectedAngle: Math.floor(Math.random()*3),
        modValue: 5,    
        colors: [],
        controls: [],
        tooltip: {
            previus: null
        },
        /**
         * 
         * @returns {undefined}
         */
        init: function(){

            var pads = $("#demoCanvas").padding();
            app.screenWidth = $("#demoCanvas").innerWidth() -pads.left -pads.right ;
            app.screenHeight = $("#demoCanvas").innerHeight() -pads.bottom -pads.top;

            $("#demoCanvas").append("<canvas id=\"demoCanvasStage\" "+
                "width=\""+app.screenWidth+"px\" "+
                "height=\""+app.screenHeight+"px\">"+
            "</canvas>");            

            app.stage = new createjs.Stage("demoCanvasStage");
            app.chess = new colors.ChessBoard();
            app.chess.x = 0;
            app.chess.y = 0;
            app.chess.drawChessBoard(app.screenWidth, app.screenHeight, 100);            
            app.stage.addChild(app.chess);

            //make calibration
            app.calibration = <?php echo json_encode($this->calibration); ?>;
            colors.Color.Convertor.calibrate.apply(colors.Color.Convertor, app.calibration);
            
            var debug = <?php echo (($this->debug) ? 'true' : 'false' ) ?>;
            
            //set colors
            var colorArray = <?php echo json_encode($this->colors); ?>;
            for(var c in colorArray){
                var color = new colors.Color(colorArray[c].lab, c);
                if(colorArray[c].final){ 
                    color.final = true;
                    color.active = true;
                }
                color.debug = debug;
                /*color.labMax = colorArray[c].max;
                color.labMin = colorArray[c].min;*/
                app.colors.push(color);
            }

            app.disableSlider();

            app.palette = new colors.Palette();            
            app.palette.colors = app.colors;            
            app.palette.x = Math.round(app.screenWidth / 2);
            app.palette.y = Math.round(app.screenHeight / 2);
            app.palette.radius = Math.round(Math.min(app.screenWidth, app.screenHeight) / 3);
            app.palette.angle = <?php echo $this->angle ?>;
            app.palette.drawColorPalette();

            app.stage.addChild(app.palette);                           
            app.stage.update();
            
            app.bindControls();
            app.disableSlider();
        },
        
        /**
         * 
         */
        update: function(){
            $(document).foundation();
            var pads = $("#demoCanvas").padding();
            app.screenWidth = $("#demoCanvas").innerWidth() -pads.left -pads.right ;
            app.screenHeight = $("#demoCanvas").innerHeight() -pads.bottom -pads.top;

            $("#demoCanvas").append("<canvas id=\"demoCanvasStage\" "+
                "width=\""+app.screenWidth+"px\" "+
                "height=\""+app.screenHeight+"px\">"+
            "</canvas>");            

            app.stage = new createjs.Stage("demoCanvasStage");
            app.chess.drawChessBoard(app.screenWidth, app.screenHeight, 100);       
            app.stage.addChild(app.chess);
            app.palette.x = Math.round(app.screenWidth / 2);
            app.palette.y = Math.round(app.screenHeight / 2);
            app.palette.radius = Math.round(Math.min(app.screenWidth, app.screenHeight) / 3);  
            app.palette.drawColorPalette();
            
            app.stage.addChild(app.palette);   
            app.stage.update();
        },       
        
        bindControls: function(){
            
            for(var i = 0; i < app.colors.length; i++){
                var color = app.colors[i];
                //add controller for color
                var control = new colors.Controller({
                    color: color,
                    controls: [{
                        min: 0,
                        max: 100,
                        step: 0.1,
                        defaultValue: color.lab.L,
                        plus: function(control, color){
                            if(app.controlSelection(color)){     
                                if(!color.hasFilter(color.filters.Lighting)){
                                    color.addFilter(color.filters.Lighting);                
                                }
                                color.applyFilter("increaseLighting", 0.1);
                                $(control.sliderContainer).find('a.ui-slider-handle').focus();
                                Foundation.libs.tooltip.getTip($(control.sliderContainer)).html(
                                    (new Number(color.lab.L)).toPrecision(3)+'<span class="nub"></span>'
                                );
                                $(control.sliderContainer).attr('title', color.lab.L);
                                $(control.sliderContainer).slider('value', color.lab.L);
                                app.stage.update();                    
                            }
                        },
                        mins: function(control, color){
                            if(app.controlSelection(color)){     
                                if(!color.hasFilter(color.filters.Lighting)){
                                    color.addFilter(color.filters.Lighting);                
                                }
                                color.applyFilter("decreaseLighting", 0.1);
                                $(control.sliderContainer).find('a.ui-slider-handle').focus();
                                Foundation.libs.tooltip.getTip($(control.sliderContainer)).html(
                                    (new Number(color.lab.L)).toPrecision(3)+'<span class="nub"></span>'
                                );
                                $(control.sliderContainer).slider('value', color.lab.L);
                                app.stage.update();                  
                            }
                        },
                        onchange: function(ui, control, color){
                            if(app.controlSelection(color)){     
                                if(!color.hasFilter(color.filters.Lighting)){
                                    color.addFilter(color.filters.Lighting);                
                                }

                                color.applyFilter("setLighting", ui.value);  
                                Foundation.libs.tooltip.getTip($(control.sliderContainer)).html(
                                    (new Number(color.lab.L)).toPrecision(3)+'<span class="nub"></span>'
                                );
                                $(control.sliderContainer).attr('title', color.lab.L);
                                app.stage.update();    
                            } else {
                                $(control.sliderContainer).slider('value', color.lab.L);
                            }
                        }
                    }]
                });

                app.controls.push(control);
                control.create($('#controls-container'));            
            }
            
            $('#save').addClass("disabled");
            
            $('#save').on('click', function(event){
                event.preventDefault();
                if($(this).hasClass("run")){
                    tooltip("Salvataggio in corso, attendi..", 'alert');
                    return;                    
                }                
                if($(this).hasClass("disabled")){
                    tooltip("Modifica tutti i valori prima di salvare", 'alert');
                    return;
                }
                
                $(this).addClass("run");

                var colors = {};
                for(var i = 0; i < app.colors.length; i++){                
                    colors[app.colors[i].id] = {
                        lab: app.colors[i].lab,
                        labMin: app.colors[i].labMin,
                        labMid: app.colors[i].labMid,
                        labMax: app.colors[i].labMax
                    };
                }
                
                $.post('<?php echo $this->basePath ?>testL/save', {colors: JSON.stringify(colors)}, 
                    function(data, textStatus, jqXHR){
                        $('#save').removeClass("disabled");
                        $('#save').removeClass("run");
                        try{
                            var response = JSON.parse(data);
                            if(response.status == 'OK'){
                                tooltip("Sessione salvata correttamente", "info");
                                setTimeout(function(){
                                    window.location.replace(response.redirect);
                                }, 2000);
                            }
                        } catch(ex){   
                            tooltip("Errore nel salvataggio, prego riprovare", "warning");
                        }                    
                });
            });   
            
        },
        
        /**
         * 
         * @param {type} values
         * @returns {undefined}
         */
        controlSelection: function(color){                
            var index = createjs.indexOf(app.colors, color);
            var nearLeft = ((index - 1) < 0 ? app.colors.length - 1: (index - 1));
            var nearRight = (index + 1) % app.colors.length;            
            
            if(!app.colors[nearLeft].active &&
                !app.colors[nearRight].active){
                tooltip("Devi selezionare un colore adiacente a un colore attivo", "info");
                return false;
            } 
            
            color.active = true;
            app.disableSlider();
            return true;
        },
        /**
         * 
         * @param {type} values
         * @returns {undefined}
         */
        disableSlider: function(){
            var allenabled = true;
            for( var i = 0; i < app.controls.length; i++){
                var controller = app.controls[i];
                var index = createjs.indexOf(app.colors, controller.color);
                var nearLeft = ((index - 1) < 0 ? app.colors.length - 1: (index - 1));
                var nearRight = (index + 1) % app.colors.length;            
            
                if(!app.colors[nearLeft].active &&
                    !app.colors[nearRight].active){
                    allenabled = false;
                    controller.disable();
                } else {
                    controller.enable();
                }
            }
            
            if(allenabled){
                $('#save').removeClass('disabled');
            }
        }, 
        
        showJoyride: function(){
            var active = null;
            for(var i = 0; i < app.controls.length; i++){
                if(app.controls[i].enabled){
                    active = app.controls[i];
                    break;
                }
            }            
            
            //append active controller id
            $('#endride').attr('data-id', 'controller-'+active.id);
            
            setTimeout(function(){
                $(document).foundation('joyride', 'start', {
                    pre_step_callback:function(index){
                        if(index === 1){
                            $('#controls').scrollTop($("#controls-container").offset().top - 70,{
                                duration: 1000
                            });
                        } else if(index === 2){
                            $('#controls').scrollTop($('#controls').height(),{
                                duration: 1000
                            });
                        } else if(index === 3){
                            $('#controls').scrollTop($('#controller-'+active.id).top - 70, {
                                duration: 2000
                            });
                            $('#controller-'+active.id).find('a.ui-slider-handle').focus();
                        }    
                    },
                });
            }, 500);           
        }
    });


    $(document).ready(function(){

        $(document).foundation();
        app.init(); //initialize 
        app.showJoyride();

        $( window ).resize(function(event) {
            event.preventDefault();
            $("#demoCanvasStage").remove();
            app.update();
        });
    });         

</script>
