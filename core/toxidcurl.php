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
     * sets cache TTL (in seconds)
     * @var int
     */
    protected $_iCacheTtl = null;

    /**
     * sets active Oxid charset
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
	 * set active Shop ID
	 * @var string
	 */
	public $sShopId = null;

	/**
	 * set active language
	 * @var string
	 */
	public $sLangId = null;
	
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
	 * Construct, load required configurations only once
	 */
	publich function __construct() {
		
		$this->_oConf = $this->getConfig();
		$this->sShopId = $this->_oConf->getActiveShop()->getId();
		$this->sLangId = oxRegistry::getLang()->getBaseLanguage();
		
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
        if($snippet == null) {
            return '<strong style="color:red;">TOXID: Please add part, you want to display!</strong>';
        }

        if($iCacheTtl === null) {
            $iCacheTtl = $this->_getCacheTtl();
		}

        $oConf   = $this->getConfig();
        $sShopId = $oConf->getActiveShop()->getId();
        $sLangId = oxRegistry::getLang()->getBaseLanguage();
		
		// check if snippet text has a ttl
		if($iCacheTtl && $this->_oSxToxid === null) {
	        $sCacheIdent = 'toxid_snippet_'.$snippet.'_'.$sShopId.'_'.$sLangId;
			$oUtils = oxRegistry::getUtils();
			// and is in cache
			if($sCacheContent = $oUtils->fromFileCache($sCacheIdent)) {
				$oUtilsServer = oxRegistry::get('oxUtilsServer');
				if($oUtilsServer->getServerVar('HTTP_CACHE_CONTROL') !== 'no-cache') {
					return $sCacheContent;
				}
			}
		} else {
			// no caching for this snippet
			$sCacheIdent = null;
		}
		
        if ($customPage != '') {
            $this->_sCustomPage = $customPage;
        }

        $sText = $this->_getSnippetFromXml($snippet);
        $sText = $this->_rewriteUrls($sText, null, $blMultiLang);

        $sText = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
            $sText,
            $snippet.'_'.$sShopId.'_'.$sLangId,
            null,
            true
        );
		
        $this->_sCustomPage = null;

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

        if($this->_getCharset() !== 'utf-8')
        {
            $sText = str_replace(array('„', '“', '´', '`','€'), array('&bdquo;', '&ldquo;', '&acute;', '&#96;', '&euro;'), $sText);
            return mb_convert_encoding($sText, $this->_sCharset, "auto");
        }
		
        // save in cache if $sCacheIdent is set
        if($sCacheIdent !== null) {
            $oUtils->toFileCache($sCacheIdent, $sText, $iCacheTtl);
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
        $aMetadataKeys = array('title', 'description', 'keywords');

        if($sMetadata === null || !in_array($sMetadata, $aMetadataKeys)) {
            return '<strong style="color:red;">No TOXID Metadata key given: title, description or keywords</strong>';
        }
        
		if($iCacheTtl === null) {
            $iCacheTtl = $this->_getCacheTtl();
		}
        $oConf   = $this->getConfig();
        $sShopId = $oConf->getActiveShop()->getId();
        $sLangId = oxRegistry::getLang()->getBaseLanguage();
		
		// check if snippet text has a ttl
		if($iCacheTtl && $this->_oSxToxid === null) {
	        $sCacheIdent = 'toxid_snippet_'.$sMetadata.'_'.$sShopId.'_'.$sLangId;
			$oUtils = oxRegistry::getUtils();
			// and is in cache
			if($sCacheContent = $oUtils->fromFileCache($sCacheIdent)) {
				$oUtilsServer = oxRegistry::get('oxUtilsServer');
				if($oUtilsServer->getServerVar('HTTP_CACHE_CONTROL') !== 'no-cache') {
					return $sCacheContent;
				}
			}
		} else {
			// no caching for this snippet
			$sCacheIdent = null;
		}
		
        if ($customPage != '') {
            $this->_sCustomPage = $customPage;
        }

        if($this->_blToxidParsedMeta === false || $customPage !== null) {

            // Title
            $sMetadataContent = $this->_rewriteUrls($this->_getSnippetFromXml('//metadata//title'), null, $blMultiLang);

            $this->_sPageTitle = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
                $sMetadataContent,
                '//metadata//title_'.$sShopId.'_'.$sLangId,
                null,
                true
            );

            // Description
            $sMetadataContent = $this->_rewriteUrls($this->_getSnippetFromXml('//metadata//description'), null, $blMultiLang);

            $this->_sPageDescription = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
                $sMetadataContent,
                '//metadata//description_'.$sShopId.'_'.$sLangId,
                null,
                true
            );

            // Keywords
            $sMetadataContent = $this->_rewriteUrls($this->_getSnippetFromXml('//metadata//keywords'), null, $blMultiLang);

            $this->_sPageKeywords = oxRegistry::get("oxUtilsView")->parseThroughSmarty(
                $sMetadataContent,
                '//metadata//keywords_'.$sShopId.'_'.$sLangId,
                null,
                true
            );

            $this->_blToxidParsedMeta = true;

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

        if($sMetadataContent && $this->_getCharset() !== 'utf-8')
        {
            $sMetadataContent = str_replace(array('„', '“', '´', '`','€'), array('&bdquo;', '&ldquo;', '&acute;', '&#96;', '&euro;'), $sMetadataContent);
            return mb_convert_encoding($sMetadataContent, $this->_sCharset, "auto");
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

        $source = $this->_getToxidLangSource();
        $page = $this->getConfig()->getConfigParam('sToxidCurlPage');
        $param = $this->_getToxidLangUrlParam();
        $custom  = $this->_getToxidCustomPage();
        $sUrl = $source.$custom.$page.$param;

        // check if cache TTL and requested URL is cached
        // if not get remote content
        if(!$this->getCacheTtl() || !($cachedPage = $this->_getCachedXml($sUrl))) {

            $sLogin = $this->_getToxidLangCurlLogin();
            $sPwd = $this->_getToxidLangCurlPwd();

            $aPage = $this->_getRemoteContent($sUrl, $sLogin, $sPwd);
            $dontRedirectOnError = (bool) $this->getConfig()->getConfigParam('toxidDontRedirectOnError');

            if($dontRedirectOnError === false && $aPage['info']['http_code'] !== 200) {

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
                    case 403:
                        header ("HTTP/1.1 403 Forbidden");
                        header ('Location: '.$this->getConfig()->getShopHomeURL());
                        oxRegistry::getUtils()->showMessageAndExit('');
                        break;
                    case 401:
                        header ("HTTP/1.1 401 Unauthorized");
                        header ('Location: '.$this->getConfig()->getShopHomeURL());
                        oxRegistry::getUtils()->showMessageAndExit('');
                        break;
                    case 0:
                        header ('Location: '.$this->getConfig()->getShopHomeURL());
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
                if($this->_iCacheTtl) {
                    $this->_setCachedXml($sUrl, $this->_sPageContent);
                }

            } else {

                $this->_sPageContent = null;

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
        $sLogin = $this->_getToxidLangCurlLogin();
        $sPwd = $this->_getToxidLangCurlPwd();

        $aSearchResults = $this->_getRemoteContent($sSearchStartUrl.$sKeywords, $sLogin, $sPwd);

        if ($aSearchResults['info']['http_code'] == 200) {
            $sSearchResult = $aSearchResults['content'];
            $sSearchResult = $this->_rewriteUrls($sSearchResult);
            $this->_aSearchCache[$sKeywords] = $sSearchResult;
        }

        if($this->_aSearchCache[$sKeywords] && $this->_getCharset() !== 'utf-8')
        {
            return mb_convert_encoding($this->_aSearchCache[$sKeywords], $this->_sCharset, "auto");
        }

        return $this->_aSearchCache[$sKeywords];
    }

    /**
     * Returns if the requested cache file exists and the cache TTL is valid (or not)
     * @author Oliver Georgi <slackero@gmail.com>
     * @param string $sCacheFile
     * @return bool
     */
    protected function _isCached($sCacheFile)
    {
        if(is_file($sCacheFile) && (filemtime($sCacheFile) + $this->_iCacheTtl >= time())) {
            return true;
        }

        return false;
    }

    /**
     * Set the cache TTL based on Toxid configuration
     * @author Oliver Georgi <slackero@gmail.com>
     * @return int
     */
    protected function _setCacheTtl()
    {
        // Cache or not to cache
        if($this->getConfig()->getConfigParam('toxidCacheEnabled')) {
            $this->_iCacheTtl = intval($this->getConfig()->getConfigParam('iToxidCacheTTL'));
        } else {
            $this->_iCacheTtl = 0;
        }

        return $this->_iCacheTtl;
    }

    /**
     * Get the cache TTL
     * @author Oliver Georgi <slackero@gmail.com>
     * @return int
     */
    protected function _getCacheTtl()
    {
        if($this->_iCacheTtl === null) {
           return $this->_setCacheTtl();
        }

        return $this->_iCacheTtl;
    }

    /**
     * Set the Toxid charset
     * @author Oliver Georgi <slackero@gmail.com>
     */
    protected function _setCharset()
    {
        // set charset used for converting if not in UTF-8 mode
	    if($this->getConfig()->getConfigParam('iUtfMode') === 0) {
	        $this->_sCharset = oxRegistry::getLang()->translateString('charset');
	    } else {
	     	$this->_sCharset = 'utf-8';
	    }
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
            $this->_aToxidCurlLogin = $this->getConfig()->getConfigParam('aToxidCurlLogin');
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
            $encryptKey = $this->getConfig()->getConfigParam('dbPwd');
            $this->_aToxidCurlPwd = $this->getConfig()->getConfigParam('aToxidCurlPwd');

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
