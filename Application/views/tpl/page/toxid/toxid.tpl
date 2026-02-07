[{assign var='toxid' value=$oViewConf->getToxid()}]

[{capture append="oxidBlock_content"}]
    <div class="row">
        [{* CMS navigation (e.g. WordPress categories) as sidebar *}]
        [{assign var="toxidNav" value=$toxid->getCmsSnippet('navigation', false, null, 1800)}]
        [{if $toxidNav}]
        <div class="col-12 col-md-3">
            <div class="toxid-navigation mb-3">
                [{$toxidNav}]
            </div>
        </div>
        <div class="col-12 col-md-9">
        [{else}]
        <div class="col-12">
        [{/if}]
            [{* CMS content (article list or single article) *}]
            <div class="toxid-content">
                [{$toxid->getCmsSnippet('content')}]
            </div>
        </div>
    </div>
[{/capture}]

[{include file="layout/page.tpl" blContentPage=true}]
