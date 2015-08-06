<?php

/**
 *    This file is part of TOXID Module for OXID eShop CE/PE/EE.
 *
 *    TOXID is free software: you can redistribute it and/or modify
 *    it under the terms of the MIT License.
 *
 *
 * @link          http://toxid.org
 * @link          http://marmalade.de
 * @package       core
 * @copyright (C) marmalade.de 2011
 */
class toxid_curl_oxseodecoder extends toxid_curl_oxseodecoder_parent
{
    private $decodedUrl = array();

    public function decodeUrl($sSeoUrl)
    {
        if ($this->isToxidUrl($sSeoUrl)) {
            $aRet['cl']   = 'toxid_curl';
            $aRet['lang'] = $this->decodedUrl['lang'];
            $toxidUrl     = $this->decodedUrl['toxidUrl'];

            oxRegistry::getLang()->setBaseLanguage($aRet['lang']);
            $this->getConfig()->setConfigParam('sToxidCurlPage', $toxidUrl);

            return $aRet;
        }
        if (isset($this->decodedUrl['params'])) {
            return $this->decodedUrl['params'];
        }
        oxRegistry::getUtils()->redirect($this->getConfig()->getShopURL() . $this->decodedUrl['url'], false);

    }

    /**
     * detect if page is toxidPage
     * if so, return array with langId and URL part
     */
    protected function detectToxidAndLang($sSeoUrl)
    {
        $seoSnippets = $this->getConfig()->getConfigParam('aToxidCurlSeoSnippets');
        foreach ($seoSnippets as $langId => $snippet) {
            if ('' === $snippet) {
                continue;
            }
            if (strpos($sSeoUrl, $snippet . '/') !== false) {
                $aUrlSplit = explode($snippet . '/', $sSeoUrl);
                $toxidInfo = array(
                    'lang' => $langId,
                    'url'  => $aUrlSplit[1],
                );

                return $toxidInfo;
            }
        }

        // if nothing was found, return false
        return false;
    }

    private function isToxidUrl($sSeoUrl)
    {
        $decodedToxidUrl = $this->detectToxidAndLang($sSeoUrl);
        if (false !== $decodedToxidUrl) {
            $this->decodedUrl['toxidLang'] = $decodedToxidUrl['lang'];
            $this->decodedUrl['toxidUrl']  = $decodedToxidUrl['url'];

            return true;
        }
        $aParams = parent::decodeUrl($sSeoUrl);
        if (false !== $aParams) {
            $this->decodedUrl['params'] = $aParams;

            return false;
        }
        $sUrl = $this->_decodeOldUrl($sSeoUrl);
        if (false !== $sUrl) {
            $this->decodedUrl['url'] = $sUrl;

            return false;
        }
        $sUrl = $this->_decodeSimpleUrl($sSeoUrl);
        if (null !== $sUrl) {
            $this->decodedUrl['url'] = $sUrl;

            return false;
        }

        $this->decodedUrl['toxidUrl']  = $this->postProcessToxidUrl($sSeoUrl);
        $this->decodedUrl['toxidLang'] = oxRegistry::getLang()->getBaseLanguage();

        return true;
    }

    protected function postProcessToxidUrl($sSeoUrl)
    {
        /* Waving chicken... */
        if (method_exists(get_parent_class(), 'postProcessToxidUrl')) {
            $sSeoUrl = parent::postProcessToxidUrl($sSeoUrl);
        }
        return $sSeoUrl;
    }
}
