<?php

class Toxid_Curl_Smarty_Parser
{
    public function parse($content)
    {
        return oxUtilsView::getInstance()->parseThroughSmarty($content);
    }
}


