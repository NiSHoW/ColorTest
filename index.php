<?php 
session_start();

//leggo i color
$colors = array();
$colorrows = file("colori_riferimento.txt");
foreach($colorrows as $i => $row){
    $lab = preg_split('/\s+/', $row);
    if(count($lab) >= 3){
        $c = $i % 3 ;
        if($c == 0){
            $color = array();
            if($i == 0){
                $color['final'] = true;
            }            
            $color['max'] = array('L' => $lab[0],'a' =>  $lab[1],'b' =>  $lab[2]);
        } elseif($c == 1){
            $color['lab'] = array('L' => $lab[0],'a' =>  $lab[1],'b' =>  $lab[2]);
        } else {
            $color['min'] = array('L' => $lab[0],'a' =>  $lab[1],'b' =>  $lab[2]);
            $colors[] = $color;    
        }
    }    
}

shuffle($colors);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Demo</title>    
    <link rel="stylesheet" href="assets/css/normalize.css" />
    <link rel="stylesheet" href="assets/css/foundation.css" />      
    <link type="text/css" rel="stylesheet" href="assets/css/style.css" />    
    <script src="assets/js/vendor/modernizr.js"></script>    
</head>
<body>    
    <nav id="navbar" class="top-bar" data-topbar>
        <ul class="title-area">
          <li class="name">
            <h1><a href="#">Esperimento Colori</a></h1>
          </li>
          <li class="toggle-topbar menu-icon"><a href="#">Menu</a></li>
        </ul>
    </nav>
    
    <div id="page" class="row">
        
        <div id="controls" class="small-2 columns panel">
            
            <div class="row">
                <div class="control small-2 large-12 columns">
                    <label>Selezionato:</label>
                    <div id="selected-value" class="alert-box radius secondary">
                        <span>Nessuno</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="large-12 columns">
                    <label>Modifica Bianco</label>
                    <a id="WPlus" href="#" class="button small-3">+</a>
                    <a id="WMins" href="#" class="button small-3">-</a>
                    <a id="WReset" href="#" class="button small-4 button-reset">Reset</a>
                </div>
            </div>
            
            <div class="row">
                <div class="large-12 columns">
                    <label>Modifica Nero</label>
                    <a id="SPlus" href="#" class="button small-3">+</a>
                    <a id="SMins" href="#" class="button small-3">-</a>
                    <a id="SReset" href="#" class="button small-4 button-reset">Reset</a>
                </div>
            </div>
            
            <div class="row">
                <div class="large-12 columns">
                    <label>Modifica Luminosit&agrave;</label>
                    <a id="LPlus" href="#" class="button small-3">+</a>
                    <a id="LMins" href="#" class="button small-3">-</a>
                    <a id="LReset" href="#" class="button small-4 button-reset">Reset</a>
                </div>
            </div>            
            
        </div>

        <div id="demoCanvas" class="small-10 columns"></div>
        
    </div>  
    
    <div id="tooltip" data-alert class="alert-box radius"></div>
    
    <script type="text/javascript" src="assets/js/modernizr.js"></script>    
    <script type="text/javascript" src="assets/js/vendor/jquery.js"></script>
    <script type="text/javascript" src="assets/js/vendor/jquery.cookie.js"></script>
    <script type="text/javascript" src="assets/js/plugins/jquery.jsize.js"></script>
    <script type="text/javascript" src="assets/js/vendor/fastclick.js"></script>
    <script type="text/javascript" src="assets/js/foundation.min.js"></script>
    <script type="text/javascript" src="assets/js/easeljs-0.7.0.min.js"></script>        
    <script type="text/javascript" src="assets/js/colors/colors.js"></script>    
    <script type="text/javascript" src="assets/js/colors/colors.matrix.js"></script>   
    <script type="text/javascript" src="assets/js/colors/colors.color.js"></script>   
    <script type="text/javascript" src="assets/js/colors/display/colors.chessboard.js"></script>    
    <script type="text/javascript" src="assets/js/colors/display/colors.clove.js"></script>    
    <script type="text/javascript" src="assets/js/colors/display/colors.Pointer.js"></script>          
    <script type="text/javascript" src="assets/js/colors/display/colors.palette.js"></script>                
    <script>
        
        $(document).foundation({ 
        });

        //create app namespace
        window.app = window.app || {
            rotateAngles: [36/180*Math.PI, 24/180*Math.PI, 18/180*Math.PI],
            selectedAngle: Math.floor(Math.random()*3)
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
            var colorArray = <?php echo json_encode($colors); ?>;
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
            console.info(app.rotateAngles[app.selectedAngle]);
            app.palette.angle = app.rotateAngles[app.selectedAngle];
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
                var clove = app.palette.getSelectedClove();
                if(clove !== null){
                    var color = clove.color;
                    if(!color.hasFilter(color.filters.BlackWhite)){
                        color.addFilter(color.filters.BlackWhite);
                        console.info(clove);
                    }
                    color.applyFilter("increaseWhite", 5);
                    select(clove);
                    app.stage.update();
                    
                    if(clove.color.alphaW == 1){
                        $('#SPlus').removeClass("disabled");
                        $('#SMins').removeClass("disabled");
                        $('#SReset').removeClass("disabled");
                    } else {
                        $('#SPlus').addClass("disabled");
                        $('#SMins').addClass("disabled");
                        $('#SReset').addClass("disabled");
                    }
                    
                } else {
                    tooltip("devi selezionare un segmento per modificarlo", "info");
                }
            });
            
            $('#WMins').on('click', function(event){
                event.preventDefault();
                var clove = app.palette.getSelectedClove();
                if(clove !== null){
                    var color = clove.color;
                    if(!color.hasFilter(color.filters.BlackWhite)){
                        color.addFilter(color.filters.BlackWhite);
                    }
                    color.applyFilter("decreaseWhite", 5);
                    select(clove);
                    app.stage.update();
                    
                    if(clove.color.alphaW == 1){
                        $('#SPlus').removeClass("disabled");
                        $('#SMins').removeClass("disabled");
                        $('#SReset').removeClass("disabled");
                    } else {
                        $('#SPlus').addClass("disabled");
                        $('#SMins').addClass("disabled");
                        $('#SReset').addClass("disabled");
                    }
                    
                } else {
                    tooltip("devi selezionare un segmento per modificarlo", "info");
                }  
            });
            
            
            $('#WReset').on('click', function(event){
                event.preventDefault();
                var clove = app.palette.getSelectedClove();
                if(clove !== null){
                    var color = clove.color;
                    if(color.hasFilter(color.filters.BlackWhite)){
                        color.removeFilter(color.filters.BlackWhite);
                    }
                    
                    select(clove);
                    app.stage.update();       
                    
                    $('#SPlus').removeClass("disabled");
                    $('#SMins').removeClass("disabled");
                    $('#SReset').removeClass("disabled");
                    
                } else {
                    tooltip("devi selezionare un segmento per modificarlo", "info");
                }  
            });
            
            
            $('#SPlus').on('click', function(event){
                event.preventDefault();
                var clove = app.palette.getSelectedClove();
                if(clove !== null){
                    var color = clove.color;
                    if(!color.hasFilter(color.filters.BlackWhite)){
                        color.addFilter(color.filters.BlackWhite);
                    }
                    color.applyFilter("increaseBlack", 5);
                    select(clove);
                    app.stage.update();
                    
                    if(clove.color.alphaS == 1){
                        $('#WPlus').removeClass("disabled");
                        $('#WMins').removeClass("disabled");
                        $('#WReset').removeClass("disabled");
                    } else {
                        $('#WPlus').addClass("disabled");
                        $('#WMins').addClass("disabled");
                        $('#WReset').addClass("disabled");
                    }
                    
                } else {
                    tooltip("devi selezionare un segmento per modificarlo", "info");
                } 
            });
            
            $('#SMins').on('click', function(event){
                event.preventDefault();
                var clove = app.palette.getSelectedClove();
                if(clove !== null){
                    var color = clove.color;
                    if(!color.hasFilter(color.filters.BlackWhite)){
                        color.addFilter(color.filters.BlackWhite);
                    }
                    color.applyFilter("decreaseBlack", 5);
                    select(clove);
                    app.stage.update();
                                        
                    if(clove.color.alphaS == 1){
                        $('#WPlus').removeClass("disabled");
                        $('#WMins').removeClass("disabled");
                        $('#WReset').removeClass("disabled");
                    } else {
                        $('#WPlus').addClass("disabled");
                        $('#WMins').addClass("disabled");
                        $('#WReset').addClass("disabled");
                    }
                    
                } else {
                    tooltip("devi selezionare un segmento per modificarlo", "info");
                }  
            });
            
            
            $('#SReset').on('click', function(event){
                event.preventDefault();
                var clove = app.palette.getSelectedClove();
                if(clove !== null){
                    var color = clove.color;
                    if(color.hasFilter(color.filters.BlackWhite)){
                        color.removeFilter(color.filters.BlackWhite);
                    }
                    
                    select(clove);
                    app.stage.update();; 
                    
                    $('#WPlus').removeClass("disabled");
                    $('#WMins').removeClass("disabled");
                    $('#WReset').removeClass("disabled");
                    
                } else {
                    tooltip("devi selezionare un segmento per modificarlo", "info");
                }  
            });
            
            
            
            $('#LPlus').on('click', function(event){
                event.preventDefault();
                var clove = app.palette.getSelectedClove();
                if(clove !== null){
                    var color = clove.color;
                    if(!color.hasFilter(color.filters.Lighting)){
                        color.addFilter(color.filters.Lighting);                
                    }
                    color.applyFilter("increaseLighting", 5);
                    select(clove);
                    app.stage.update();
                    
                } else {
                    tooltip("devi selezionare un segmento per modificarlo", "info");
                }
            });
            
            $('#LMins').on('click', function(event){
                event.preventDefault();
                var clove = app.palette.getSelectedClove();
                if(clove !== null){
                    var color = clove.color;
                    if(!color.hasFilter(color.filters.Lighting)){
                        color.addFilter(color.filters.Lighting);
                    }
                    color.applyFilter("decreaseLighting", 5);
                    select(clove);
                    app.stage.update();
                    
                } else {
                    tooltip("devi selezionare un segmento per modificarlo", "info");
                }  
            });      
            
            $('#LReset').on('click', function(event){
                event.preventDefault();
                var clove = app.palette.getSelectedClove();
                if(clove !== null){
                    var color = clove.color;
                    if(color.hasFilter(color.filters.Lighting)){
                        console.info("reset filter");
                        color.removeFilter(color.filters.Lighting);
                    }
                    
                    select(clove);
                    app.stage.update();
                } else {
                    tooltip("devi selezionare un segmento per modificarlo", "info");
                }  
            });
            
            
            
            $( window ).resize(function(event) {
                event.preventDefault();
                $("#demoCanvasStage").remove();
                update(app.parameters);
            });
        });         
 
   </script>
</body>
</html>