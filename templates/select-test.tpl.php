<?php 
$this->sections->title = "Seleziona Test";
?>        
<style>
    html, body{position: relative;}
    .max-height{max-height: 377px; overflow: auto;}
</style>
<div id="page-background"></div>
<div class="top-spaced"></div>     

<div class="row">

    <div class="large-10 small-10 large-push-1 small-push-1">
        <div class="panel">
            <p>L'esperimento analizza il rapporto tra colore e forma e in particolare il ruolo giocato dal 
            colore nella percezione di profondit&agrave; delle superfici visive.</p>
            <p>Il compito dei partecipanti &egrave; di modificare il colore di quattro quadranti di un cerchio, 
            facendo uso delle barre laterali, in modo che tutti appaiano complanari, ovvero 
            nessuno "sopra" o "sotto" un altro, o pi&ugrave; vicino o pi&ugrave; lontano dall'osservatore degli altri.</p>
            <p>Scegliere a caso uno dei due esperimenti</p>
        </div>
    </div>

    <div class="large-10 small-10 large-push-1 small-push-1">
        <div class="panel">
            <div class="row">   
                <?php if($this->statusTestWS !== 'complete'): ?>
                    <div class="large-10 medium-9 columns">
                        <h5>Esperimento F</h5>
                        <p></p>
                    </div>
                    <div class="large-2 medium-3 columns">
                        <a href="<?php echo $this->basePath ?>testWS" class="button autocenter block">INIZIA</a>            
                    </div>
                <?php else: ?>
                <div class='columns large-12 small-12'>
                    <div class="large-12 small-12">
                        <h5>Esperimento F</h5>
                        <p></p>
                    </div>
                    <div class="large-12 small-12 output">
                        <label>Dati registrati</label>
                        <pre><?php echo $this->outputTestWS ?></pre>
                    </div>                
                </div>                    
                <?php endif; ?>                    
            </div>                        
        </div>
    </div>

    <div class="large-10 small-10 large-push-1 small-push-1">
        <div class="panel">
            <div class="row">    
                <?php if($this->statusTestL !== 'complete'): ?>                    
                    <div class="large-10 medium-9 columns">
                        <h5>Esperimento 6</h5>
                        <p></p>
                    </div>
                    <div class="large-2 medium-3 columns">
                        <a href="<?php echo $this->basePath ?>testL" class="button autocenter block">INIZIA</a>            
                    </div>
                <?php else: ?>
                <div class='columns large-12 small-12'>
                    <div class="large-12 small-12">
                        <h5>Esperimento 6</h5>
                        <p></p>
                    </div>
                    <div class="large-12 small-12 output">                        
                        <label>Dati registrati</label>
                        <pre><?php echo $this->outputTestL ?></pre>
                    </div>         
                </div>                      
                <?php endif; ?>                        
            </div>                        
        </div>
    </div> 

    <?php if($this->statusTestL == 'complete' && $this->statusTestWS == 'complete'): ?>
        <div class="large-10 small-10 large-push-1 small-push-1">
            <div class="panel">
                <div class="row">    
                    <div class="large-10 columns">
                        <h5>Grazie</h5>
                        <p>Ha eseguito tutti gli esperimenti a disposizione, La ringraziamo e le chiediamo di procedere con il logout</p>
                    </div>
                    <div class="large-2 columns">
                        <a href="<?php echo $this->basePath ?>login/logout" class="button autocenter block">LOGOUT</a>            
                    </div>
                </div>                        
            </div>
        </div>
    <?php endif; ?>                  

</div>

