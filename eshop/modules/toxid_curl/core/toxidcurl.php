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
 * @version   TOXID cURL 1.1.0
 * @version   toxidcurl.php 2011-05-28 18:02 jkrug $
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
     * @var string
     */
    protected $_sSourceUrlByLang = null;

    /**
     * Stores URI to which typo3 urls will be converted, so it will be loaded in oxid
     * @var string
     */
    protected $_sRewriteStartUrl = null;

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
     * should later generate Cache
     */
    protected function _generateCache($blReset = false)
	{
		if ($this->_oSxToxid !== null && !$blReset) {
            return $this->_oSxToxid;
        }
        $this->_readURL();
		$this->_sPageContent = $this->_rewriteUrls($this->_sPageContent);
		$this->_oSxToxid = simplexml_load_string($this->_sPageContent);
        return $this->_oSxToxid;

	}
	
	/**
     * returns the called snippet
     */
    public function getCmsSnippet($snippet=null)
	{
        $this->_generateCache();
		if($snippet == null)
		{
			return '<strong style="color:red;">TOXID: Please add part, you want to display!</strong>';
		}

        $aXpathSnippets = $this->_oSxToxid->xpath('//'.$snippet.'[1]');
        $sText = $aXpathSnippets[0];
        $sShopId = $this->getConfig()->getActiveShop()->getId();
        $sLangId = oxLang::getInstance()->getBaseLanguage();
        $sText = oxUtilsView::getInstance()->parseThroughSmarty(
            $sText,
            $snippet.'_'.$sShopId.'_'.$sLangId
        );
        return $sText;

	}
	
	/**
	 * returns raw string from CMS-page
	 */
	protected function _readUrl()
	{
		$source = $this->_getToxidLangSource();
		$page = $this->getConfig()->getConfigParam('sToxidCurlPage');
		$param = $this->getConfig()->getConfigParam('sToxidCurlUrlParam');

		$aPage = $this->_getRemoteContent($source.$page.$param);

        $this->_sPageContent = $aPage['content'];
		
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
	 * returns string with toxidStartUrl
	 */
	public function getToxidStartUrl()
	{
		return preg_replace('#index.php\??$#', '', $this->getConfig()->getShopHomeURL()).$this->_getToxidLangSeoSnippet();
	}

    /**
     * rewrites given string URL's, which belongs to typo3 and configured in aToxidCurlSource
     * @param string $sContent
     * @return string with changed URL's 
     */
	protected function _rewriteUrls($sContent)
	{
		$target      = $this->getConfig()->getConfigParam('sShopURL').$this->_getToxidLangSeoSnippet().'/';
		$source    = str_replace('.','\.',$this->_getToxidLangSource());
		$actual    = '%href="'.$source.'(?=.*?.html)%';
		$should    = 'href="'.$target;
		return preg_replace($actual, $should, $sContent);
	}

    /**
     * returns string with language specific sourceUrl
     * @param bool $blReset reset object value, and get url again
     * @return string
     */
	protected function _getToxidLangSource($blReset = false)
	{
        if ($this->_sSourceUrlByLang !== null && !$blReset) {
            return $this->_sSourceUrlByLang;
        }
		$langUrls = $this->getConfig()->getConfigParam('aToxidCurlSource');
		$this->_sSourceUrlByLang = $langUrls[oxLang::getInstance()->getBaseLanguage()];
		return $this->_sSourceUrlByLang;
	}

    /**
     * returns string with language specific toxidSeoSnippet
     * @param bool $blReset reset object value, and get url again
     * @return string
     */
	protected function _getToxidLangSeoSnippet($blReset = false)
	{
        if ($this->_sRewriteStartUrl !== null && !$blReset) {
            return $this->_sRewriteStartUrl;
        }

		$langSeoSnippets = $this->getConfig()->getConfigParam('aToxidCurlSeoSnippets');
		$this->_sRewriteStartUrl = $langSeoSnippets[oxLang::getInstance()->getBaseLanguage()];
		return $this->_sRewriteStartUrl;
	}
        }
