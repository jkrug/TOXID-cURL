<?php
/**
 *    This file is part of TOXID Module for OXID eShop CE/PE/EE.
 *
 *    TOXID is free software: you can redistribute it and/or modify
 *    it under the terms of the MIT License.
 *
 *
 * @link      http://toxid.org
 * @link      http://marmalade.de
 * @package   core
 * @copyright (C) marmalade.de 2011
 */

/**
 * toxid Class
 *
 */
class toxidCurl extends oxSuperCfg
{
    /**
     * toxidCurl class instance.
     *
     * @var toxidcurl instance
     */
    private static $_instance = null;

    /**
     * array of content snippets
     *
     * @var array
     */
    public static $aToxidSnippets = null;

    /**
     * object
     *
     * @var object
     */
    private $_oSxToxid = null;

    /**
     * string with XML-content
     *
     * @var string
     */
    private $_sPageContent = null;

    /**
     * stores URL from which typo3 content is loaded.
     * @var array
     */
    protected $_aSourceUrlByLang = null;

    /**
     * Stores URI to which typo3 urls will be converted, so it will be loaded in oxid
     * @var array
     */
    protected $_aRewriteStartUrl = null;

    /**
     * array of string with language specific toxidUrlParam
     * @var array
     */
    protected $_aToxidLangUrlParam = null;

    /**
     * stores search url by active language
     * @var array
     */
    protected $_aSearchUrl = null;

    /**
     * stores search results
     * @var array
     */
    protected $_aSearchCache = array();

    /**
     * resturns a single instance of this class
     *
     * @return toxidCurl
     */
    public static function getInstance()
    {
        if ( !(self::$_instance instanceof toxidCurl) ) {

            self::$_instance = oxNew( 'toxidCurl' );

        }
        return self::$_instance;
    }

    /**
     * content will be loaded on first usage needed
     * @deprecated
     */
    public function loadCmsPage() {}

    /**
     * returns SimpleXMLElement object from Typo3 xml
     * @param bool $blReset
     * @return SimpleXMLElement
     */
    protected function _getXmlObject($blReset = false)
    {
        if ($this->_oSxToxid !== null && !$blReset) {
            return $this->_oSxToxid;
        }
        $this->_oSxToxid = simplexml_load_string($this->_getXmlFromTypo3($blReset));
        return $this->_oSxToxid;

    }

    /**
     * returns snippet text from xml object
     * @param $sSnippet
     * @return SimpleXMLElement
     */
    protected function _getSnippetFromXml($sSnippet)
    {
        $oTypo3Xml = $this->_getXmlObject();
        $aXpathSnippets = $oTypo3Xml->xpath('//'.$sSnippet.'[1]');
        $sText = $aXpathSnippets[0];

        return $sText;

    }

    /**
     * returns the called snippet
     * @param string $snippet
     * @param bool $blMultiLang
     * @return string
     */
    public function getCmsSnippet($snippet=null, $blMultiLang = false, $customPage = null)
    {
        if($snippet == null) {
            return '<strong style="color:red;">TOXID: Please add part, you want to display!</strong>';
        }

        if ($customPage != '') {
            $this->_aCustomPage = $customPage;
        }

        $sText = $this->_getSnippetFromXml($snippet);
        $sText = $this->_rewriteUrls($sText, null, $blMultiLang);


        $sShopId = $this->getConfig()->getActiveShop()->getId();
        $sLangId = oxLang::getInstance()->getBaseLanguage();
        $sText = oxUtilsView::getInstance()->parseThroughSmarty(
            $sText,
            $snippet.'_'.$sShopId.'_'.$sLangId,
            null,
            true
        );

        $this->_aCustomPage = null;

        /* if actual site is ssl-site, replace all image-sources with ssl-urls */
        if ($this->getConfig()->isSsl()) {

            $sslRepArray = $this->getConfig()->getConfigParam('aToxidCurlSslPicReplacement');
            $sslRepUrls = $sslRepArray[$sLangId];

            if (is_array($sslRepUrls)) {

                $oldSrc = $sslRepUrls[0];
                $newSrc = $sslRepUrls[1];

                if ($oldSrc != "" && $newSrc != "") {
                    $sText= str_replace('src="'.$oldSrc, 'src="'.$newSrc, $sText);
                }
            }
        }

        if($this->getConfig()->getConfigParam('iUtfMode') !== 1)
        {
			$sText= str_replace("�", "\"", $sText);
			$sText= str_replace("�", "\"", $sText);
			$sText= str_replace("�", "'", $sText);
			$sText= str_replace("�", "'", $sText);
			$sText= str_replace("�", "-", $sText);
			$sText = mb_convert_encoding($sText, 'ISO-8859-15', 'UTF-8');
            return $sText;
        } else {
            return $sText;
        }
    }

