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
 * @version   toxid_curl_cmp.php 2011-04-28 00:06 jkrug $
 */

class toxid_curl_cmp extends oxView
{
	
    /**
     * Executes parent::render() and returns Toxid Object.
     *
     * @return object $oToxid
     */
    public function render()
    {
        parent::render();
		$oToxid = toxidCurl::getInstance();
		$oToxid->loadCmsPage();
		return $oToxid;
    }

}