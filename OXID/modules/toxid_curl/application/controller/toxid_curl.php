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
