<?php

$sLangName  = "Deutsch";

$aLang = array(
    'charset'                                      => 'UTF-8',
    'toxid_setup'                                  => 'TOXID Einstellungen',
    'toxid_setup_main'                             => 'TOXID Grundeinstellungen',
    'TOXID_SUPPORT_HEADLINE'                       => 'TOXID unterstützen',
    'TOXID_SUPPORT_DESC'                           => 'TOXID cURL ist ein OpenSource Projekt, ursprünglich entwickelt von Joscha Krug. 
                                                       Die Entwicklung wird von seiner Agentur, der <b><a href="http://www.marmalade.de/?pk_campaign=toxidBackend&pk_kwd=Textlink" target="_blank">marmalade GmbH</a></b>, fortgesetzt.
                                                       Wir freuen uns über Unterstützung, am liebsten <b><a href="https://github.com/jkrug/TOXID-cURL/" target="_blank">in Form von Code</a></b>,
                                                       aber natürlich trägt auch ein finanzielle Unterstützung zur Wartung, 
                                                       Pflege und Entwicklung neuer Features bei.',
    'TOXID_SOURCE'                                 => 'CMS URL',
    'TOXID_SOURCE_SSL'                             => 'CMS SSL-URL',
    'TOXID_SEO_SNIPPET'                            => 'URL Identifier / SEO-Snippet',
    'TOXID_SEARCH_URL'                             => 'URL zum Aufruf der Suche (optional)',
    'TOXID_NOT_FOUND_URL'                          => 'Not found URL (optional)',
    'TOXID_PREVIEW_PARAM'                          => 'Preview URL-Parameter (z.B. Blog Preview)',
    'TOXID_PARAM'                                  => 'TOXID URL-Parameter',
    'TOXID_DONT_REWRITE'                           => 'URLs nicht umschreiben - Aufrufe führen auf externe CMS-Seite',
    'TOXID_DONT_PASSTHROUGH'                       => 'POST-Parameter <b><u>nicht</u></b> an CMS durchreichen',
    'TOXID_DONT_REWRITE_REL_URLS'                  => 'URLs für bestimmte "rel" Attributwerte nicht umschreiben (kommaseparierte Liste)',
    'TOXID_DONT_REWRITE_URLS_WITH_FILE_EXTENSIONS' => 'URLs für bestimmte Dateiendungen nicht umschreiben (kommaseparierte Liste)',
    'TOXID_ERROR_404_LINK'                         => 'User will be redirected to this URL on 404 error, when no other 404 url is specified for specific language (optional)',
    'TOXID_REWRITE_URLENCODED'                     => 'URLs die "url encoded" sind umschreiben (URL muss sich im "src" attribut befindet)',
    'TOXID_REDIRECT_301_TO_STARTPAGE'              => 'Auf Startseite weiterleiten wenn das CMS den HTTP-Statuscode 301 liefert',
    'TOXID_LOOKING_FOR'                            => 'gesucht!',
    'TOXID_GENERAL'                                => 'Allgemein',
    'TOXID_BECOME_PARTNER'                         => 'Partner werden',
    'TOXID_INTEGRATIONPARTNER'                     => 'Integrationspartner',
    'TOXID_CACHE'                                  => 'Cache',
    'TOXID_CACHE_TTL'                              => 'Cache-Lebenszeit (TTL) in Sekunden',
    'HELP_TOXID_CACHE_TTL'                         => '0 => unendlich<br>kein Wert => default Cache deaktiviert',

);

if (oxRegistry::getConfig()->getConfigParam('iUtfMode') === 0) {
    foreach ($aLang as $k=>$v) {
        $aLang[$k] = utf8_decode($v);
    }
}
