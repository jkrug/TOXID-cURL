<?php

$sLangName  = "Deutsch";

$aLang = array(
    'charset'                                      => 'UTF-8',
    'toxid_setup'                                  => 'TOXID Configuration',
    'toxid_setup_main'                             => 'TOXID Basic settings',
    'TOXID_SUPPORT_HEADLINE'                       => 'Support the TOXID project',
    'TOXID_SUPPORT_DESC'                           => 'TOXID cURL ist ein OpenSource Projekt, ursprünglich entwickelt von Joscha Krug. 
                                                       Die Entwicklung wird von seiner Agentur, der <b><a href="http://www.marmalade.de/?pk_campaign=toxidBackend&pk_kwd=Textlink" target="_blank">marmalade GmbH</a></b>, fortgesetzt.
                                                       Wir freuen uns über Unterstützung, am liebsten <b><a href="https://github.com/jkrug/TOXID-cURL/" target="_blank">in Form von Code</a></b>,
                                                       aber natürlich trägt auch ein finanzielle Unterstützung zur Wartung, 
                                                       Pflege und Entwicklung neuer Features bei.',
    'TOXID_SOURCE'                                 => 'CMS URL',
    'TOXID_SOURCE_SSL'                             => 'CMS SSL-URL',
    'TOXID_SEO_SNIPPET'                            => 'URL Identifier / SEO-Snippet',
    'TOXID_SEARCH_URL'                             => 'URL to call the searchpage (optional)',
    'TOXID_PARAM'                                  => 'TOXID URL parameter',
    'TOXID_PREVIEW_PARAM'                          => 'Admin URL-Parameter (i.e. Blog Preview)',
    'TOXID_CMS_PARAMS'                             => 'Allowed, CMS specific parameters (comma-separated list)',
    'TOXID_DONT_REWRITE'                           => 'Don\'t rewrite the URLs - Users will linked to the external site.',
    'TOXID_DONT_PASSTHROUGH'                       => '<b><u>Don\'t</u></b> pass POST parameters to CMS',
    'TOXID_DONT_REWRITE_REL_URLS'                  => 'Don\'t rewrite the URLs for particular "rel" attribute values (comma-separated list)',
    'TOXID_DONT_REWRITE_URLS_WITH_FILE_EXTENSIONS' => 'Don\'t rewrite the URLs for particular file extensions (comma-separated list)',
    'TOXID_ERROR_404_LINK'                         => 'User will be redirected to this URL on 404 error (optional)',
    'TOXID_REWRITE_URLENCODED'                     => 'Rewrite URLs that are "url encoded" (URL must be contained in the "src" attribute)',
    'TOXID_REDIRECT_301_TO_STARTPAGE'              => 'Redirect to home page if the CMS returns the HTTP status code 301',
    'TOXID_LOOKING_FOR'                            => 'Support us with PayPal!',
    'TOXID_GENERAL'                                => 'General',
    'TOXID_BECOME_PARTNER'                         => 'Become a partner',
    'TOXID_INTEGRATIONPARTNER'                     => 'Integration partners',
    'TOXID_CACHE'                                  => 'Cache',
    'TOXID_CACHE_TTL'                              => 'Cache lifetime (TTL) in seconds',
    'HELP_TOXID_CACHE_TTL'                         => '0 => forever<br>no value => default cache deactivated',
);

if (oxRegistry::getConfig()->getConfigParam('iUtfMode') === 0) {
    foreach ($aLang as $k=>$v) {
        $aLang[$k] = utf8_decode($v);
    }
}