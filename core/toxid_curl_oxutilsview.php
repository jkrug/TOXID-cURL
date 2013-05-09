<?php
/**
 * vt Smarty Extension Demo
 * Copyright (C) 2013  Marat Bedoev
 *
 *    This file is part of TOXID Module for OXID eShop CE/PE/EE.
 *
 *    TOXID is free software: you can redistribute it and/or modify
 *    it under the terms of the MIT License.
 *
 *
 * @link      https://github.com/vanilla-thunder/vt-smartyext
 * @link      http://toxid.org
 * @package   core
 */

class toxid_curl_oxutilsview extends toxid_curl_oxutilsview_parent
{
    public function _fillCommonSmartyProperties($oSmarty)
    {
        parent::_fillCommonSmartyProperties($oSmarty);

        $cfg = oxRegistry::getConfig();

        $aPluginsDir = $oSmarty->plugins_dir;
        $aPluginsDir[] = $cfg->getModulesDir()."/toxid_curl/smarty/plugins/";

        $oSmarty->plugins_dir = $aPluginsDir;
    }
}