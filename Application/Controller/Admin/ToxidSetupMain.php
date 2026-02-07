<?php

namespace Toxid\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * TOXID Admin Setup Main - Configuration controller
 */
class ToxidSetupMain extends \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
{
    const CONFIG_MODULE_NAME = 'module:toxid_curl';

    protected $_sThisTemplate = 'admin/toxid_setup_main.tpl';

    public function render()
    {
        $oConf = Registry::getConfig();

        $this->_aViewData['aToxidCurlSource'] = $oConf->getShopConfVar('aToxidCurlSource');
        $this->_aViewData['aToxidCurlSourceSsl'] = $oConf->getShopConfVar('aToxidCurlSourceSsl');
        $this->_aViewData['aToxidSearchUrl'] = $oConf->getShopConfVar('aToxidSearchUrl');
        $this->_aViewData['aToxidCurlUrlParams'] = $oConf->getShopConfVar('aToxidCurlUrlParams');
        $this->_aViewData['aToxidCurlSeoSnippets'] = $oConf->getShopConfVar('aToxidCurlSeoSnippets');
        $this->_aViewData['toxidDontRewriteRelUrls'] = $oConf->getShopConfVar('toxidDontRewriteRelUrls');
        $this->_aViewData['toxidDontRewriteFileExtension'] = $oConf->getShopConfVar('toxidDontRewriteFileExtension');
        $this->_aViewData['toxidRewriteUrlEncoded'] = $oConf->getShopConfVar('toxidRewriteUrlEncoded');
        $this->_aViewData['toxidDontRewriteUrls'] = $oConf->getShopConfVar('toxidDontRewriteUrls');
        $this->_aViewData['bToxidDontPassPostVarsToCms'] = $oConf->getShopConfVar('bToxidDontPassPostVarsToCms');
        $this->_aViewData['bToxidRedirect301ToStartpage'] = $oConf->getShopConfVar('bToxidRedirect301ToStartpage');
        $this->_aViewData['toxidCacheTtl'] = $oConf->getShopConfVar('toxidCacheTtl');
        $this->_aViewData['toxidError404Link'] = $oConf->getShopConfVar('toxidError404Link');
        $this->_aViewData['aToxidNotFoundUrl'] = $oConf->getShopConfVar('aToxidNotFoundUrl');
        $this->_aViewData['aToxidCurlUrlAdminParams'] = $oConf->getShopConfVar('aToxidCurlUrlAdminParams');
        $this->_aViewData['toxidAllowedCmsRequestParams'] = $oConf->getShopConfVar('toxidAllowedCmsRequestParams');
        $this->_aViewData['toxidDontVerifySSLCert'] = $oConf->getShopConfVar('toxidDontVerifySSLCert');

        return parent::render();
    }

    /**
     * Saves the settings
     */
    public function save(): void
    {
        $oConf = Registry::getConfig();
        $aParams = $oConf->getRequestParameter("editval");
        $sShopId = $oConf->getShopId();

        if (!is_array($aParams)) {
            return;
        }

        $oConf->saveShopConfVar('arr', 'aToxidCurlSource', $aParams['aToxidCurlSource'] ?? [], $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('arr', 'aToxidCurlSourceSsl', $aParams['aToxidCurlSourceSsl'] ?? [], $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('arr', 'aToxidSearchUrl', $aParams['aToxidSearchUrl'] ?? [], $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('arr', 'aToxidCurlUrlParams', $aParams['aToxidCurlUrlParams'] ?? [], $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('arr', 'aToxidCurlSeoSnippets', $aParams['aToxidCurlSeoSnippets'] ?? [], $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('str', 'toxidDontRewriteRelUrls', $aParams['toxidDontRewriteRelUrls'] ?? '', $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('str', 'toxidDontRewriteFileExtension', $aParams['toxidDontRewriteFileExtension'] ?? '', $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('str', 'toxidCacheTtl', $aParams['toxidCacheTtl'] ?? '', $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('str', 'toxidError404Link', $aParams['toxidError404Link'] ?? '', $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('arr', 'aToxidNotFoundUrl', $aParams['aToxidNotFoundUrl'] ?? [], $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('bl', 'toxidRewriteUrlEncoded', $aParams['toxidRewriteUrlEncoded'] ?? 0, $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('bl', 'toxidDontRewriteUrls', $aParams['toxidDontRewriteUrls'] ?? 0, $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('bl', 'bToxidDontPassPostVarsToCms', $aParams['bToxidDontPassPostVarsToCms'] ?? 0, $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('bl', 'bToxidRedirect301ToStartpage', $aParams['bToxidRedirect301ToStartpage'] ?? 0, $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('arr', 'aToxidCurlUrlAdminParams', $aParams['aToxidCurlUrlAdminParams'] ?? [], $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('str', 'toxidAllowedCmsRequestParams', $aParams['toxidAllowedCmsRequestParams'] ?? '', $sShopId, self::CONFIG_MODULE_NAME);
        $oConf->saveShopConfVar('bl', 'toxidDontVerifySSLCert', $aParams['toxidDontVerifySSLCert'] ?? 0, $sShopId, self::CONFIG_MODULE_NAME);
    }
}
