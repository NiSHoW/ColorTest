<?php 
$this->sections->title = "Seleziona Test";
?>        
<style>.max-height{max-height: 377px; overflow: auto;}</style>
<div id="page-background">
    <div class="top-spaced">
        <div class="row">

            <div class="large-5 small-push-1 columns">
                <div class="panel">
                    <h3>TEST BIANCO NERO</h3>
                    <p class="max-height">
                    Istruzioni per il partecipante da scrivere a sinistra (al posto delle barre di scorrimento, che compariranno dopo che il partecipante avrà segnalato [il come sarà deciso da Corradini] di averle lette)<br/>
                    Il compito consiste nel fare apparire i cinque settori del disco, diversamente colorati, sullo stesso piano, ovvero tutti alla stessa distanza dall’osservatore (nessuno deve apparire più vicino o lontano degli altri).<br/>
                    Se visivamente le sembrerebbe possibile infilare un coltello sotto uno dei settori, vuol dire che quel settore appare davanti agli altri e che di conseguenza va aggiustato.<br/>
                    Usando la barra laterale, e rendendo i colori più chiari o più scuri, si può farli apparire più vicini o più lontani dall’osservatore. Il compito richiede quindi di regolare la barra in modo continuo, o a salti piccoli (cliccando sulle freccette nere laterali) o grandi (cliccando sugli spazi bianchi della barra), sinché ogni settore appaia sullo stesso piano degli atri. <br/>
                    […  Si consiglia di spostare la barra inzialmente con movimenti lenti e ampi in modo da vedere il settore andare avanti e indietro, così da rendersi conto di quale possa essere una posizione coplanare da raggiungere con aggiustamenti più fini…]   questa parte può essere spiegata e fatta vedere nel training iniziale.<br/>
                    Prima di finire e salvare i risultati, osservare in modo globale tutto il disco e vedere se qualche settore sporge in avanti o va indietro rispetto gli altri. In questo caso aggiustare ancora il colore o i colori che abbiano bisogno di essere spostati in avanti o indietro.  <br/>
                    </p>
                    <div class="row text-center">
                        <?php if($this->statusTestWS == 'complete'): ?>
                            <a href="<?php echo $this->basePath ?>testWS/exportFile" target="_blank" class="button">ESPORTA RISULTATO</a>            
                        <?php else: ?>
                            <a href="<?php echo $this->basePath ?>testWS" class="button">INIZIA</a>            
                        <?php endif; ?>        
                    </div>
                </div>
            </div>

            <div class="large-5 small-pull-1 columns">
                <div class="panel">
                    <h3>TEST LUMINOSITA'</h3>
                    <p class="max-height">Descrizione Test luminosità</p>
                    <div class="row text-center">
                        <?php if($this->statusTestL == 'complete'): ?>
                              <a href="<?php echo $this->basePath ?>testL/exportFile" target="_blank" class="button">ESPORTA RISULTATO</a>              
                        <?php else: ?>
                            <a href="<?php echo $this->basePath ?>testL" class="button">INIZIA</a>            
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>          
</div>