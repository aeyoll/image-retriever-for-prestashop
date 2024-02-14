# Image Retriever for PrestaShop

This module helps you generate thumbnails and webp/avif alternatives for images inside a custom module.

Requirements
---

PHP 7.1+ is needed to use this module.

Installation
---

### PrestaShop 8.0+

For PrestaShop 8.0+, require the plugin with Composer using the following command:

```sh
composer require aeyoll/image_retriever
```

Usage
---

Let's assume you have an image file in a module, located at `modules/your_module/img/test.jpg`.

```php
<?php

use PrestaShop\Module\ImageRetriever\Service\ImageRetrieverService;

$irs = new ImageRetrieverService();
$image = $irs->getImage(
    _PS_MODULE_DIR_ . 'your_module/img/', // The absolute path to the uploaded images folder
    'test.jpg', // The image filename
    ['home_default'] // Optional: generate only specific image types, otherwise generate every format
);
```

In your template:

```tpl
<picture>
    {if !empty($image.bySize.home_default.sources.avif)}
        <source srcset="{$image.bySize.home_default.sources.avif}" type="image/avif">
    {/if}

    {if !empty($image.bySize.home_default.sources.webp)}
        <source srcset="{$image.bySize.home_default.sources.webp}" type="image/webp">
    {/if}

    <img
        class="img-fluid"
        src="{$image.bySize.home_default.url}"
        alt=""
        width="{$image.bySize.home_default.width}"
        height="{$image.bySize.home_default.height}">
</picture>

