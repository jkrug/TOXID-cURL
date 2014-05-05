<?php

class toxid_setup_main extends oxAdminView
{
    protected $_aStandardSettings = array(
        array(  'name'      => 'aToxidCurlSource',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aToxidCurlSourceSsl',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aToxidSearchUrl',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aToxidCurlUrlParams',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'aToxidCurlSeoSnippets',
                'type'      => 'arr',
                'global'    => false,
            ),
        array(  'name'      => 'toxidDontRewriteUrls',
                'type'      => 'bl',
                'global'    => false,
            ),
        array(  'name'      => 'iToxidCacheTTL',
                'type'      => 'str',
                'global'    => false,
            ),
        array(  'name'      => 'iToxidCacheRandomize',
                'type'      => 'str',
                'global'    => false,
            ),
    );
    
    protected $_sThisTemplate = 'toxid_setup_main.tpl';

    const CONFIG_MODULE_NAME = 'module:toxid_curl';
    
    public function render()
    {
        $oConf = oxRegistry::getConfig();
        
        foreach ( $this->_aStandardSettings as $aSetting ) {
            // For global settings, the associated shop id is the base shop id.
            $iTargetShopId = $aSetting['global'] ? $oConf->getBaseShopId() : $sShopId;
            $this->_aViewData[ $aSetting['name'] ] = $oConf->getShopConfVar( $aSetting['name'], $iTargetShopId, self::CONFIG_MODULE_NAME );
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
        
        $aParams = $oConf->getParameter( "editval" );
        if($aParams['toxidDontRewriteUrls'] != '1')
        {
            $aParams['toxidDontRewriteUrls'] = 0;
        }
        
        $sShopId = $oConf->getShopId();
        
        foreach ( $this->_aStandardSettings as $aSetting ) {
            // For global settings, the associated shop id is the base shop id.
            $iTargetShopId = $aSetting['global'] ? $oConf->getBaseShopId() : $sShopId;
            $oConf->saveShopConfVar( $aSetting['type'], $aSetting['name'], $aParams[ $aSetting['name'] ], $iTargetShopId, self::CONFIG_MODULE_NAME );
        }
    }
}