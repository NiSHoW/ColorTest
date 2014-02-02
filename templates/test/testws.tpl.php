<?php 
$this->sections->title = "TestWS";
?>
<style>#slider {margin-bottom: 20px;}</style>
<div id="controls" class="small-2 columns panel">

    <div id="selected-value-container"  class="row">
        <div class="control small-2 large-12 columns">
            <label>Selezionato:</label>
            <div id="selected-value" class="alert-box radius secondary">
                <span>Nessuno</span>
            </div>
        </div>
    </div>

    <div id="slider-container" class="row">
        <div class="large-12 small-centered columns">
            <label>Valore di modifica</label>
            <div id="slider" data-tooltip class="has-tip" title="5"></div>
        </div>
    </div>            
    
    <div class="row">
        <div class="large-12 small-centered columns">
            <label>Modifica Bianco: +/- <span class="mod-val">5</span>%</label>
            <a id="WPlus" href="#" class="button disabled">+</a>
            <a id="WMins" href="#" class="button disabled">-</a>
            <a id="WReset" href="#" class="button button-reset disabled">Reset</a>
        </div>
    </div>

    <div id="button-container" class="row">
        <div class="large-12 small-centered columns">
            <label>Modifica Nero: +/- <span class="mod-val">5</span>%</label>
            <a id="SPlus" href="#" class="button disabled">+</a>
            <a id="SMins" href="#" class="button disabled">-</a>
            <a id="SReset" href="#" class="button button-reset disabled">Reset</a>
        </div>
    </div>    
    
    <div class="row">
        <div class="large-10 small-centered columns">
            <a id="save" href="#" class="button">REGISTRA DATI</a>
        </div>
    </div>       

</div>

<div id="demoCanvas" class="small-10 columns"></div>


<div id="tooltip" data-alert class="alert-box radius"></div>

<ol class="joyride-list" data-joyride>
  <li data-text="Prossimo" data-options="tip_animation:fade">
    <h4>Istruzioni</h4>
    <p>Il compito consiste nel fare apparire i cinque settori del disco, 
       diversamente colorati, sullo stesso piano, ovvero tutti alla stessa 
       distanza dall'osservatore (nessuno deve apparire pi&ugrave; vicino o 
       lontano degli altri).<br/><br/>
       Se visivamente le sembrerebbe possibile infilare un coltello sotto uno 
       dei settori, vuol dire che quel settore appare davanti agli altri 
       e che di conseguenza va aggiustato.<br/>
    </p>
  </li>
  <li data-text="Prossimo" data-options="tip_location:bottom;tip_animation:fade">
    <h4>Primo Passo</h4>
    <p>Seleziona un settore adiacente al colore attivo per iniziare a effettuare delle mofiche</p>
  </li>
  <li data-id="selected-value-container" data-button="Prossimo" data-options="tip_location:bottom;tip_animation:fade">
    <h4>Selezione Effettuata</h4>
    <p>Qui vedrai comparire il colore dello spicchio selezionato e vedrai cambiare<br/>
        colore anche allo spicchio passando da grigio alla sua tonalità reale</p>    
  </li>
  <li data-id="slider-container" data-button="Prossimo" data-options="tip_location:bottom;tip_animation:fade">
    <h4>Cambia i valori</h4>
    <p>Modifica la slide per aumentare o diminuire l'effetto di modifica<br/>
  </li>
  <li data-id="button-container" data-button="Prossimo" data-options="tip_location:bottom;tip_animation:fade">
    <h4>Modifica i colori</h4>
    <p>Usa poi i tasti + e - per aumentare o diminuire.<br/>
        Puoi usare il tasto reset per ripristinare il colore iniziale</p>
  </li>  
  <li data-id="save" data-button="Prossimo" data-options="tip_animation:fade">
    <h4>Salva i dati</h4>
    <p>Ricordati di salvare i dati per terminare la sessione dell'esperimento!</p>
  </li>
</ol>

