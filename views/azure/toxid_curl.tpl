[{assign var="tpl" value=$oViewConf->getActTplName()}]
[{assign var="toxid" value=$oViewConf->getToxid()}]

[{*assign var="_sMetaTitle" value=$toxid->getCmsMetadata('title')*}]
[{*assign var="description" value=$toxid->getCmsMetadata('description')*}]
[{*assign var="keywords" value=$toxid->getCmsMetadata('keywords')*}]

[{assign var="border" value=$toxid->getCmsSnippet('sections/border')}]

[{if $border}]
    <div class="header">
        [{$border}]
    </div>
[{/if}]

[{capture append="oxidBlock_content"}]
    <div id="toxid_curl_main">
        [{$toxid->getCmsSnippet('content')}]
        [{* $toxid->getCmsSnippet('sections/left') *}]
    </div>
[{/capture}]

[{capture append="oxidBlock_sidebar"}]
    <div id="toxid_curl_sub">
        [{* $toxid->getCmsSnippet('sidebar') *}]
        [{$toxid->getCmsSnippet('navigation')}]
	[{* Cache navigation snippet for 1800 seconds *}]
	[{* $toxid->getCmsSnippet('navigation', false, null, 1800) *}]
    </div>
[{/capture}]

[{include file="layout/page.tpl" sidebar="Left"}]
