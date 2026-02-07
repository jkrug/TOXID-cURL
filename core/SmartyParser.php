<?php

namespace Toxid\Core;

use OxidEsales\Eshop\Core\Registry;

/**
 * TOXID Smarty Parser - Parses CMS content through Smarty
 */
class SmartyParser
{
    public function parse(string $content): string
    {
        if ($content === '') {
            return '';
        }

        return Registry::getUtilsView()->parseThroughSmarty($content, md5($content), null, true);
    }
}
