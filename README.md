# OpenContent - Stanza Del Cittadino (```ocsdc```)

### Applicazione per la gestione delle istanze on-line dei cittadini

La Stanza del Cittadino (```ocsdc```) è un'applicazione web basata sul framework [Symfony](https://github.com/symfony/symfony) versione 3.1
che facilita l'erogazione di servizi online

### Requisiti

* [Composer](https://getcomposer.org/)
* PHP versione 7 o superiore
* Webserver Apache (con mod_shibboleth)
* Database PostgreSql versione 9.4
* Ghostscript
* wkhtmltopdf
* npm, gulp, bower

### Installazione

```
git clone https://github.com/OpencontentCoop/stanzadelcittadino stanzadelcittadino
cd stanzadelcittadino
composer install
```

### Test
Per i test funzionali eseguire il seguente comando:
```
php phpunit -d memory_limit=2G --bootstrap stanzadelcittadino/var/bootstrap.php.cache --configuration stanzadelcittadino/phpunit.xml.dist stanzadelcittadino/tests --teamcity
```

### Ricevere aiuto
* Per segnalare malfunzionamenti utilizzare la funzionalità GitHub Issues di questo repository
* Per richiedere l'assistenza di uno sviluppatore scrivere a info@opencontent.it

### Licenza
Il codice sorgente di questo software è rilasciato con licenza GNU General Public License v2.0; l'applicazione è pertanto Open Source.
La scelta di questa licenza è dovuta a da due ragioni: 
* il modello di business di [Opencontent](https://www.opencontent.it/Chi-siamo) ed il suo codice etico aziendale, che identifica nella condivisione del sapere un fattore determinante per migliorare costantemente la qualità e la competitività aziendale
* la tutela degli utenti ed in particolare degli enti pubblici, che possono più facilmente rispettare quanto previsto dall'Art. 68 comma 1-ter e dal Piano Triennale per l'informativa nella Pubblica Amministrazione.


### Art. 68.  Analisi comparativa delle soluzioni 

In vigore dal 14 settembre 2016 

1.  Le pubbliche amministrazioni acquisiscono programmi informatici o parti di essi nel rispetto dei princìpi di economicità e di efficienza, tutela degli investimenti, riuso e neutralità tecnologica, a seguito di una valutazione comparativa di tipo tecnico ed economico tra le seguenti soluzioni disponibili sul mercato:

a)  software sviluppato per conto della pubblica amministrazione; 

b)  riutilizzo di software o parti di esso sviluppati per conto della pubblica amministrazione; 

c)  software libero o a codice sorgente aperto; 

d)  software fruibile in modalità cloud computing; 

e)  software di tipo proprietario mediante ricorso a licenza d'uso; 

f)  software combinazione delle precedenti soluzioni.

1-bis.  A tal fine, le pubbliche amministrazioni prima di procedere all'acquisto, secondo le procedure di cui al codice di cui al decreto legislativo 12 aprile 2006 n. 163, effettuano una valutazione comparativa delle diverse soluzioni disponibili sulla base dei seguenti criteri:

a)  costo complessivo del programma o soluzione quale costo di acquisto, di implementazione, di mantenimento e supporto; 

b)  livello di utilizzo di formati di dati e di interfacce di tipo aperto nonché di standard in grado di assicurare l’interoperabilità e la cooperazione applicativa tra i diversi sistemi informatici della pubblica amministrazione; 

c)  garanzie del fornitore in materia di livelli di sicurezza, conformità alla normativa in materia di protezione dei dati personali, livelli di servizio tenuto conto della tipologia di software acquisito. 

### 1-ter.  Ove dalla valutazione comparativa di tipo tecnico ed economico, secondo i criteri di cui al comma 1-bis, risulti motivatamente l'impossibilità di accedere a soluzioni già disponibili all'interno della pubblica amministrazione, o a software liberi o a codici sorgente aperto, adeguati alle esigenze da soddisfare, è consentita l'acquisizione di programmi informatici di tipo proprietario mediante ricorso a licenza d'uso. 

La valutazione di cui al presente comma è effettuata secondo le modalità e i criteri definiti dall'AgID.

[2.  Le pubbliche amministrazioni nella predisposizione o nell'acquisizione dei programmi informatici, adottano soluzioni informatiche, quando possibile modulari, basate sui sistemi funzionali resi noti ai sensi dell'articolo 70, che assicurino l'interoperabilità e la cooperazione applicativa e consentano la rappresentazione dei dati e documenti in più formati, di cui almeno uno di tipo aperto, salvo che ricorrano motivate ed eccezionali esigenze.]

[2-bis.  Le amministrazioni pubbliche comunicano tempestivamente a DigitPA l'adozione delle applicazioni informatiche e delle pratiche tecnologiche, e organizzative, adottate, fornendo ogni utile informazione ai fini della piena conoscibilità delle soluzioni adottate e dei risultati ottenuti, anche per favorire il riuso e la più ampia diffusione delle migliori pratiche.]

3.  Agli effetti del presente Codice si intende per:

a)  formato dei dati di tipo aperto, un formato di dati reso pubblico, documentato esaustivamente e neutro rispetto agli strumenti tecnologici necessari per la fruizione dei dati stessi; 

b)  dati di tipo aperto, i dati che presentano le seguenti caratteristiche:

1)  sono disponibili secondo i termini di una licenza che ne permetta l'utilizzo da parte di chiunque, anche per finalità commerciali, in formato disaggregato; 

2)  sono accessibili attraverso le tecnologie dell'informazione e della comunicazione, ivi comprese le reti telematiche pubbliche e private, in formati aperti ai sensi della lettera a), sono adatti all'utilizzo automatico da parte di programmi per elaboratori e sono provvisti dei relativi metadati; 

3)  sono resi disponibili gratuitamente attraverso le tecnologie dell'informazione e della comunicazione, ivi comprese le reti telematiche pubbliche e private, oppure sono resi disponibili ai costi marginali sostenuti per la loro riproduzione e divulgazione, salvo i casi previsti dall'articolo 7 del decreto legislativo 24 gennaio 2006, n. 36, e secondo le tariffe determinate con le modalità di cui al medesimo articolo. 

[4.  DigitPA istruisce ed aggiorna, con periodicità almeno annuale, un repertorio dei formati aperti utilizzabili nelle pubbliche amministrazioni e delle modalità di trasferimento dei formati.]


