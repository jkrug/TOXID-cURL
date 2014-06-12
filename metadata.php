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
 * @version  2.0
 * @link     http://toxid.org
 * @link     https://github.com/jkrug/TOXID-cURL
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

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
    'version'       => '2.0',
    'author'        => 'marmalade GmbH :: Joscha Krug',
    'extend' => array(
        'oxseodecoder'              => 'toxid_curl/core/toxid_curl_oxseodecoder',
        'oxviewconfig'              => 'toxid_curl/core/toxid_curl_oxviewconfig',
        'oxutilsview'               => 'toxid_curl/core/toxid_curl_oxutilsview'
    ),
    'templates' => array(
        'toxid_curl.tpl'            => 'toxid_curl/views/azure/toxid_curl.tpl',
        'product.tpl'               => 'toxid_curl/views/azure/product.tpl',
        'toxid_setup_main.tpl'      => 'toxid_curl/views/admin/tpl/toxid_setup_main.tpl',
    ),
    'files' => array(
        'toxid_curl'                => 'toxid_curl/controller/toxid_curl.php',
        'toxid_curl_oxseodecoder'   => 'toxid_curl/core/toxid_curl_oxseodecoder.php',
        'toxid_curl_oxviewconfig'   => 'toxid_curl/core/toxid_curl_oxviewconfig.php',
        'toxidcurl'                 => 'toxid_curl/core/toxidcurl.php',
        'toxid_setup'               => 'toxid_curl/controller/admin/toxid_setup.php',
        'toxid_setup_main'          => 'toxid_curl/controller/admin/toxid_setup_main.php',
        'toxid_setup_list'          => 'toxid_curl/controller/admin/toxid_setup_list.php',
        'toxid_curl_events'         => 'toxid_curl/core/toxid_curl_events.php',
        'toxid_curl_smarty_parser'  => 'toxid_curl/core/facades/toxid_curl_smarty_parser.php',
    ),
    'settings' => array(
        array(
            'group' => 'toxid_config_not_here',
            'name'  => 'noConfigHere',
        ),
    ),
    'events' => array(
        'onActivate'    => 'toxid_curl_events::onActivate',
        'onDeactivate'  => 'toxid_curl_events::onDeactivate'
    )
);
