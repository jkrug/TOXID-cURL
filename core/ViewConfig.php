<?php

namespace Toxid\Core;

use OxidEsales\Eshop\Core\Registry;

/**
 * TOXID ViewConfig - Provides getToxid() in templates
 */
class ViewConfig extends ViewConfig_parent
{
    /**
     * Returns instance of ToxidCurl
     */
    public function getToxid(): ToxidCurl
    {
        $toxidCurl = Registry::get(ToxidCurl::class);
        if (!$toxidCurl->getInitialized()) {
            $smartyParser = new SmartyParser();
            $toxidCurl->init($smartyParser);
        }

        return $toxidCurl;
    }
}
