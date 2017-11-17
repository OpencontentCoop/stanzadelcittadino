# OpenContent - Stanza Del Cittadino (```ocsdc```)

### Applicazione per l'esposizione dei servizi online per la Pubblica Amministrazione

La Stanza del Cittadino (```ocsdc```) è un'applicazione web basata sul framework [Symfony](https://github.com/symfony/symfony) versione 3.1
che permette l'erogazione di servizi online

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
Il software è rilasciato con licenza GNU GPL come da file presente in repository

