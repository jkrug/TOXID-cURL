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

class toxid_curl_oxviewconfig extends toxid_curl_oxviewconfig_parent
{
    /**
     * calls _injectTplVariable and returns parent constructor result
     */
    public function __construct()
    {
        $this->_injectTplVariable();
        return parent::__construct();
    }

    /**
     * if config variable sTplVariable is set,
     * sets template variable with getToxid() result
     * @return void
     */
    protected function _injectTplVariable()
    {
        $oConfig = oxRegistry::getConfig();
        $sTplVariableName = $oConfig->getConfigParam('sTplVariable');
        if ($sTplVariableName) {
            $oConfig->getActiveView()->addTplParam($sTplVariableName, $this->getToxid());
        }
    }

    /**
     * returns instance of toxidCurl
     * @return toxidCurl
     */
    public function getToxid()
    {
        $toxidCurl = oxRegistry::get('toxidcurl');
        if (!$toxidCurl->getInitialized()) {
            $smartyParser = oxNew('toxid_curl_smarty_parser');
            $toxidCurl->init(
                $smartyParser
            );
        }
        return $toxidCurl;
    }
}
