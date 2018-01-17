[{assign var="tpl" value=$oViewConf->getActTplName()}]
[{assign var='toxid' value=$oViewConf->getToxid()}]

[{assign var="toxidBlockContent" value=$toxid->getCmsSnippet('content')}]
[{capture append="oxidBlock_content"}]
    <div id="toxid_curl_main">
        [{$toxidBlockContent}]
    </div>
[{/capture}]

[{assign var="toxidBlockSidebar" value=$toxid->getCmsSnippet('sidebar')}]
[{capture append="oxidBlock_sidebar"}]
    <div id="toxid_curl_sub">
        [{$toxidBlockSidebar}]
    </div>
[{/capture}]

[{include file="layout/page.tpl" sidebar="Left"}]

