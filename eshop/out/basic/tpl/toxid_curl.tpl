[{ include file="_header.tpl" title=$template_title location=$template_title }]

    <div id="toxid_curl_main" style="margin:0 5px;">
		<h1>Hey! Welcome to TOXID!</h1>
		[{ $toxid_curl_cmp->getCmsSnippet(content) }]
		[{ $toxid_curl_cmp->getCmsSnippet() }]
	</div>

[{ include file="_footer.tpl" }]