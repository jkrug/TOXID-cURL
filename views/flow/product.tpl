[{toxid_load type="oxarticle" ident=$ident oxid=$oxid assign="_product"}]
[{if $_product}]
    [{assign var="testid" value=$_product->getId()}]
    <div class="row lineView clear">
        <span itemscope itemtype="http://schema.org/Product">[{oxid_include_widget cl="oxwArticleBox" _parent=$oView->getClassName() nocookie=1 _navurlparams=$oViewConf->getNavUrlParams() iLinkType=$_product->getLinkType() _object=$_product anid=$_product->getId() isVatIncluded=$oView->isVatIncluded() rsslinks=$rsslinks sWidgetType="product" sListType="listitem_line" inlist=$_product->isInList() skipESIforUser=1}]</span>
    </div>
[{/if}]
