[{capture append="oxidBlock_content"}]
    [{assign var="oContent" value=$oView->getContent()}]
    [{assign var="tpl" value=$oViewConf->getActTplName()}]

    <div id="toxid_curl_main" style="margin:0 5px;width:580px;">
		[{ $toxid_curl_cmp->getCmsSnippet(content) }]
	</div>
[{/capture}]
[{include file="layout/page.tpl" sidebar="Left"}]