<?php
/**
 *       This file is part of TOXID Module for OXID eShop CE/PE/EE.
 *
 *       TOXID is free software: you can redistribute it and/or modify
 *       it under the terms of the MIT License.
 *
 *
 * @link      http://toxid.org
 * @link      http://marmalade.de
 * @package   core
 * @copyright (C) marmalade.de 2011-2013
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
     * string with XML-content-title
     *
     * @var string
     */
    public $_sPageTitle = null;

    /**
     * string with XML-content-description
     *
     * @var string
     */
    public $_sPageDescription = null;

    /**
     * string with XML-content-keywords
     *
     * @var string
     */
    public $_sPageKeywords = null;

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
     * sets cache TTL (in seconds)
     * @var int
     */
    protected $_iCacheTTL = 0;

    /**
     * Deprecated!
     * resturns a single instance of this class
     *
     * @return toxidCurl
     * @deprec Use the registry pattern please
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

        return (string) $sText;

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

        $sPageTitle = $this->_rewriteUrls($this->_getSnippetFromXml('//metadata//title', null, $blMultiLang));

        $sPageDescription = $this->_rewriteUrls($this->_getSnippetFromXml('//metadata//description', null, $blMultiLang));

        $sPageKeywords = $this->_rewriteUrls($this->_getSnippetFromXml('//metadata//keywords', null, $blMultiLang));

        $oConf   = $this->getConfig();
        $sShopId = $oConf->getActiveShop()->getId();
        $sLangId = oxRegistry::getLang()->getBaseLanguage();
        $sText   = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
            $sText,
            $snippet.'_'.$sShopId.'_'.$sLangId,
            null,
            true
        );

        $this->_sPageTitle = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
            $sPageTitle,
            '//metadata//title_'.$sShopId.'_'.$sLangId,
            null,
            true
        );

        $this->_sPageDescription = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
            $sPageDescription,
            '//metadata//description_'.$sShopId.'_'.$sLangId,
            null,
            true
        );

        $this->_sPageKeywords = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
            $sPageKeywords,
            '//metadata//keywords_'.$sShopId.'_'.$sLangId,
            null,
            true
        );

        $this->_aCustomPage = null;

        /* if actual site is ssl-site, replace all image-sources with ssl-urls */
        if ($oConf->isSsl()) {

            $aSslUrl = $oConf->getShopConfVar('aToxidCurlSourceSsl', $sShopId);
            $sSslUrl = $aSslUrl[$sLangId];

            if (!empty($sSslUrl)) {

                $oldSrc = $this->_getToxidLangSource($sLangId);
                $newSrc = $sSslUrl;

                if ($oldSrc != "" && $newSrc != "") {
                    $sText= str_replace('src="'.$oldSrc, 'src="'.$newSrc, $sText);
                }
            }
        }

        $sShopCharset = oxRegistry::getLang()->translateString('charset');
        if($oConf->getConfigParam('iUtfMode') === 0)
        {
            $sText = utf8_decode($sText);
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
        $sUrl = $source.$custom.$page.$param;

        // Set cache TTL based on Toxid setup
        $this->_setCacheTTL();

        // check if cache TTL and requested URL is cached
        // if not get remote content
        if(!$this->_iCacheTTL || !($cachedPage = $this->_getCachedXml($sUrl))) {

            $aPage = $this->_getRemoteContent($sUrl);

            switch ($aPage['info']['http_code'])
            {
                case 500:
                    header ("HTTP/1.1 500 Internal Server Error");
                    header ('Location: '.$this->getConfig()->getShopHomeURL());
                    oxRegistry::getUtils()->showMessageAndExit('');
                    break;
                case 404:
                    header ("HTTP/1.1 404 Not Found");
                    header ('Location: '.$this->getConfig()->getShopHomeURL());
                    oxRegistry::getUtils()->showMessageAndExit('');
                    break;
                case 0:
                    header ('Location: '.$this->getConfig()->getShopHomeURL());
                    oxRegistry::getUtils()->showMessageAndExit('');
                    break;
            }

            // Especially for Wordpress-Frickel-Heinze
            // Kill everything befor the <?xml
            $this->_sPageContent = preg_replace('/.*<\?xml/ms', '<?xml', $aPage['content']);

            // try to save Toxid content in the cache
            if($this->_iCacheTTL) {
                $this->_setCachedXml($sUrl, $this->_sPageContent);
            }

        } else {

            // use the cached content
            $this->_sPageContent = $cachedPage;

        }

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

        /* Forward POST requests like a boss */
        if (!empty($_POST)) {
            $postRequest = http_build_query($_POST, '', '&');
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $postRequest);
        }

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
                $iLangId = oxRegistry::getLang()->getBaseLanguage();
            }
            $aLanguages = array($iLangId);
        }
        else {
            $aLangs = $this->getConfig()->getConfigParam('aToxidCurlSource');
            arsort($aLangs);
            $aLanguages = array_keys($aLangs);
        }

        foreach ($aLanguages as $iLangId ) {
            if ($this->getConfig()->getEdition() === 'EE') {
                $sShopUrl = $this->getConfig()->getConfigParam('sMallShopURL');
            } else {
                $sShopUrl = $this->getConfig()->getConfigParam('sShopURL');
            }
            if (substr($sShopUrl, -1) !== '/') {
                $sShopUrl = $sShopUrl.'/';
            }
            $target = $sShopUrl.$this->_getToxidLangSeoSnippet($iLangId).'/';
            $source = $this->_getToxidLangSource($iLangId);
            $pattern = '%href=(\'|")' . $source . '[^"\']*(.|/|\.html|\.php|\.asp)(\?[^"\']*)?(\'|")%';
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
            $iLangId = oxRegistry::getLang()->getBaseLanguage();
        }

        $source = $this->_aSourceUrlByLang[$iLangId];
        if (substr($source, -1) !== '/') {
            return $source.'/';
        }
        return $source;
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
            $iLangId = oxRegistry::getLang()->getBaseLanguage();
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
            $iLangId = oxRegistry::getLang()->getBaseLanguage();
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
            $iLangId = oxRegistry::getLang()->getBaseLanguage();
        }

        return $this->_aSearchUrl[$iLangId];
    }

    /**
     * runs search by   given keywords in typo3, and returns results as html
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

    /**
     * Returns Toxid cache file name
     * @author Oliver Georgi <slackero@gmail.com>
     * @param string $sUrl
     * @return string
     */
    protected function _getCacheFileName($sUrl)
    {
        return 'toxid_'.md5($sUrl).'.xml';
    }

    /**
     * Returns Toxid cache directory
     * @author Oliver Georgi <slackero@gmail.com>
     * @return string
     */
    protected function _getCacheDir()
    {
        return realpath($this->getConfig()->getConfigParam('sCompileDir')).DIRECTORY_SEPARATOR.'toxid'.DIRECTORY_SEPARATOR;
    }

    /**
     * Returns XML from local cache file if existing
     * @author Oliver Georgi <slackero@gmail.com>
     * @param string $sUrl
     * @return string
     */
    protected function _getCachedXml($sUrl)
    {
        $sCacheFile = $this->_getCacheDir().$this->_getCacheFileName($sUrl);

        if($this->_isCached($sCacheFile)) {
            return file_get_contents($sCacheFile);
        }

        return false;
    }

    /**
     * Save the remote curled XML to the local cache file and
     * create the toxid cache directory if not exiting
     * @author Oliver Georgi <slackero@gmail.com>
     * @param string $sUrl
     * @param string $sCacheData
     * @return int|bool
     */
    protected function _setCachedXml($sUrl, $sCacheData='')
    {
        $sCacheDir = $this->_getCacheDir();

        if(!is_dir($sCacheDir)) {

            // a file exists, so we cannot write here and stop with writing to cache
            if(file_exists($sCacheDir)) {
                return false;
            }

            clearstatcache();
            $iMode = defined('OXID_PHP_UNIT') ? 0777 : 0755;

            if((@mkdir($sCacheDir, $iMode, true) === false) || (!is_dir($sCacheDir))) {
                return false;
            }
        }

        $sCacheFile = $sCacheDir.$this->_getCacheFileName($sUrl);

        return @file_put_contents($sCacheFile, $sCacheData);
    }

    /**
     * Returns if the requested cache file exists and the cache TTL is valid (or not)
     * @author Oliver Georgi <slackero@gmail.com>
     * @param string $sCacheFile
     * @return bool
     */
    protected function _isCached($sCacheFile)
    {
        if(is_file($sCacheFile) && (filemtime($sCacheFile) + $this->_iCacheTTL >= time())) {
            return true;
        }

        return false;
    }

    /**
     * Set the cache TTL based on Toxid configuration
     * @author Oliver Georgi <slackero@gmail.com>
     * @return int
     */
    protected function _setCacheTTL()
    {
        // Cache or not to cache
        if($this->getConfig()->getConfigParam('toxidCacheEnabled')) {
            $this->_iCacheTTL = intval($this->getConfig()->getConfigParam('iToxidCacheTTL'));
        } else {
            $this->_iCacheTTL = 0;
        }

        return $this->_iCacheTTL;
    }

}
