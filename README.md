# OpenContent - Stanza Del Cittadino (```ocsdc```)

## Applicazione per la gestione delle istanze on-line dei cittadini

La Stanza del Cittadino (```ocsdc```) è un'applicazione web basata sul framework [Symfony](https://github.com/symfony/symfony) versione 3.1
che facilita l'erogazione di servizi online

## Requisiti

* [Composer](https://getcomposer.org/)
* PHP versione 7 o superiore
* Webserver Apache (con mod_shibboleth)
* Database PostgreSql versione 9.4
* Ghostscript
* wkhtmltopdf
* npm, gulp, bower

## Installazione

```
git clone https://github.com/OpencontentCoop/stanzadelcittadino stanzadelcittadino
cd stanzadelcittadino
composer install
```

## Test
Per i test funzionali eseguire il seguente comando:
```
php phpunit -d memory_limit=2G --bootstrap stanzadelcittadino/var/bootstrap.php.cache --configuration stanzadelcittadino/phpunit.xml.dist stanzadelcittadino/tests --teamcity
```

## Ricevere aiuto
* Per segnalare malfunzionamenti utilizzare la funzionalità GitHub Issues di questo repository
* Per richiedere l'assistenza di uno sviluppatore scrivere a info@opencontent.it

## Copyright

Copyright (C) 2010-2017 Opencontent SCARL. Tutti i diritti riservati.

## Licenza
http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2.0

## Codice etico
La Stanza del Cittadino è un'applicazione Open Source. Il codice sorgente viene rilasciato con licenza GNU General Public License v2.0 per due ragioni: 
* il modello di business di [Opencontent](https://www.opencontent.it/Chi-siamo) ed il suo codice etico aziendale, che identifica nella condivisione del sapere un fattore determinante per migliorare costantemente la qualità e la competitività aziendale
* la tutela degli utenti ed in particolare degli enti pubblici, che possono più facilmente rispettare quanto previsto dall'Art. 68 comma 1-ter e dal Piano Triennale per l'informatica nella Pubblica Amministrazione.


#### Codice dell'Amministrazione Digitale - Art. 68.  Analisi comparativa delle soluzioni 

In vigore dal 14 settembre 2016 

1  Le pubbliche amministrazioni acquisiscono programmi informatici o parti di essi nel rispetto dei princìpi di economicità e di efficienza, tutela degli investimenti, riuso e neutralità tecnologica, a seguito di una valutazione comparativa di tipo tecnico ed economico tra le seguenti soluzioni disponibili sul mercato:
a)  software sviluppato per conto della pubblica amministrazione; 
b)  riutilizzo di software o parti di esso sviluppati per conto della pubblica amministrazione; 
c)  software libero o a codice sorgente aperto; 
d)  software fruibile in modalità cloud computing; 
e)  software di tipo proprietario mediante ricorso a licenza d'uso; 
f)  software combinazione delle precedenti soluzioni.

1-bis  A tal fine, le pubbliche amministrazioni prima di procedere all'acquisto, secondo le procedure di cui al codice di cui al decreto legislativo 12 aprile 2006 n. 163, effettuano una valutazione comparativa delle diverse soluzioni disponibili sulla base dei seguenti criteri:
a)  costo complessivo del programma o soluzione quale costo di acquisto, di implementazione, di mantenimento e supporto; 
b)  livello di utilizzo di formati di dati e di interfacce di tipo aperto nonché di standard in grado di assicurare l’interoperabilità e la cooperazione applicativa tra i diversi sistemi informatici della pubblica amministrazione;
c)  garanzie del fornitore in materia di livelli di sicurezza, conformità alla normativa in materia di protezione dei dati personali, livelli di servizio tenuto conto della tipologia di software acquisito. 

#### 1-ter  Ove dalla valutazione comparativa di tipo tecnico ed economico, secondo i criteri di cui al comma 1-bis, risulti motivatamente l'impossibilità di accedere a soluzioni già disponibili all'interno della pubblica amministrazione, o a software liberi o a codici sorgente aperto, adeguati alle esigenze da soddisfare, è consentita l'acquisizione di programmi informatici di tipo proprietario mediante ricorso a licenza d'uso. 

## Autori e contributori
 * Marco Albarelli [marcoalbarelli](https://github.com/marcoalbarelli)
 * Raffaele Luccisano [coppo](https://github.com/coppo)
 * Luca Realdi [lrealdi](https://github.com/lrealdi)
