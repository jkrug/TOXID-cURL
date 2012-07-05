<?php
/**
 * OXID_Module_TOXID
 *
 * PHP version 5
 *
 * @category TOXID
 * @package  TOXID
 * @author   Joscha Krug <krug | at | marmalade.de>
 * @license  MIT License http://www.opensource.org/licenses/mit-license.html
 * @version  GIT: <$Id$>
 * @link     http://toxid.org
 * @link.....https://github.com/jkrug/TOXID-cURL
 */

$aModule = array(
            'id'          => 'toxid_curl',
            'title'       => 'TOXID cURL',
            'description' => 'Renders CMS-pages and Navigation in OXID.',
            'thumbnail'   => 'toxid.jpg',
            'version'     => '1.4.0',
            'author'      => 'GN2 netwerk',
            'extend'      => array(
                              'oxseodecoder' => 'toxid_curl/core/toxid_curl_oxseodecoder',
                              'oxviewconfig' => 'toxid_curl/core/toxid_curl_oxviewconfig',
                             )
           );