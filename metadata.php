<?php

/**
 * TOXID cURL - CMS Integration for OXID eShop
 *
 * Ported to OXID eShop 6.5+ with metadata 2.1, PSR-4 namespaces and PHP 8 compatibility.
 *
 * @license MIT License http://www.opensource.org/licenses/mit-license.html
 * @link    https://github.com/jkrug/TOXID-cURL
 */

$sMetadataVersion = '2.1';

$aModule = [
    'id'          => 'toxid_curl',
    'title'       => 'TOXID cURL',
    'description' => [
        'de' => 'Integriert CMS-Inhalte (WordPress, TYPO3 etc.) per cURL/XML in OXID eShop.',
        'en' => 'Renders CMS pages and navigation in OXID eShop via cURL/XML.',
    ],
    'thumbnail'   => 'toxid.jpg',
    'version'     => '3.0.0',
    'author'      => 'marmalade GmbH / Community',
    'url'         => 'https://github.com/jkrug/TOXID-cURL',
    'email'       => 'support@marmalade.de',

    'extend' => [
        \OxidEsales\Eshop\Core\ViewConfig::class  => \Toxid\Core\ViewConfig::class,
        \OxidEsales\Eshop\Core\SeoDecoder::class  => \Toxid\Core\SeoDecoder::class,
        \OxidEsales\Eshop\Core\UtilsView::class   => \Toxid\Core\UtilsView::class,
    ],

    'controllers' => [
        'toxid'            => \Toxid\Application\Controller\ToxidController::class,
        'toxid_setup'      => \Toxid\Application\Controller\Admin\ToxidSetup::class,
        'toxid_setup_main' => \Toxid\Application\Controller\Admin\ToxidSetupMain::class,
        'toxid_setup_list' => \Toxid\Application\Controller\Admin\ToxidSetupList::class,
    ],

    'templates' => [
        'page/toxid/toxid.tpl'       => 'toxid_curl/Application/views/tpl/page/toxid/toxid.tpl',
        'admin/toxid_setup_main.tpl' => 'toxid_curl/Application/views/admin/tpl/toxid_setup_main.tpl',
    ],

    'smartyPluginDirectories' => [
        'smarty/plugins',
    ],
];