    /**
     * returns raw string from typo3 CMS-page
     * @param bool $blReset set to true if you want to fetch content again
     * @return string
     */
    protected function _getXmlFromTypo3($blReset = false)
    {
        if ($this->_sPageContent !== null && !$blReset) {
            return $this->_sPageContent;
        }

        $source = $this->_getToxidLangSource();
        $page = $this->getConfig()->getConfigParam('sToxidCurlPage');
        $param = $this->_getToxidLangUrlParam();
        $custom  = $this->_getToxidCustomPage();
        $aPage = $this->_getRemoteContent($source.$custom.$page.$param);

        switch ($aPage['info']['http_code'])
        {
            case 500:
                header ("HTTP/1.1 500 Internal Server Error");
                header ('Location: '.$this->getConfig()->getShopHomeURL());
                oxUtils::getInstance()->showMessageAndExit('');
                break;
            case 404:
                header ("HTTP/1.1 404 Not Found");
                header ('Location: '.$this->getConfig()->getShopHomeURL());
                oxUtils::getInstance()->showMessageAndExit('');
                break;
        }

		// Especially for Wordpress-Frickel-Heinze
		// Kill everything befor the <?xml
		$this->_sPageContent = preg_replace('/.*<\?xml/ms', '<?xml', $aPage['content']);

		return $this->_sPageContent;

    }

