<?php

$sLangName  = "Deutsch";

$aLang = array(
    'charset'                     => 'UTF-8',
    'toxid_setup'                 => 'TOXID Configuration',
    'toxid_setup_main'            => 'TOXID Basic settings',
    'TOXID_SUPPORT_HEADLINE'      => 'Support the TOXID project',
    'TOXID_SUPPORT_DESC'          => 'TOXID cURL is an OpenSource project initially developed by Joscha Krug.
                                      The ongoing development is managed by his company
                                      <b><a href="http://www.marmalade.de/?pk_campaign=toxidBackend&pk_kwd=Textlink" target="_blank">marmalade GmbH</a></b>.
                                      All kind of support is welcome best by<b><a href="https://github.com/jkrug/TOXID-cURL/" target="_blank">code</a></b>
                                      but also by donations to maintain this project or implement new features.',
    'TOXID_SOURCE'                => 'CMS URL',
    'TOXID_SOURCE_SSL'            => 'CMS SSL-URL',
    'TOXID_SEO_SNIPPET'           => 'URL Identifier / SEO-Snippet',
    'TOXID_SEARCH_URL'            => 'URL to call the searchpage (optional)',
    'TOXID_PARAM'                 => 'TOXID URL parameter',
    'TOXID_DONT_REWRITE'          => 'Don\'t rewrite the URLs - Users will linked to the external site.',
    'TOXID_DONT_PASSTHROUGH'      => '<b><u>Don\'t</u></b> pass POST parameters to CMS',
    'TOXID_LOOKING_FOR'           => 'Support us with PayPal!',
    'TOXID_GENERAL'               => 'General',
    'TOXID_BECOME_PARTNER'        => 'Become a partner',
    'TOXID_INTEGRATIONPARTNER'    => 'Integration partners',
    'TOXID_CACHE'                 => 'Cache',
    'TOXID_CACHE_ENABLED'         => 'Cache remote CMS pages locally',
    'TOXID_CACHE_SNIPPET_ENABLED' => 'Cache snippets locally',
    'TOXID_CACHE_TTL'             => 'Lifetime (TTL)',
    'TOXID_IN_SECONDS'            => 'seconds (e.g. 3600 = 1 hour)',
    'TOXID_LOGIN'                 => 'Access control login',
    'TOXID_PWD'                   => 'Access control password',
    'TOXID_DONT_REDIRECT'         => 'Do not redirect to shop’s home if request failed',
);

if (oxRegistry::getConfig()->getConfigParam('iUtfMode') === 0) {
    foreach ($aLang as $k=>$v) {
        $aLang[$k] = utf8_decode($v);
    }
}
