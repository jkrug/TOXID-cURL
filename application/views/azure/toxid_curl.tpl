[{capture append="oxidBlock_content"}]
    [{assign var="tpl" value=$oViewConf->getActTplName()}]
    <div id="toxid_curl_main">
		[{assign var='toxid' value=$oViewConf->getToxid()}] 
		[{ $toxid->getCmsSnippet(content) }]
	</div>
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]