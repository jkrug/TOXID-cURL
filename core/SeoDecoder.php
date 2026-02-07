<?php

namespace Toxid\Core;

use OxidEsales\Eshop\Core\Registry;

/**
 * TOXID SeoDecoder - Intercepts SEO URLs and routes TOXID pages
 */
class SeoDecoder extends SeoDecoder_parent
{
    /** @var array */
    private $decodedUrl = [];

    /**
     * Overwrite default decodeUrl handling to check for TOXID pages
     */
    public function decodeUrl($sSeoUrl)
    {
        if ($this->isToxidUrl($sSeoUrl)) {
            $aRet = [];
            $aRet['cl'] = 'toxid';
            $aRet['lang'] = $this->decodedUrl['toxidLang'];
            $toxidUrl = $this->decodedUrl['toxidUrl'];

            Registry::getLang()->setBaseLanguage($aRet['lang']);
            $this->getConfig()->setConfigParam('sToxidCurlPage', $toxidUrl);

            return $aRet;
        }
        if (isset($this->decodedUrl['params'])) {
            return $this->decodedUrl['params'];
        }
        if (isset($this->decodedUrl['url'])) {
            Registry::getUtils()->redirect($this->getConfig()->getShopURL() . $this->decodedUrl['url'], false);
        }

        return false;
    }

    /**
     * Decode SEO snippet URL for TOXID
     */
    protected function detectToxidAndLang($sSeoUrl)
    {
        $seoSnippets = $this->getConfig()->getConfigParam('aToxidCurlSeoSnippets');

        if (!is_array($seoSnippets)) {
            return false;
        }

        foreach ($seoSnippets as $langId => $snippet) {
            if ($snippet === '' || $snippet === null) {
                continue;
            }
            if (strpos($sSeoUrl, $snippet . '/') !== false) {
                $aUrlSplit = explode($snippet . '/', $sSeoUrl);
                return [
                    'lang' => $langId,
                    'url' => $aUrlSplit[1] ?? '',
                ];
            }
        }

        return false;
    }

    /**
     * Check for TOXID URL
     */
    private function isToxidUrl($sSeoUrl): bool
    {
        $decodedToxidUrl = $this->detectToxidAndLang($sSeoUrl);
        if (false !== $decodedToxidUrl) {
            $this->decodedUrl['toxidUrl'] = $decodedToxidUrl['url'];
            $languageId = Registry::getLang()->getBaseLanguage();
            $this->decodedUrl['toxidLang'] = $this->processToxidLangByUrl($languageId, $this->decodedUrl['toxidUrl']);

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

        // If nothing matched, treat as TOXID URL
        $this->decodedUrl['toxidUrl'] = $sSeoUrl;
        $languageId = Registry::getLang()->getBaseLanguage();
        $this->decodedUrl['toxidLang'] = $this->processToxidLangByUrl($languageId, $this->decodedUrl['toxidUrl']);

        return true;
    }

    /**
     * Process TOXID language by URL
     */
    protected function processToxidLangByUrl(int $languageId, string $sSeoUrl): int
    {
        return $languageId;
    }
}
