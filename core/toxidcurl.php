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
     * stores custom page
     * @var string
     */
    protected $_sCustomPage = null;

    /**
     * integer with cache’s TTL (in seconds)
     * @var int
     */
    protected $_iCacheTtl = null;

    /**
     * is toxid XML caching enabled or not
     * @var bool
     */
    protected $_blCache = null;

    /**
     * is snippet caching enabled or not
     * @var bool
     */
    protected $_blCacheSnippets = null;

    /**
     * string with Toxid cache directory
     * @var string
     */
    protected $_sCacheDir = null;

    /**
     * string with active Oxid charset
     * @var string
     */
    protected $_sCharset = null;

    /**
     * array of strings with language specific toxidCurlLogin
     * @var array
     */
    protected $_aToxidCurlLogin = null;

    /**
     * array of strings with language specific toxidCurlPwd
     * @var array
     */
    protected $_aToxidCurlPwd = null;

    /**
     * bool if cURL metadata has been parsed
     * @var bool
     */
    protected $_blToxidParsedMeta = false;

    /**
     * shortcut to the Oxid config object
     * @var object
     */
    protected $_oConf = null;

    /**
     * is Oxid in SSL mode
     * @var bool
     */
    protected $_blIsSsl = false;

    /**
     * used to separate snippet cache names for http/https mode
     * @var string
     */
    protected $_sSslSnippetAdd = '';

    /**
     * string with active Shop ID
     * @var string
     */
    public $sShopId = null;

    /**
     * string active language
     * @var string
     */
    public $sLangId = null;

    /**
     * string with requested Toxid URL
     * @var string
     */
    public $sUrl = null;

    /**
     * bool value handling HTTP_CACHE_CONTROL
     * @var bool
     */
    public $blHttpCacheControl = null;

    /**
     * array of UTF-8 chars to search for to be replaced
     * by HTML entities if Oxid charset is not equal UTF-8
     * @var array
     */
    public $aCharsUtf8Search = array('„', '“', '´', '`','€');

     /**
     * array of HTML entities for replacing $aCharsUtf8Search
     * @var array
     */
    public $aCharsUtf8Replace = array('&bdquo;', '&ldquo;', '&acute;', '&#96;', '&euro;');

    /**
     * Deprecated!
     * returns a single instance of this class
     *
     * @return toxidCurl
     * @deprecated Use the registry pattern please
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
     * Construct, set required stuff only once per request
     */
    public function __construct() {

        $this->_oConf = $this->getConfig();
        if($this->_oConf->isSsl()) {
            $this->_blIsSsl = true;
            $this->_sSslSnippetAdd = 'ssl_';
        } else {
            $this->_blIsSsl = false;
            $this->_sSslSnippetAdd = '';
        }
        $this->sShopId = $this->_oConf->getActiveShop()->getId();
        $this->sLangId = oxRegistry::getLang()->getBaseLanguage();
        $this->_blCache = (bool) $this->_oConf->getConfigParam('toxidCacheEnabled');
        $this->_blCacheSnippets = (bool) $this->_oConf->getConfigParam('toxidCacheSnippetsEnabled');

        if($this->_blCache || $this->_blCacheSnippets) {
            $this->blHttpCacheControl = oxRegistry::get('oxUtilsServer')->getServerVar('HTTP_CACHE_CONTROL') !== 'no-cache' ? true : false;
            $this->setCacheTtl();
            $this->setCacheDir();
        }

        $source = $this->_getToxidLangSource();
        $page = $this->_oConf->getConfigParam('sToxidCurlPage');
        $param = $this->_getToxidLangUrlParam();
        $custom  = $this->_getToxidCustomPage();
        $this->sUrl = $source.$custom.$page.$param;

    }

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

        $sPageContent = $this->_getXmlFromTypo3($blReset);

        if($sPageContent === null) {
            return false;
        }

        $this->_oSxToxid = simplexml_load_string($sPageContent);
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

        // check if xmlObject is valid
        if($oTypo3Xml === false) {
            return '';
        }

        $aXpathSnippets = $oTypo3Xml->xpath('//'.$sSnippet.'[1]');
        $sText = $aXpathSnippets[0];

        return (string) $sText;

    }

    /**
     * returns the called snippet
     * @param string $snippet
     * @param bool $blMultiLang
     * @param string $customPage
     * @param int $iCacheTtl
     * @return string
     */
    public function getCmsSnippet($snippet=null, $blMultiLang = false, $customPage = null, $iCacheTtl = null)
    {
        if($snippet === null) {
            return '<strong style="color:red;">TOXID: Please add part, you want to display!</strong>';
        }

        if($iCacheTtl === null && $this->_blCacheSnippets) {
            $iCacheTtl = $this->_getCacheTtl();
        }

        // snippet caching
        if($this->blHttpCacheControl && $iCacheTtl && $this->_oSxToxid === null && ($sCacheContent = $this->_getCache('html', $snippet, $iCacheTtl))) {
            return $sCacheContent;
        }

        if ($customPage != '') {
            $this->_sCustomPage = $customPage;
        }

        $sText = $this->_getSnippetFromXml($snippet);
        $sText = $this->_rewriteUrls($sText, null, $blMultiLang);

        $sText = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
            $sText,
            $snippet.'_'.$this->sShopId.'_'.$this->sLangId,
            null,
            true
        );

        $this->_sCustomPage = null;

        /* if actual site is ssl-site, replace all image-sources with ssl-urls */
        if ($this->_blIsSsl) {

            $aSslUrl = $this->_oConf->getConfigParam('aToxidCurlSourceSsl', $this->sShopId);
            $sSslUrl = $aSslUrl[$this->sLangId];

            if (!empty($sSslUrl)) {

                $oldSrc = $this->_getToxidLangSource($this->sLangId);
                $newSrc = $sSslUrl;

                if ($oldSrc !== '' && $newSrc !== '') {
                    $sText= str_replace('src="'.$oldSrc, 'src="'.$newSrc, $sText);
                }
            }
        }

        if($sText && $this->_getCharset() !== 'utf-8') {
            $sText = str_replace($this->aCharsUtf8Search, $this->aCharsUtf8Replace, $sText);
            $sText = mb_convert_encoding($sText, $this->_sCharset, "auto");
        }

        if($sText && $this->blHttpCacheControl && $iCacheTtl) {
            $this->_writeCache('html', $snippet, $sText);
        }

        return $sText;
    }

    /**
     * returns the requested Metadata
     * @param string $sMetadata
     * @param bool $blMultiLang
     * @param string $customPage
     * @param int $iCacheTtl
     * @return string
     */
    public function getCmsMetadata($sMetadata=null, $blMultiLang = false, $customPage = null, $iCacheTtl = null)
    {
        $aMetadataKeys = array(
            'title' => '//metadata//title',
            'description' => '//metadata//description',
            'keywords' => '//metadata//keywords'
        );

        if($sMetadata === null || !isset($aMetadataKeys[$sMetadata])) {
            return '<strong style="color:red;">No TOXID Metadata key given: title, description or keywords</strong>';
        }

        if($iCacheTtl === null && $this->_blCacheSnippets) {
            $iCacheTtl = $this->_getCacheTtl();
        }

        // get metadata cache
        if($this->blHttpCacheControl && $iCacheTtl && $this->_oSxToxid === null && ($sCacheContent = $this->_getCache('html', $aMetadataKeys[$sMetadata], $iCacheTtl))) {

            if($sMetadata === 'title') {
                $this->_sPageTitle = $sCacheContent;
            } elseif($sMetadata === 'description') {
                $this->_sPageDescription = $sCacheContent;
            } elseif($sMetadata === 'keywords') {
                $this->_sPageKeywords = $sCacheContent;
            }

            return $sCacheContent;
        }


        if ($customPage != '') {
            $this->_sCustomPage = $customPage;
        }

        if($this->_blToxidParsedMeta === false || $customPage !== null) {

            // Title
            $sMetadataContent = $this->_rewriteUrls($this->_getSnippetFromXml($aMetadataKeys['title']), null, $blMultiLang);

            $this->_sPageTitle = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
                $sMetadataContent,
                $aMetadataKeys['title'].'_'.$this->sShopId.'_'.$this->sLangId,
                null,
                true
            );

            // Description
            $sMetadataContent = $this->_rewriteUrls($this->_getSnippetFromXml($aMetadataKeys['description']), null, $blMultiLang);

            $this->_sPageDescription = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
                $sMetadataContent,
                $aMetadataKeys['description'].'_'.$this->sShopId.'_'.$this->sLangId,
                null,
                true
            );

            // Keywords
            $sMetadataContent = $this->_rewriteUrls($this->_getSnippetFromXml($aMetadataKeys['keywords']), null, $blMultiLang);

            $this->_sPageKeywords = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
                $sMetadataContent,
                $aMetadataKeys['keywords'].'_'.$this->sShopId.'_'.$this->sLangId,
                null,
                true
            );

            $this->_blToxidParsedMeta = true;

            if($this->_getCharset() !== 'utf-8') {

                if($this->_sPageTitle) {
                    $this->_sPageTitle = str_replace($this->aCharsUtf8Search, $this->aCharsUtf8Replace, $this->_sPageTitle);
                    $this->_sPageTitle = mb_convert_encoding($this->_sPageTitle, $this->_sCharset, "auto");
                }

                if($this->_sPageDescription) {
                    $this->_sPageDescription = str_replace($this->aCharsUtf8Search, $this->aCharsUtf8Replace, $this->_sPageDescription);
                    $this->_sPageDescription = mb_convert_encoding($this->_sPageDescription, $this->_sCharset, "auto");
                }

                if($this->_sPageKeywords) {
                    $this->_sPageKeywords = str_replace($this->aCharsUtf8Search, $this->aCharsUtf8Replace, $this->_sPageKeywords);
                    $this->_sPageKeywords = mb_convert_encoding($this->_sPageKeywords, $this->_sCharset, "auto");
                }

            }

            // cache metadata
            if($this->blHttpCacheControl && $iCacheTtl) {

                if($this->_sPageTitle) {
                    $this->_writeCache('html', $aMetadataKeys['title'], $this->_sPageTitle);
                }

                if($this->_sPageDescription) {
                    $this->_writeCache('html',  $aMetadataKeys['description'], $this->_sPageDescription);
                }

                if($this->_sPageKeywords) {
                    $this->_writeCache('html', $aMetadataKeys['keywords'], $this->_sPageKeywords);
                }

            }

        }

        $this->_sCustomPage = null;

        if($sMetadata === 'title') {
            $sMetadataContent = $this->_sPageTitle;
        } elseif($sMetadata === 'description') {
            $sMetadataContent = $this->_sPageDescription;
        } elseif($sMetadata === 'keywords') {
            $sMetadataContent = $this->_sPageKeywords;
        } else {
            $sMetadataContent = '';
        }

        return $sMetadataContent;
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

        // check if cache TTL and requested URL is cached
        if($this->blHttpCacheControl && $this->_getCacheTtl() && ($sCacheContent = $this->_getCache('xml', '', $this->_iCacheTtl))) {

            // use the cached content
            $this->_sPageContent = $sCacheContent;

        } else {

            $sLogin = $this->_getToxidLangCurlLogin();
            $sPwd = $this->_getToxidLangCurlPwd();

            $aPage = $this->_getRemoteContent($this->sUrl, $sLogin, $sPwd);
            $dontRedirectOnError = (bool) $this->_oConf->getConfigParam('toxidDontRedirectOnError');

            if($dontRedirectOnError === false && $aPage['info']['http_code'] !== 200) {

                switch ($aPage['info']['http_code']) {
                    case 500:
                        header ("HTTP/1.1 500 Internal Server Error");
                        header ('Location: '.$this->_oConf->getShopHomeURL());
                        oxRegistry::getUtils()->showMessageAndExit('');
                        break;
                    case 404:
                        header ("HTTP/1.1 404 Not Found");
                        header ('Location: '.$this->_oConf->getShopHomeURL());
                        oxRegistry::getUtils()->showMessageAndExit('');
                        break;
                    case 403:
                        header ("HTTP/1.1 403 Forbidden");
                        header ('Location: '.$this->_oConf->getShopHomeURL());
                        oxRegistry::getUtils()->showMessageAndExit('');
                        break;
                    case 401:
                        header ("HTTP/1.1 401 Unauthorized");
                        header ('Location: '.$this->_oConf->getShopHomeURL());
                        oxRegistry::getUtils()->showMessageAndExit('');
                        break;
                    case 0:
                        header ('Location: '.$this->_oConf->getShopHomeURL());
                        oxRegistry::getUtils()->showMessageAndExit('');
                        break;
                }
            }

            // Do this only when HTTP status 200 OK
            if($aPage['info']['http_code'] === 200) {

                // Especially for Wordpress-Frickel-Heinze
                // Kill everything befor the <?xml
                $this->_sPageContent = preg_replace('/.*<\?xml/ms', '<?xml', $aPage['content']);

                // try to save Toxid content in the cache
                if($this->blHttpCacheControl && $this->_iCacheTtl) {
                    $this->_writeCache('xml', '', $this->_sPageContent);
                }

            } else {

                $this->_sPageContent = null;

            }

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
     * @param $sLogin
     * @param $sPwd
     * @return array
     */
    protected function _getRemoteContent($sUrl, $sLogin='', $sPwd='')
    {
        $aResult = array();
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $sUrl);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

        // Use cURL Login/Password
        if($sLogin.$sPwd !== '') {
            curl_setopt($curl_handle, CURLOPT_USERPWD, $sLogin.':'.$sPwd);
        }

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
        return preg_replace('#index.php\??$#', '', $this->_oConf->getShopHomeURL()).$this->_getToxidLangSeoSnippet($iLangId);
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
        if ($this->_oConf->getConfigParam('toxidDontRewriteUrls') == true) {
            return $sContent;
        }

        if ($blMultiLang == false) {
            if ($iLangId === null) {
                $iLangId = oxRegistry::getLang()->getBaseLanguage();
            }
            $aLanguages = array($iLangId);
        } else {
            $aLangs = $this->_oConf->getConfigParam('aToxidCurlSource');
            arsort($aLangs);
            $aLanguages = array_keys($aLangs);
        }

        $sShopUrl = '';
        if ($this->_oConf->getEdition() === 'EE') {
	        if($this->_blIsSsl) {
		        $sShopUrl = $this->_oConf->getConfigParam('sMallSSLShopURL');
	        }
	        if(empty($sShopUrl)) { // http or fallback if empty https
		        $sShopUrl = $this->_oConf->getConfigParam('sMallShopURL');
	        }
        } else {
	        if($this->_blIsSsl) {
            	$sShopUrl = $this->_oConf->getConfigParam('sSSLShopURL');
            }
            if(empty($sShopUrl)) { // http or fallback if empty https
	            $sShopUrl = $this->_oConf->getConfigParam('sShopURL');
            }
        }

        if (substr($sShopUrl, -1) !== '/') {
            $sShopUrl .= '/';
        }

        foreach ($aLanguages as $iLangId ) {
            $target = $sShopUrl.$this->_getToxidLangSeoSnippet($iLangId).'/';
            $source = $this->_getToxidLangSource($iLangId);
            $pattern = '%href=(\'|")' . $source . '[^"\']*(.|/|\.html|\.php|\.asp)(\?[^"\']*)?(\'|")%';
            preg_match_all($pattern, $sContent, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $sContent = str_replace($match[0], str_replace($source, $target, $match[0]), $sContent);
            }
            unset($match);
        }

        // strip double http://example.com/http://example.com
        $sContent = preg_replace('/href=(\'|")(http[s]{0,1}:\/\/.+?)\/(http[s]{0,1}:\/\/.+?)(\'|")/', 'href=$1$2/$1', $sContent);

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
            $this->_aSourceUrlByLang = $this->_oConf->getConfigParam('aToxidCurlSource');
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
            $this->_aToxidLangUrlParam = $this->_oConf->getConfigParam('aToxidCurlUrlParams');
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
            $this->_aRewriteStartUrl = $this->_oConf->getConfigParam('aToxidCurlSeoSnippets');
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
        return ($this->_sCustomPage !== null) ? $this->_sCustomPage : '';
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
            $this->_aSearchUrl = $this->_oConf->getConfigParam('aToxidSearchUrl');
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
        $sLogin = $this->_getToxidLangCurlLogin();
        $sPwd = $this->_getToxidLangCurlPwd();

        $aSearchResults = $this->_getRemoteContent($sSearchStartUrl.$sKeywords, $sLogin, $sPwd);

        if ($aSearchResults['info']['http_code'] == 200) {
            $sSearchResult = $aSearchResults['content'];
            $sSearchResult = $this->_rewriteUrls($sSearchResult);
            $this->_aSearchCache[$sKeywords] = $sSearchResult;
        }

        if($this->_aSearchCache[$sKeywords] && $this->_getCharset() !== 'utf-8') {
            return mb_convert_encoding($this->_aSearchCache[$sKeywords], $this->_sCharset, "auto");
        }

        return $this->_aSearchCache[$sKeywords];
    }

    /**
     * Returns Toxid cache file name
     * @author Oliver Georgi <slackero@gmail.com>
     * @param string $sUrl
     * @return string
     */
    protected function _getCacheFileName($sType = null, $sSnippet = null)
    {
        if($sType === null || !in_array($sType, array('xml', 'html', 'txt'))) {
            return false;
        }

        // build cache file name based on type
        $sCacheFileName  = 'toxid_' . $this->sShopId . '_' . $this->sLangId . '_' . $this->_sSslSnippetAdd;
        // use an individual MD5 hash for current URL and snippet
        $sCacheFileName .= md5( $sSnippet === null ? $this->sUrl : $this->sUrl.$sSnippet );
        $sCacheFileName .= '.' . $sType;

        return $sCacheFileName;
    }

    /**
     * Sets Toxid cache directory
     * @author Oliver Georgi <slackero@gmail.com>
     * @return string
     */
    public function setCacheDir($sCacheDir = null) {

        if($sCacheDir === null || !is_dir($sCacheDir)) {
            $sCacheDir = realpath($this->_oConf->getConfigParam('sCompileDir')).DIRECTORY_SEPARATOR.'toxid'.DIRECTORY_SEPARATOR;
        }

        $this->_sCacheDir = $sCacheDir;

        return $this->_sCacheDir;
    }

    /**
     * Returns Toxid cache directory
     * @author Oliver Georgi <slackero@gmail.com>
     * @return string
     */
    protected function _getCacheDir()
    {
        return $this->_sCacheDir;
    }

    /**
     * Returns XML from local cache file if existing
     * @author Oliver Georgi <slackero@gmail.com>
     * @param string $sUrl
     * @return string
     */
    protected function _getCache($sType = null, $sSnippet = '', $iCacheTtl = null)
    {
        $sCacheFile = $this->_getCacheDir().$this->_getCacheFileName($sType, $sSnippet);

        if($this->_isCached($sCacheFile, $iCacheTtl)) {
            return @file_get_contents($sCacheFile);
        }

        return false;
    }

    /**
     * Save cache data to local cache file and
     * create the toxid cache directory if not exiting
     * @author Oliver Georgi <slackero@gmail.com>
     * @param string $sUrl
     * @param string $sCacheData
     * @return int|bool
     */
    protected function _writeCache($sType = null, $sSnippet = '', $sCacheData = '')
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

        if($sCacheFile = $sCacheDir.$this->_getCacheFileName($sType, $sSnippet)) {

            return @file_put_contents($sCacheFile, $sCacheData);

        }

        return false;
    }

    /**
     * Returns if the requested cache file exists and the cache TTL is valid (or not)
     * @author Oliver Georgi <slackero@gmail.com>
     * @param string $sCacheFile
     * @return bool
     */
    protected function _isCached($sCacheFile, $iCacheTtl = null)
    {
        if($iCacheTtl === null) {
            $iCacheTtl = $this->_iCacheTtl;
        }
        if($iCacheTtl && is_file($sCacheFile) && (filemtime($sCacheFile) + $iCacheTtl >= time())) {
            return true;
        }

        return false;
    }

    /**
     * Set the cache TTL based on Toxid configuration
     * @author Oliver Georgi <slackero@gmail.com>
     * @return int
     */
    public function setCacheTtl($iCacheTtl = null)
    {
        if($iCacheTtl === null) {
            $iCacheTtl = $this->_oConf->getConfigParam('iToxidCacheTTL');
        }

        $this->_iCacheTtl = (int) $iCacheTtl;

        return $this->_iCacheTtl;
    }

    /**
     * Get the cache TTL
     * @author Oliver Georgi <slackero@gmail.com>
     * @return int
     */
    protected function _getCacheTtl()
    {
        return (int) $this->_iCacheTtl;
    }

    /**
     * Set the Toxid charset
     * @author Oliver Georgi <slackero@gmail.com>
     * @return string
     */
    protected function _setCharset()
    {
        // set charset used for converting if not in UTF-8 mode
        if($this->_oConf->getConfigParam('iUtfMode') === 0) {
            $this->_sCharset = oxRegistry::getLang()->translateString('charset');
        } else {
            $this->_sCharset = 'utf-8';
        }

        return $this->_sCharset;
    }

    /**
     * Get the Toxid charset
     * @author Oliver Georgi <slackero@gmail.com>
     * @return string
     */
    protected function _getCharset()
    {
        if($this->_sCharset === null) {
            $this->_setCharset();
        }

        return $this->_sCharset;
    }

    /**
     * Get language specific Toxid access control login
     * @param int $iLangId
     * @param bool $blReset reset object value, and get url again
     * @return string
     */
    protected function _getToxidLangCurlLogin($iLangId = null, $blReset = false)
    {
        if ($this->_aToxidCurlLogin === null || $blReset) {
            $this->_aToxidCurlLogin = $this->_oConf->getConfigParam('aToxidCurlLogin');
        }
        if ($iLangId === null) {
            $iLangId = oxRegistry::getLang()->getBaseLanguage();
        }

        return isset($this->_aToxidCurlLogin[$iLangId]) ? $this->_aToxidCurlLogin[$iLangId] : '';
    }

    /**
     * Get language specific Toxid access control password
     * @param int $iLangId
     * @param bool $blReset reset object value, and get url again
     * @return string
     */
    protected function _getToxidLangCurlPwd($iLangId = null, $blReset = false)
    {
        if ($this->_aToxidCurlPwd === null || $blReset) {

            $oDecryptor = oxNew('oxDecryptor');
            $encryptKey = $this->_oConf->getConfigParam('dbPwd');
            $this->_aToxidCurlPwd = $this->_oConf->getConfigParam('aToxidCurlPwd');

            foreach($this->_aToxidCurlPwd as $lang => $value) {
                if($value !== '') {
                    $this->_aToxidCurlPwd[$lang] = $oDecryptor->decrypt($value, $encryptKey);
                }
            }
        }
        if ($iLangId === null) {
            $iLangId = oxRegistry::getLang()->getBaseLanguage();
        }

        return isset($this->_aToxidCurlPwd[$iLangId]) ? $this->_aToxidCurlPwd[$iLangId] : '';
    }


}
