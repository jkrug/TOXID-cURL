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

class toxid_curl_cms_content extends oxWidget
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'toxid_cms_content.tpl';

    /**
     * Executes parent::render().
     * Returns name of template file to render.
     *
     * @return string $this->_sThisTemplate current template file name
     */
    public function render()
    {
        /** @var toxidCurl $toxid */
        $toxid = oxRegistry::get('toxidCurl');

        if (!$toxid->getInitialized()) {
            $toxid->init(oxNew('Toxid_Curl_Smarty_Parser'));
        }

        $cmsSnippet = $toxid->getCmsSnippet('content', true, 'toxid-teaser');

        parent::render();

        $this->_aViewData['cmsContent'] = $cmsSnippet;

        return $this->_sThisTemplate;

    }

}
