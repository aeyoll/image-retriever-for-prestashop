# Image Retriever for PrestaShop

This module helps you generate thumbnails and webp/avif alternatives for images inside a custom module.

Requirements
---

PHP 8.1+ is needed to use this module.

Installation
---

### PrestaShop 8.1+

For PrestaShop 8.1+, require the plugin with Composer using the following command:

```sh
composer require aeyoll/image_retriever
```

```json
{
    "name": "project-name/project-name",
    "require": {
        "aeyoll/image_retriever": "dev-main",
        "composer/installers": "^1.0.21"
    },
    "config": {
        "allow-plugins": {
            "composer/installers": true
        },
        "sort-packages": true
    },
    "minimum-stability": "dev"
}
```

Usage
---

Let's assume you have an image file in a module, located at `modules/your_module/img/test.jpg`.

```php
<?php

use PrestaShop\Module\ImageRetriever\Service\ImageRetrieverService;

$irs = new ImageRetrieverService();
$irs->getImage(_PS_MODULE_DIR_ . '/your_module/img/', 'test.jpg');
```
