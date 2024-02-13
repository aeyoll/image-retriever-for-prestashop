<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];

foreach ($autoloadPaths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
    }
}

class Image_Retriever extends Module
{
    public function __construct()
    {
        $this->name = 'image_retriver';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'aeyoll';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Image Retriver', [], 'Modules.ImageRetriever.Admin');
        $this->description = $this->trans('Image Retriver for PrestaShop', [], 'Modules.ImageRetriever.Admin');

        $this->ps_versions_compliancy = ['min' => '8.1.0.0', 'max' => _PS_VERSION_];

        $this->errors = array();
    }
  }

