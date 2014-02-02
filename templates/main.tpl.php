<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
        $(document).foundation({});        
        $(document).ready(function(){
            $.pinger({interval: 60, url: "/default/ping", callback: function(data){
                console.info(data);
            }});
        });
    </script>
</head>
<body>    
    <nav id="navbar" class="top-bar" data-topbar>
        <ul class="title-area">
          <li class="name">
            <h1><a href="<?php echo $this->basePath ?>">Esperimento Colori</a></h1>
          </li>                   
        </ul>
        
        <section class="top-bar-section">
          <!-- Right Nav Section -->
          <ul class="right">
              <?php if(isset($_SESSION['sessionName'])): ?>
                <li class="name">
                  <h1><a id="logout" href="<?php echo $this->basePath ?>login/logout">Logout</a></h1>
                </li>          
              <?php endif; ?>
              <?php if($this->infoEnabled): ?>
                <li class="name">
                    <h1><a id="info" href="#" onclick="$(document).foundation('joyride', 'start');">Info</a></h1>
                </li>    
              <?php endif; ?>
          </ul>
        </section>      
             
    </nav>
    
    <div id="page" class="row">
        <?php echo $this->sections->body ?>        
    </div>  
    
    <div id="tooltip" data-alert class="alert-box radius"></div>    
</body>
</html>