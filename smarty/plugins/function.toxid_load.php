<?php

/**
 * Smarty plugin: toxid_load
 *
 * Loads OXID objects (articles, categories, manufacturers) from CMS content.
 *
 * Usage:
 *   [{toxid_load type="oxarticle" oxid="943ed656e21971fb2f1827facbba9bec" assign="oProduct"}]
 *   [{toxid_load type="oxarticle" ident="0802-85-823" assign="oProduct"}]
 */
function smarty_function_toxid_load($params, &$smarty)
{
    if (!isset($params['assign'])) {
        return;
    }

    $aSupportedTypes = ['oxarticle', 'oxcategory', 'oxmanufacturer'];

    $sType = isset($params['type']) ? strtolower($params['type']) : '';
    if (!in_array($sType, $aSupportedTypes)) {
        return;
    }

    $sOxid = $params['oxid'] ?? null;
    $sIdent = $params['ident'] ?? null;

    if (!$sOxid && !$sIdent) {
        return;
    }

    $oObject = oxNew($sType);

    if ($sIdent) {
        switch ($sType) {
            case 'oxarticle':
                $sOxid = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne(
                    'SELECT OXID FROM oxarticles WHERE OXARTNUM = ?',
                    [$sIdent]
                );
                break;
        }
    }

    if ($sOxid) {
        $oObject->load($sOxid);
    } else {
        return;
    }

    $smarty->assign($params['assign'], $oObject);
}
