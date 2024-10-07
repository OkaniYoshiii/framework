# FRAMEWORK PHP

## Initialiser le projet

`composer require okaniyoshii/framework`

Créer un dossier "public" à la racine du projet
Ajouter un fichier "index.php" et rajouter le code suivant :

```php
/* Fichier : public/index.php */
<?php

use OkaniYoshiii\Framework;

require_once '../vendor/autoload.php';

Webapp::init();
```

