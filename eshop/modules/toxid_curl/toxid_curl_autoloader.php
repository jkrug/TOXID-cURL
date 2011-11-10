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

	function toxidAutoloader($sClass) {
		$sClass = strtolower(basename($sClass));
		$sBasePath = getShopBasePath().'modules/toxid_curl/';
		
		$aClassDirs = array(
			$sBasePath,
			$sBasePath . 'core/',
			$sBasePath . 'controller/',
			$sBasePath . 'admin/'
		);
		foreach ($aClassDirs as $sDir) {
			$sFilename = $sDir . $sClass . '.php';
			if (file_exists($sFilename)) {
				include $sFilename;
				return;
			}
		}
	}
	spl_autoload_register("toxidAutoloader");
	