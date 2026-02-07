<?php

namespace Toxid\Core;

use OxidEsales\Eshop\Core\Registry;

/**
 * TOXID UtilsView - Registers TOXID Smarty plugin directory
 */
class UtilsView extends UtilsView_parent
{
    protected function _fillCommonSmartyProperties($oSmarty)
    {
        parent::_fillCommonSmartyProperties($oSmarty);

        $aPluginsDir = $oSmarty->plugins_dir;
        $aPluginsDir[] = Registry::getConfig()->getModulesDir() . "toxid_curl/smarty/plugins/";

        $oSmarty->plugins_dir = $aPluginsDir;
    }
}
