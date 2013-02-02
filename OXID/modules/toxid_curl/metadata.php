<?php
/**
 * OXID_Module_TOXID
 *
 * PHP version 5
 *
 * @category TOXID
 * @package  TOXID
 * @author   Joscha Krug <support@marmalade.de>
 * @license  MIT License http://www.opensource.org/licenses/mit-license.html
 * @version  1.5.1
 * @link     http://toxid.org
 * @link     https://github.com/jkrug/TOXID-cURL
 */

$aModule = array(
    'id'          => 'toxid_curl',
    'title'       => 'marmalade :: TOXID cURL',
    'description' => array(
        'de'    => 'Integriert CMS-Inhalte in OXID eShop',
        'en'    => 'Renders pages form CMS and navigation in OXID.',
    ),
    'email'         => 'support@marmalade.de',
    'url'           => 'http://www.marmalade.de',
    'thumbnail'     => 'toxid.jpg',
    'version'       => '1.5.1',
    'author'        => 'marmalade GmbH :: Joscha Krug',
    'extend'        => array(
        'oxseodecoder' => 'toxid_curl/core/toxid_curl_oxseodecoder',
        'oxviewconfig' => 'toxid_curl/core/toxid_curl_oxviewconfig',
    ),
    'templates'     => array(
        'toxid_curl.tpl'    => 'toxid_curl/application/views/azure/toxid_curl.tpl',
        'product.tpl'       => 'toxid_curl/application/views/azure/product.tpl',
    ),
    'files'         => array(
        'toxid_curl'                => 'toxid_curl/application/controller/toxid_curl.php',
        'toxid_curl_oxseodecoder'   => 'toxid_curl/core/toxid_curl_oxseodecoder.php',
        'toxid_curl_oxviewconfig'   => 'toxid_curl/core/toxid_curl_oxviewconfig.php',
        'toxidcurl'                 => 'toxid_curl/core/toxidcurl.php',
    )
);