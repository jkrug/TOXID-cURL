<?php

$sLangName  = "Deutsch";

$aLang = array(
    'charset'                   => 'UTF-8',
    'toxid_setup'               => 'TOXID Einstellungen',
    'toxid_setup_main'          => 'TOXID Grundeinstellungen',
    'TOXID_SUPPORT_HEADLINE'    => 'TOXID unterstützen',
    'TOXID_SUPPORT_DESC'        => 'TOXID cURL ist ein OpenSource Projekt, ursprünglich entwickelt von Joscha Krug.
                                    Die Entwicklung wird von seiner Agentur, der <b><a href="http://www.marmalade.de/?pk_campaign=toxidBackend&pk_kwd=Textlink" target="_blank">marmalade GmbH</a></b>, fortgesetzt.
                                    Wir freuen uns über Unterstützung, am liebsten <b><a href="https://github.com/jkrug/TOXID-cURL/" target="_blank">in Form von Code</a></b>,
                                    aber natürlich trägt auch ein finanzielle Unterstützung zur Wartung,
                                    Pflege und Entwicklung neuer Features bei.',
    'TOXID_SOURCE'              => 'CMS URL',
    'TOXID_SOURCE_SSL'          => 'CMS SSL-URL',
    'TOXID_SEO_SNIPPET'         => 'URL Identifier / SEO-Snippet',
    'TOXID_SEARCH_URL'          => 'URL zum Aufruf der Suche (optional)',
    'TOXID_PARAM'               => 'TOXID URL-Parameter',
    'TOXID_DONT_REWRITE'        => 'URLs nicht umschreiben - Aufrufe führen auf externe CMS-Seite',
    'TOXID_DONT_PASSTHROUGH'    => 'POST-Parameter <b><u>nicht</u></b> an CMS durchreichen',
    'TOXID_LOOKING_FOR'         => 'gesucht!',
    'TOXID_GENERAL'             => 'Allgemein',
    'TOXID_BECOME_PARTNER'      => 'Partner werden',
    'TOXID_INTEGRATIONPARTNER'  => 'Integrationspartner',
    'TOXID_CACHE'               => 'Caching',
    'TOXID_CACHE_ENABLED'       => 'Externen CMS-Inhalt lokal zwischenspeichern',
    'TOXID_CACHE_TTL'           => 'Gültigkeitsdauer (TTL)',
    'TOXID_IN_SECONDS'          => 'Sekunden (z.B. 3600)',
    'TOXID_LOGIN'               => 'Zugangsschutz Login',
    'TOXID_PWD'                 => 'Zugangsschutz Passwort',
    'TOXID_DONT_REDIRECT'       => 'Nicht weiterleiten auf Startseite des Shops bei fehlgeschlagenem Abruf',
);

if (oxRegistry::getConfig()->getConfigParam('iUtfMode') === 0) {
    foreach ($aLang as $k=>$v) {
        $aLang[$k] = utf8_decode($v);
    }
}