    /**
     * returns array with result of http get. array structure:
     * array (
     *   [content] => '<html></html>',
     *   [info] => array (
     *     [http_code] => 200,
     *     ...
     *   )
     * )
     * @param $sUrl
     * @return array
     */
    protected function _getRemoteContent($sUrl)
    {
        $aResult = array();
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $sUrl);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        $aResult['content'] = curl_exec($curl_handle);
        $aResult['info']    = curl_getinfo($curl_handle);
        curl_close($curl_handle);
        return $aResult;
    }

    /**
     * @param null $iLangId
     * @return string with toxidStartUrl
     */
    public function getToxidStartUrl($iLangId = null)
    {
        return preg_replace('#index.php\??$#', '', $this->getConfig()->getShopHomeURL()).$this->_getToxidLangSeoSnippet($iLangId);
    }

    /**
     * rewrites given string URL's, which belongs to typo3 and configured in aToxidCurlSource
     * @param string $sContent
     * @param int $iLangId
     * @param bool $blMultiLang
     * @return string with changed URL's
     */
    protected function _rewriteUrls($sContent, $iLangId = null, $blMultiLang = false)
    {
        if ($this->getConfig()->getConfigParam('toxidDontRewriteUrls') == true)
        {
            return $sContent;
        }
        
        if ($blMultiLang == false) {
            if ($iLangId === null) {
                $iLangId = oxLang::getInstance()->getBaseLanguage();
            }
            $aLanguages = array($iLangId);
        }
        else {
            $aLangs = $this->getConfig()->getConfigParam('aToxidCurlSource');
            arsort($aLangs);
            $aLanguages = array_keys($aLangs);
        }

        foreach ($aLanguages as $iLangId ) {
            $target = $this->getConfig()->getConfigParam('sShopURL').$this->_getToxidLangSeoSnippet($iLangId).'/';
            $source = $this->_getToxidLangSource($iLangId);
            $pattern = '%href=(\'|")' . $source . '[^"\']*(/|\.html|\.php|\.asp)(\?[^"\']*)?(\'|")%';
            preg_match_all($pattern, $sContent, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				$sContent = str_replace($match[0], str_replace($source, $target, $match[0]), $sContent);
			}
			unset($match);
        }

        return $sContent;
    }

    /**
     * returns string with language specific sourceUrl
     * @param int $iLangId
     * @param bool $blReset reset object value, and get url again
     * @return string
     */
    protected function _getToxidLangSource($iLangId = null, $blReset = false)
    {
        if ($this->_aSourceUrlByLang === null || $blReset) {
            $this->_aSourceUrlByLang = $this->getConfig()->getConfigParam('aToxidCurlSource');
        }
        if ($iLangId === null) {
            $iLangId = oxLang::getInstance()->getBaseLanguage();
        }

        return $this->_aSourceUrlByLang[$iLangId];
    }

    /**
     * returns string with language specific toxidUrlParam
     * @param int $iLangId
     * @param bool $blReset reset object value, and get url again
     * @return string
     */
    protected function _getToxidLangUrlParam($iLangId = null, $blReset = false)
    {
        if ($this->_aToxidLangUrlParam === null || $blReset) {
            $this->_aToxidLangUrlParam = $this->getConfig()->getConfigParam('aToxidCurlUrlParams');
        }
        if ($iLangId === null) {
            $iLangId = oxLang::getInstance()->getBaseLanguage();
        }

        return $this->_aToxidLangUrlParam[$iLangId];
    }

    /**
     * returns string with language specific toxidSeoSnippet
     * @param int $iLangId
     * @param bool $blReset reset object value, and get url again
     * @return string
     */
    protected function _getToxidLangSeoSnippet($iLangId = null, $blReset = false)
    {
        if ($this->_aRewriteStartUrl === null || $blReset) {
            $this->_aRewriteStartUrl = $this->getConfig()->getConfigParam('aToxidCurlSeoSnippets');
        }
        if ($iLangId === null) {
            $iLangId = oxLang::getInstance()->getBaseLanguage();
        }

        return $this->_aRewriteStartUrl[$iLangId];
    }

    /**
     * returns string with the currently defined custom url
     */
    protected function _getToxidCustomPage()
    {
        return ($this->_aCustomPage !== null) ? $this->_aCustomPage : '';
    }

    /**
     * returns typo3 search URL
     * @param int $iLangId
     * @param bool $blReset reset object value, and get url again
     * @return string
     */
    protected function _getToxidSearchUrl($iLangId = null, $blReset = false)
    {
        if ($this->_aSearchUrl === null || $blReset) {
            $this->_aSearchUrl = $this->getConfig()->getConfigParam('aToxidSearchUrl');
        }
        if ($iLangId === null) {
            $iLangId = oxLang::getInstance()->getBaseLanguage();
        }

        return $this->_aSearchUrl[$iLangId];
    }

    /**
     * runs search by  given keywords in typo3, and returns results as html
     * @param $sKeywords
     * @return string
     */
    public function getSearchResult($sKeywords)
    {
        if (isset($this->_aSearchCache[$sKeywords])) {
            return $this->_aSearchCache[$sKeywords];
        }
        $this->_aSearchCache[$sKeywords] = '';
        $sSearchStartUrl = $this->_getToxidSearchUrl();

        $aSearchResults = $this->_getRemoteContent($sSearchStartUrl.$sKeywords);

        if ($aSearchResults['info']['http_code'] == 200) {
            $sSearchResult = $aSearchResults['content'];
            $sSearchResult = $this->_rewriteUrls($sSearchResult);
            $this->_aSearchCache[$sKeywords] = $sSearchResult;
        }

        if($this->getConfig()->getConfigParam('iUtfMode') !== 1)
        {
            return utf8_decode($this->_aSearchCache[$sKeywords]);
        } else {
            return $this->_aSearchCache[$sKeywords];
        }
    }
}