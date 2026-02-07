<?php

namespace Toxid\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use Toxid\Core\ToxidCurl;

/**
 * TOXID Frontend Controller - Displays CMS pages
 */
class ToxidController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current view template
     * @var string
     */
    protected $_sThisTemplate = 'page/toxid/toxid.tpl';

    /**
     * Template variable getter. Returns page title from CMS
     */
    public function getTitle()
    {
        return Registry::get(ToxidCurl::class)->_sPageTitle;
    }

    /**
     * Template variable getter. Returns meta description from CMS
     */
    public function getMetaDescription()
    {
        $sDescription = strip_tags(Registry::get(ToxidCurl::class)->_sPageDescription ?? '');

        if ($sDescription) {
            $this->_sMetaDescription = $sDescription;
        }

        return $this->_sMetaDescription;
    }

    /**
     * Template variable getter. Returns meta keywords from CMS
     */
    public function getMetaKeywords()
    {
        $sKeywords = strip_tags(Registry::get(ToxidCurl::class)->_sPageKeywords ?? '');

        if ($sKeywords) {
            $this->_sMetaKeywords = $sKeywords;
        }

        return $this->_sMetaKeywords;
    }

    /**
     * Render the CMS page
     */
    public function render()
    {
        return parent::render();
    }
}
