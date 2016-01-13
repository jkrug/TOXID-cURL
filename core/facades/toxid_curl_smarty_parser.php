<?php

class Toxid_Curl_Smarty_Parser
{
    public function parse($content)
    {
        return oxRegistry::get('oxUtilsView')->parseThroughSmarty($content, md5($content));
    }
}


