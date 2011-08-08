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
     * initial load of the Content
     */
    public function loadCmsPage()
	{
		$this->_generateCache();
	}
	
	/**
     * should later generate Cache
     */
    protected function _generateCache()
	{
		$this->_readURL();
		$this->_rewriteUrls();
		$source = $this->_getToxidLangSource();
		$this->_oSxToxid = simplexml_load_string($this->_sPageContent);

	}
	
	/**
     * returns the called snippet
     */
    public function getCmsSnippet($snippet=null)
	{
		if($snippet == null)
		{
			return '<strong style="color:red;">TOXID: Please add part, you want to display!</strong>';
		}else{
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
	}
	
	/**
	 * returns raw string from CMS-page
	 */
	protected function _readUrl()
	{
		$source = $this->_getToxidLangSource();

		$page = $this->getConfig()->getConfigParam('sToxidCurlPage');

		$param = $this->getConfig()->getConfigParam('sToxidCurlUrlParam');
		
		$curl_handle = curl_init();
		
		// set URL
        curl_setopt($curl_handle, CURLOPT_URL, $source.$page.$param);

        //return the transfer as a string
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		
        $this->_sPageContent = curl_exec($curl_handle);
		
		$requestInfo = curl_getinfo($curl_handle);
		
		switch ($requestInfo[http_code])
		{
			case 500:
				header ("HTTP/1.1 500 Internal Server Error");
				header ('Location: '.$this->getConfig()->getShopHomeURL());
				exit();
			case 404:
				header ("HTTP/1.1 404 Not Found");
				header ('Location: '.$this->getConfig()->getShopHomeURL());
				exit();
		}

        // close curl resource to free up system resources
        curl_close($curl_handle); 
	}
	
	/**
	 * returns string with toxidStartUrl
	 */
	public function getToxidStartUrl()
	{
		return preg_replace('#index.php\??$#', '', $this->getConfig()->getShopHomeURL()).$this->_getToxidLangSeoSnippet();
	}
	
	/**
	 * returns string from CMS-page with corrected URLs
	 */	
	protected function _rewriteUrls()
	{
		$target      = $this->getConfig()->getConfigParam('sShopURL').$this->_getToxidLangSeoSnippet().'/';
		$source    = str_replace('.','\.',$this->_getToxidLangSource());
		$actual    = '%href="'.$source.'(?=.*?.html)%';
		$should    = 'href="'.$target;
		$this->_sPageContent = preg_replace($actual, $should, $this->_sPageContent);
	}
	
	/**
	 * returns string with language specific sourceUrl
	 */	
	protected function _getToxidLangSource()
	{
		$langUrls = $this->getConfig()->getConfigParam('aToxidCurlSource');
		$sLangSource = $langUrls[oxLang::getInstance()->getBaseLanguage()];
		return $sLangSource;
	}
	
	/**
	 * returns string with language specific toxidSeoSnippet
	 */	
	protected function _getToxidLangSeoSnippet()
	{
		$langSeoSnippets = $this->getConfig()->getConfigParam('aToxidCurlSeoSnippets');
		$sLangSeoSnippet = $langSeoSnippets[oxLang::getInstance()->getBaseLanguage()];
		return $sLangSeoSnippet;
	}
}