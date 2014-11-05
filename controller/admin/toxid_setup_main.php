<?php

class toxid_setup_main extends oxAdminView
{

    protected $_sThisTemplate = 'toxid_setup_main.tpl';

    const CONFIG_MODULE_NAME = 'module:toxid_curl';

    public function render()
    {
        $oConf = oxRegistry::getConfig();
        $this->_aViewData['aToxidCurlSource']       = $oConf->getShopConfVar('aToxidCurlSource');
        $this->_aViewData['aToxidCurlSourceSsl']    = $oConf->getShopConfVar('aToxidCurlSourceSsl');
        $this->_aViewData['aToxidSearchUrl']        = $oConf->getShopConfVar('aToxidSearchUrl');
        $this->_aViewData['aToxidCurlUrlParams']    = $oConf->getShopConfVar('aToxidCurlUrlParams');
        $this->_aViewData['aToxidCurlSeoSnippets']  = $oConf->getShopConfVar('aToxidCurlSeoSnippets');
        $this->_aViewData['toxidDontRewriteUrls']   = $oConf->getShopConfVar('toxidDontRewriteUrls');
        $this->_aViewData['toxidCacheEnabled']		= $oConf->getShopConfVar('toxidCacheEnabled');
        $this->_aViewData['iToxidCacheTTL']			= intval($oConf->getShopConfVar('iToxidCacheTTL'));
        if(empty($this->_aViewData['iToxidCacheTTL'])) {
	        $this->_aViewData['iToxidCacheTTL'] = 3600;
        }
        return parent::render();
    }

    /**
     * Saves the settings
     * @return void
     */
    public function save()
    {
        $oConf = oxRegistry::getConfig();
        $aParams = $oConf->getRequestParameter( "editval" );
        if(empty($aParams['toxidDontRewriteUrls']))
        {
            $aParams['toxidDontRewriteUrls'] = 0;
        }
        if(empty($aParams['toxidCacheEnabled']))
        {
            $aParams['toxidCacheEnabled'] = 0;
        }
        if(empty($aParams['iToxidCacheTTL']))
        {
            $aParams['iToxidCacheTTL'] = 3600;
        }
        $sShopId = $oConf->getShopId();
        $oConf->saveShopConfVar( 'arr', 'aToxidCurlSource', $aParams['aToxidCurlSource'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'arr', 'aToxidCurlSourceSsl', $aParams['aToxidCurlSourceSsl'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'arr', 'aToxidSearchUrl', $aParams['aToxidSearchUrl'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'arr', 'aToxidCurlUrlParams', $aParams['aToxidCurlUrlParams'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'arr', 'aToxidCurlSeoSnippets', $aParams['aToxidCurlSeoSnippets'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'bl', 'toxidDontRewriteUrls', $aParams['toxidDontRewriteUrls'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'bl', 'toxidCacheEnabled', $aParams['toxidCacheEnabled'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'num', 'iToxidCacheTTL', $aParams['iToxidCacheTTL'], $sShopId, self::CONFIG_MODULE_NAME );
    }
}