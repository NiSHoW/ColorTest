<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo $this->sections->title ?></title>    
    <link rel="stylesheet" href="<?php echo $this->basePath ?>assets/css/normalize.css" />
    <link rel="stylesheet" href="<?php echo $this->basePath ?>assets/css/foundation.css" />      
    <link rel="stylesheet" href="<?php echo $this->basePath ?>assets/css/jquery-ui.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo $this->basePath ?>assets/css/style.css" />    
    <script src="<?php echo $this->basePath ?>assets/js/vendor/modernizr.js"></script>    
    <script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/modernizr.js"></script>    
    <script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/vendor/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/vendor/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/plugins/jquery.jsize.js"></script>
    <script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/vendor/fastclick.js"></script>
    <script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/foundation.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->basePath ?>assets/js/ping.js"></script>
    <script src="<?php echo $this->basePath ?>assets/js/jquery-ui.min.js"></script>
    <script type="text/javascript">            
        function tooltip(msg, cls, fadeout){
            if(fadeout !== 0)
                fadeout = fadeout || 3000;
            if(app.tooltip.previus){
                $('#tooltip').removeClass(app.tooltip.previus);
            }
            $('#tooltip').addClass(cls);
            app.tooltip.previus = cls;
            $('#tooltip').html(msg);
            $('#tooltip').fadeIn();   
            console.info(fadeout);
            if(fadeout > 0){
                setTimeout(function(){
                    $('#tooltip').fadeOut();        
                }, 3000)
            }
        }  
        
    </script>
</head>
<body class="antialiased hide-extras">    
    <nav id="navbar" class="top-bar fixed" data-topbar data-options="is_hover: false">
        <ul class="title-area">
          <li class="name">
            <h1><a href="<?php echo $this->basePath ?>">Esperimento Colori</a></h1>
          </li>                   
          <li class="toggle-topbar menu-icon"><a href="#">Menu</a></li>
        </ul>
        
        <section class="top-bar-section">
          <!-- Right Nav Section -->
          <ul class="right">
              <?php if(isset($_SESSION['sessionName'])): ?>
                <li><a id="logout" href="<?php echo $this->basePath ?>login/logout">Logout</a></li>          
              <?php endif; ?>
              <?php if($this->infoEnabled): ?>
                <li class="divider"></li>
                <li><a id="info" href="#" onclick="$(document).foundation('joyride', 'start');">Info</a></li>    
              <?php endif; ?>
          </ul>
        </section>                   
    </nav>    
    
    <div id="page" class="row">
        <?php echo $this->sections->body ?>        
    </div>  
    
    <div id="tooltip" class="alert-box radius"></div>    
    <script type="text/javascript">
        $(document).foundation({});       
        $(document).ready(function(){
            $.pinger("init", {
                interval: 2, 
                url: "<?php echo $this->basePath ?>default/ping", 
                listen: null,
                callback: function(data){
                    console.info("PONG");
                    try{
                        var response = JSON.parse(data);
                        if(response.status == false){
                            tooltip("La sua sessione &egrave; scaduta. Contatti l'amministatore per procedere.", "alert", 0);
                        }
                    }catch(ex){}
                }
            });
        });
    </script>
</body>
</html>