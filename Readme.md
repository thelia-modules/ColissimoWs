# Colissimo Ws

Module de livraison Colissimo avec génération des étiquettes d'expédition
et récupération des numéros de suivi via les Web Services Colissimo.

Bien veiller à régénérer l'auto-loader en production :

`composer dump-autoload -o `

La facture pour les douanes doit se trouver dans le fichier

templates/pdf/votre-site/customs-invoice.html

Si vous utilisez la facture par défaut, pensez-bien à faire les "traductions" de la facture adaptées 
à vos envois.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is ProcessAndInvoice.
* Activate it in your Thelia administration panel

### Composer

Add it in your main Thelia composer.json file

```
composer require thelia/process-and-invoice-module:~1.0.0
```