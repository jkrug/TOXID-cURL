[{if $cl == "shop_list" && $oViewConf->getActiveClassName() == "toxid_setup_list"}]
    [{assign var="cl" value=$oViewConf->getActiveClassName()}]
[{/if}]

[{$smarty.block.parent}]
