<?php

class toxid_setup_main extends oxAdminView
{

    protected $_sThisTemplate = 'toxid_setup_main.tpl';

    const CONFIG_MODULE_NAME = 'module:toxid_curl';

    public function render()
    {
        $oConf = oxRegistry::getConfig();

        $this->_aViewData['aToxidCurlSource']            = $oConf->getShopConfVar('aToxidCurlSource');
        $this->_aViewData['aToxidCurlSourceSsl']         = $oConf->getShopConfVar('aToxidCurlSourceSsl');
        $this->_aViewData['aToxidSearchUrl']             = $oConf->getShopConfVar('aToxidSearchUrl');
        $this->_aViewData['aToxidCurlUrlParams']         = $oConf->getShopConfVar('aToxidCurlUrlParams');
        $this->_aViewData['aToxidCurlSeoSnippets']       = $oConf->getShopConfVar('aToxidCurlSeoSnippets');
        $this->_aViewData['toxidDontRewriteUrls']        = $oConf->getShopConfVar('toxidDontRewriteUrls');
        $this->_aViewData['bToxidDontPassPostVarsToCms'] = $oConf->getShopConfVar('bToxidDontPassPostVarsToCms');
        $this->_aViewData['toxidCacheEnabled']           = $oConf->getShopConfVar('toxidCacheEnabled');
        $this->_aViewData['toxidCacheSnippetsEnabled']   = $oConf->getShopConfVar('toxidCacheSnippetsEnabled');
        $this->_aViewData['iToxidCacheTTL']              = intval($oConf->getShopConfVar('iToxidCacheTTL'));
        $this->_aViewData['aToxidCurlLogin']             = $oConf->getShopConfVar('aToxidCurlLogin');
        $this->_aViewData['aToxidCurlPwd']               = $oConf->getShopConfVar('aToxidCurlPwd');

        return parent::render();
    }

    /**
     * Saves the settings
     * @return void
     */
    public function save()
    {
        $oConf = oxRegistry::getConfig();
        $sShopId = $oConf->getShopId();
        $aParams = $oConf->getRequestParameter( "editval" );

        $aParams['toxidDontRewriteUrls']        = empty($aParams['toxidDontRewriteUrls']) ? 0 : 1;
        $aParams['toxidCacheEnabled']           = empty($aParams['toxidCacheEnabled']) ? 0 : 1;
        $aParams['toxidCacheSnippetsEnabled']   = empty($aParams['toxidCacheSnippetsEnabled']) ? 0 : 1;
        $aParams['toxidDontRedirectOnError']    = empty($aParams['toxidDontRedirectOnError']) ? 0 : 1;
        $aParams['iToxidCacheTTL']              = empty($aParams['iToxidCacheTTL']) ? 0 : intval($aParams['iToxidCacheTTL']);
        $aParams['bToxidDontPassPostVarsToCms'] = empty($aParams['bToxidDontPassPostVarsToCms']) ? 0 : 1;

        $oConf->saveShopConfVar( 'arr', 'aToxidCurlSource', $aParams['aToxidCurlSource'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'arr', 'aToxidCurlSourceSsl', $aParams['aToxidCurlSourceSsl'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'arr', 'aToxidSearchUrl', $aParams['aToxidSearchUrl'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'arr', 'aToxidCurlUrlParams', $aParams['aToxidCurlUrlParams'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'arr', 'aToxidCurlSeoSnippets', $aParams['aToxidCurlSeoSnippets'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'bl', 'toxidDontRewriteUrls', $aParams['toxidDontRewriteUrls'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'bl', 'bToxidDontPassPostVarsToCms', $aParams['bToxidDontPassPostVarsToCms'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'bl', 'toxidCacheEnabled', $aParams['toxidCacheEnabled'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'bl', 'toxidCacheSnippetsEnabled', $aParams['toxidCacheSnippetsEnabled'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'bl', 'toxidDontRedirectOnError', $aParams['toxidDontRedirectOnError'], $sShopId, self::CONFIG_MODULE_NAME );
        $oConf->saveShopConfVar( 'num', 'iToxidCacheTTL', $aParams['iToxidCacheTTL'], $sShopId, self::CONFIG_MODULE_NAME );

        // htaccess Login
        $oConf->saveShopConfVar( 'arr', 'aToxidCurlLogin', $aParams['aToxidCurlLogin'], $sShopId, self::CONFIG_MODULE_NAME );
        // htaccess Password
        if(isset($aParams['aToxidCurlPwd']) && count($aParams['aToxidCurlPwd'])) {

            $oEncryptor = oxNew('oxEncryptor');

            // get old password settings
            $aToxidCurlPwd = $oConf->getShopConfVar('aToxidCurlPwd');
            $encryptKey = $oConf->getConfigParam('dbPwd');
            foreach($aParams['aToxidCurlPwd'] as $lang => $value) {
                $value = trim($value);
                if($value !== '') {
                    if(isset($aToxidCurlPwd[$lang]) && $value === $aToxidCurlPwd[$lang]) {
                        $aParams['aToxidCurlPwd'][$lang] = $aToxidCurlPwd[$lang];
                    } else {
                        $aParams['aToxidCurlPwd'][$lang] = $oEncryptor->encrypt($value, $encryptKey);
                    }
                } else {
                    $aParams['aToxidCurlPwd'][$lang] = '';
                }
            }
            $oConf->saveShopConfVar( 'arr', 'aToxidCurlPwd', $aParams['aToxidCurlPwd'], $sShopId, self::CONFIG_MODULE_NAME );
        }
    }
}