<script src="<?php echo $this->basePath; ?>assets/js/foundation/foundation.joyride.js"></script>
<script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/easeljs-0.7.0.min.js"></script>        
<script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/colors/colors.js"></script>    
<script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/colors/colors.matrix.js"></script>   
<script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/colors/colors.color.js"></script>   
<script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/colors/display/colors.chessboard.js"></script>    
<script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/colors/display/colors.clove.js"></script>    
<script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/colors/display/colors.Pointer.js"></script>          
<script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/colors/display/colors.palette.js"></script>                
<script>

    $(document).foundation();
    $(document).foundation('joyride', 'start');

    //create app namespace
    window.app = window.app || {
        modValue: 5
    };

   function init(values){

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

        app.colors = [];
        var colorArray = <?php echo json_encode($this->colors); ?>;
        for(var i = 0; i < colorArray.length; i++){
            var color = new colors.Color(colorArray[i].lab);
            if(colorArray[i].final){ 
                color.final = true;
                color.active = true;
            }
            color.labMax = colorArray[i].max;
            color.labMin = colorArray[i].min;
            app.colors.push(color);
        }

        //    
        app.palette = new colors.Palette();            
        app.palette.colors = app.colors;            
        app.palette.x = Math.round(app.screenWidth / 200) * 100;
        app.palette.y = Math.round(app.screenHeight / 200) * 100;
        app.palette.angle = <?php echo $this->angle ?>;
        app.palette.drawColorPalette(200);
        app.palette.addOnSelectCloveListener({
            onCloveSelected: function(clove){
                select(clove);
                app.stage.update();
            },
            onCloveSelectedError: function(error){
                if(error == "final"){
                    tooltip("Questo &egrave; il colore di riferimento e non pu&ograve; essere modificato", "warning");
                } else {                        
                    tooltip("Devi selezionare un colore adiacente a un colore attivo", "info");
                }
            }   
        })

        app.stage.addChild(app.palette);                           
        app.stage.update();
    }


    function select(clove){
        updateControls(clove);
        
        if(clove){         
            var color = clove.color;   
            color.active = true;
            clove.updateClove();
            var colorText = clove.color;
            if(clove.color instanceof colors.Color){
                colorText = clove.color.toString();
                color = clove.color.cssRGB();
            }                                            
            $('#selected-value').html("<span "+
                "style=\"background-color:"+color+"\" "+
                "class=\"colore\">"+
            "</span>"+colorText);
        } else {             
            
            $('#selected-value').html("<span>"+
                "Nessuno"+
            "</span>");
        }
    }
    
    function updateControls(clove){
    
        if(!clove){              
            $('#WPlus').addClass("disabled");
            $('#WMins').addClass("disabled");
            $('#WReset').addClass("disabled");
            $('#SPlus').addClass("disabled");
            $('#SMins').addClass("disabled");            
            $('#SReset').addClass("disabled");         
            return;
        }
        
        if(clove.color.alphaW == undefined ||
           clove.color.alphaS == undefined) {            
            if(!clove.color.alphaW){
                $('#WPlus').removeClass("disabled");
                $('#WMins').addClass("disabled");
                $('#WReset').addClass("disabled");
            }
            if(!clove.color.alphaS){
                $('#SPlus').removeClass("disabled");
                $('#SMins').addClass("disabled");            
                $('#SReset').addClass("disabled");         
            }
            return;
        }
            
        if(clove.color.alphaW == 1){
            $('#SPlus').removeClass("disabled");
        } else {
            $('#SPlus').addClass("disabled");
            $('#SMins').addClass("disabled");
            $('#SReset').addClass("disabled");
            //enable other button
            $('#WMins').removeClass("disabled");
            $('#WReset').removeClass("disabled");
        }

        if(clove.color.alphaS == 1){
            $('#WPlus').removeClass("disabled");
        } else {
            $('#WPlus').addClass("disabled");
            $('#WMins').addClass("disabled");
            $('#WReset').addClass("disabled");
            //enable other button
            $('#SMins').removeClass("disabled");
            $('#SReset').removeClass("disabled");
        }
    }
    

    function update(values){
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
        app.palette.x = Math.round(app.screenWidth / 200) * 100;
        app.palette.y = Math.round(app.screenHeight / 200) * 100;
        app.stage.addChild(app.palette);   
        app.stage.update();
    }

    function getValues(){
        app.parametes.num = $("#segment-num").val() || 5;
        app.parametes.sinus = $("#sinus-num").val() || 4;
        app.parametes.amplitude = $("#amplitude").val() || 3;            
        return app.parametes;
    }           

    function tooltip(msg, cls){
        if(app.tooltip.previus){
            $('#tooltip').removeClass(app.tooltip.previus);
        }
        $('#tooltip').addClass(cls);
        app.tooltip.previus = cls;
        $('#tooltip').html(msg);
        $('#tooltip').fadeIn();    
        setTimeout(function(){
            $('#tooltip').fadeOut();        
        }, 3000)
    }  

    $(document).ready(function(){

        app.parametes = {}
        app.tooltip = {};

        init(app.parametes); //initialize 

        $('#WPlus').on('click', function(event){
            event.preventDefault();
            if($(this).hasClass("disabled")){
                return;
            }
            
            var clove = app.palette.getSelectedClove();
            if(clove !== null){
                var color = clove.color;
                if(!color.hasFilter(color.filters.BlackWhite)){
                    color.addFilter(color.filters.BlackWhite);
                    console.info(clove);
                }
                color.applyFilter("increaseWhite", app.modValue);
                select(clove);
                app.stage.update();

            } else {
                tooltip("devi selezionare un segmento per modificarlo", "info");
            }
        });

        $('#WMins').on('click', function(event){
            event.preventDefault();
            if($(this).hasClass("disabled")){
                return;
            }
            
            var clove = app.palette.getSelectedClove();
            if(clove !== null){
                var color = clove.color;
                if(!color.hasFilter(color.filters.BlackWhite)){
                    color.addFilter(color.filters.BlackWhite);
                }
                color.applyFilter("decreaseWhite", app.modValue);
                select(clove);
                app.stage.update();

            } else {
                tooltip("devi selezionare un segmento per modificarlo", "info");
            }  
        });


        $('#WReset').on('click', function(event){
            event.preventDefault();
            if($(this).hasClass("disabled")){
                return;
            }
            
            var clove = app.palette.getSelectedClove();
            if(clove !== null){
                var color = clove.color;
                if(color.hasFilter(color.filters.BlackWhite)){
                    color.removeFilter(color.filters.BlackWhite);
                }

                select(clove);
                app.stage.update();       

            } else {
                tooltip("devi selezionare un segmento per modificarlo", "info");
            }  
        });


        $('#SPlus').on('click', function(event){
            event.preventDefault();
            if($(this).hasClass("disabled")){
                return;
            }
            
            var clove = app.palette.getSelectedClove();
            if(clove !== null){
                var color = clove.color;
                if(!color.hasFilter(color.filters.BlackWhite)){
                    color.addFilter(color.filters.BlackWhite);
                }
                color.applyFilter("increaseBlack", app.modValue);
                select(clove);
                app.stage.update();

            } else {
                tooltip("devi selezionare un segmento per modificarlo", "info");
            } 
        });

        $('#SMins').on('click', function(event){
            event.preventDefault();
            if($(this).hasClass("disabled")){
                return;
            }
            
            var clove = app.palette.getSelectedClove();
            if(clove !== null){
                var color = clove.color;
                if(!color.hasFilter(color.filters.BlackWhite)){
                    color.addFilter(color.filters.BlackWhite);
                }
                color.applyFilter("decreaseBlack", app.modValue);
                select(clove);
                app.stage.update();

            } else {
                tooltip("devi selezionare un segmento per modificarlo", "info");
            }  
        });


        $('#SReset').on('click', function(event){
            event.preventDefault();
            if($(this).hasClass("disabled")){
                return;
            }
            
            var clove = app.palette.getSelectedClove();
            if(clove !== null){
                var color = clove.color;
                if(color.hasFilter(color.filters.BlackWhite)){
                    color.removeFilter(color.filters.BlackWhite);
                }

                select(clove);
                app.stage.update();; 

            } else {
                tooltip("devi selezionare un segmento per modificarlo", "info");
            }  
        });   
        
        $( "#slider" ).slider({
            range: "max",
            min: 1,
            max: 10,
            value: 5,
            slide: function( event, ui ) {
              $(this).attr('title', ui.value);
              $('.mod-val').html(ui.value);
              app.modValue = +(ui.value);
            }
          });
        
        
        $('#save').on('click', function(event){
            event.preventDefault();
            if($(this).hasClass("disabled")){
                return;
            }
            
            var colors = [];
            for(var i = 0; i < app.colors.length; i++){                
                colors.push({
                    lab: app.colors[i].lab,
                    labMin: app.colors[i].labMin,
                    labMid: app.colors[i].labMid,
                    labMax: app.colors[i].labMax
                })
            }
            
            $.post('<?php echo $this->basePath ?>testWS/save', {colors: JSON.stringify(colors)}, 
                function(data, textStatus, jqXHR){
                    $('#save').removeClass("disabled");
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
            })
        });   


        $( window ).resize(function(event) {
            event.preventDefault();
            $("#demoCanvasStage").remove();
            update(app.parameters);
        });
        
    });         

</script>