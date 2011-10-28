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
 * @version   TOXID cURL 1.0.4
 * @version   toxid_curl_oxseodecorder.php 2011-04-28 00:06 jkrug $
 */
 
class toxid_curl_oxseodecoder extends toxid_curl_oxseodecoder_parent
{
    
    public function decodeUrl( $sSeoUrl )
    {
        // check, if SeoUrl starts with t3contenturl
        $blIsToxidPage = $this->detectToxidAndLang($sSeoUrl);
        if( !$blIsToxidPage )
        {
            return parent::decodeUrl($sSeoUrl);
        }else{
            $aRet['cl'] = 'toxid_curl';
            $aRet['lang'] = $blIsToxidPage['lang'];
            oxLang::getInstance()->setBaseLanguage($aRet['lang']);
            $toxidUrl =  $blIsToxidPage['url'];
            $this->getConfig()->setConfigParam('sToxidCurlPage',$toxidUrl);
            return $aRet;
        }
    }


    /**
     * detect if page is toxidPage
     * if so, return array with langId and URL part
     */
    protected function detectToxidAndLang($sSeoUrl){
        $seoSnippets = $this->getConfig()->getConfigParam('aToxidCurlSeoSnippets');
        foreach($seoSnippets as $langId => $snippet)
        {
            if(strpos( $sSeoUrl, $snippet.'/') !== FALSE)
            {
                $aUrlSplit = explode($snippet.'/', $sSeoUrl);
                $toxidInfo = array(
                                'lang' => $langId,
                                'url' => $aUrlSplit[1],
                             );
                return $toxidInfo;
            }
        }
        // if nothing was found, return false
        return false;
    }
}
