<?php 
$this->sections->title = "Login";
?>        
<style>
    #login-form {top: 35%;position: relative;padding-top: 70px;}
    #login-form-title {position: absolute; top: 0px; left: 0px; margin: 0; width: 100%;}
</style>
<div id="page-background">
    <form id="login-form" class="large-4 panel autocenter " action="<?php echo $this->basePath ?>login/submit">
        <nav id="login-form-title" class="top-bar" data-topbar>
            <ul class="title-area">
              <li class="name">
                <h1><a href="#">NUOVA SESSIONE</a></h1>
              </li>
            </ul>
        </nav>

        <div class="row">
          <div class="columns">
            <input name="session" type="text" placeholder="inserisci il codice della sessione" 
                   value="<?php echo (isset($this->params['session']) ? $this->params['session'] : '') ?>"
            />
          </div>
        </div>      

        <div class="row">
          <div class="small-4 columns large-centered">
            <input class="button small-12" type="submit" value="Avvia"/>
          </div>
        </div>          

    </form>
</div>