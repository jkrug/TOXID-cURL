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
 * TOXID - loads CMS-pages and displays them
 */
class toxid_curl extends oxUBase
{
    /**
     * Current view template
     * @var string
     */
    protected $_sThisTemplate = 'toxid_curl.tpl';
    
    /**
     * stores URL from which typo3 content is loaded.
     * @var array
     */
    protected $_aSourceUrlByLang = null;
    
    /**
     * sets canonical URL to the default Blog URL
     *
     * if the blog is accessible through (T)OXID & Typo3, this should be a good way to prevent duplicate content
     * @var string
     */
    public function getCanonicalUrl()
    {
        if ($this->_aSourceUrlByLang === null) {
            $this->_aSourceUrlByLang = $this->getConfig()->getConfigParam('aToxidCurlSource');
        }

        $this->_aSeoUrls = $this->getConfig()->getConfigParam('aToxidCurlSeoSnippets');
        
        if ($iLangId === null) {
            $iLangId = oxLang::getInstance()->getBaseLanguage();
        }
        
        $this->_sUrl = $this->_aSeoUrls[$iLangId];
        $sRegUri = preg_replace("/^\/".$this->_sUrl."\//i","",$_SERVER["REQUEST_URI"]);

        return $this->_aSourceUrlByLang[$iLangId].$sRegUri;
    }
    
    /**
     * Template variable getter. Returns tag title
     *
     * @return string
     */
    public function getTitle()
    {
        $sTitle = toxidCurl::getInstance()->_sPageTitle;
        return $sTitle;
    }
    
    /**
     * Template variable getter. Returns meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $this->_xMetaDescription = strip_tags(toxidCurl::getInstance()->_sPageDescription);
        if ( $this->_sMetaDescription === null ) {
            $this->_sMetaDescription = false;

            // set special meta description ?
            if ( ( $sDescription = $this->_xMetaDescription ) ) {
                $this->_sMetaDescription = $sDescription;
            } else {
                $this->_sMetaDescription = $this->_prepareMetaDescription( $this->_xMetaDescription );
            }
        }
        
        return $this->_sMetaDescription;
    }
    
    /**
     * Template variable getter. Returns meta keywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        $this->_xMetaKeywords = strip_tags(toxidCurl::getInstance()->_sPageKeywords);
        if ( $this->_sMetaKeywords === null ) {
            $this->_sMetaKeywords = false;

            // set special meta keywords ?
            if ( ( $sKeywords = $this->_xMetaKeywords ) ) {
                $this->_sMetaKeywords = $sKeywords;
            } else {
                $this->_sMetaKeywords = $this->_prepareMetaKeyword( $this->_xMetaKeywords, true );
            }
        }

        return $this->_sMetaKeywords;
    }
    
    /**
     * regular render function
     */
    public function render()
    {
        if ( version_compare(oxConfig::getInstance()->getVersion(), '4.5.0') < 0 ) {
            $this->_sThisTemplate = 'toxid_curl.tpl';
        }
        return parent::render();
    }
}
