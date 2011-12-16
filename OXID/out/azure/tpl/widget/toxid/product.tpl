[{ toxid_load type="oxarticle" ident=$ident oxid=$oxid assign="oProduct"}]
[{if $oProduct}]
    <ul class="lineView clear">
        <li class="productData" itemscope itemtype="http://schema.org/Product">[{include file="widget/product/listitem_line.tpl" product=$oProduct blDisableToCart=$blDisableToCart}]</li>
    </ul>
[{/if}]
