<?php

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: function.toxid_load.php
 * Type: string, html
 * Name: toxid_load
 * Purpose: Collect needed javascript includes and calls, but execute them at the bottom of the page.
 *
 * Add [{ toxid_load type="oxarticle" oxid="943ed656e21971fb2f1827facbba9bec" assign="oProduct"}]
 * to load oxarticle object to oProduct by its oxid
 * Add [{toxid_load type="oxarticle" ident="0802-85-823" assign="oProduct"}]
 * to load oxarticle object to oProduct by its artnum
 *
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_toxid_load( $params, &$smarty )
{
    if (!isset( $params['assign'] )) {
        return;
    }
    $aSupportedTypes = array(
        'oxarticle', 'oxcategory', 'oxmanufacturer'
    );

    $sType  = isset( $params['type'] )  ? $params['type']  : '';
    if (!in_array(strtolower($sType), $aSupportedTypes)) {
        return;
    }

    $sOxid  = isset( $params['oxid'] )  ? $params['oxid']  : null;
    $sIdent = isset( $params['ident'] ) ? $params['ident'] : null;

    if (!$sOxid && !$sIdent) {
        return;
    }

    $oObject = oxNew($sType);
    if ($sIdent) {
        switch ($sType) {
            case 'oxarticle':
                $sOxid = oxDb::getDb()->getOne(
                    'SELECT OXID FROM oxarticles WHERE OXARTNUM = ?',
                    array($sIdent)
                );
                break;
        }
    }

    if ($sOxid) {
        $oObject->load($sOxid);
    }
    else {
        return;
    }

    $smarty->assign($params['assign'], $oObject);


}
