TOXID cURL
==========
TOXID cURL enables you to load dynamic CMS content into your OXID eShop.
It keeps your userfriendly URLs from the CMS, you could add products as content-type
and they will be loaded also dynamicly from your shop.

Magic!

So let's start!


Please notice: We are developing on GitHub.
To be sure to get the latest version, look there
    https://github.com/jkrug/TOXID-cURL


What it is
----------
*    Renders CMS-pages and Navigation in OXID from any XML-Source

*    adjusts link-urls from source


What it is NOT
--------------
*    NO Single-Sign-On (so no restriced pages are possible)
     See TOXID with OxAPI

*    NO out-of-the-box solution


System requirements
-------------------
From version 1.5 on, the minimum OXID version should be 4.6
Thats because we could drop the autoloader and keep nearly all stuff in the module directory.
Also the basic theme is not longer supportet.

If you want to use it for any older version of OXID eShop, checkout the previous version.

Installation & configuration
----------------------------
**1 copy files from the module to /modules/toxid_curl/**

**2 Activate module in admin**

**3 Set up your CMS to deliver the pages in UTF-8 XML-format**

    <?xml version="1.0"?>
    <toxid>
        <part1></part1>
        <part2></part2>
    </toxid>

It is STRONGLY recommended to wrap your snippets/parts in CDATA to prevent XML-mistaktes

For Wordpress, change the "Permalinks" settings to use SEO URLs, e.g. by entry name (http://blog.mysite.com/content1) and make sure to adjust the Wordpress .htaccess to allow URL Rewriting.

**4 Configure your System in Extensions > TOXID Configuration**

Specifically, add the URLs to your CMS and enter a URL in "URL Identifier / SEO-Snippet:", e.g. "Blog".
Even if you only use one language, enter a value for the other language, too! Otherwise, the internet might explode, 
or at least you will get the blog contents for every page in your shop. :)

Also, make sure you add a "/" at the end of your CMS URL, e.g. "http://blog.mysite.com/", otherwise you will get double-slashes
in your blog URLs ("../Blog//foo-bar").

**5 now you can call your snippets via the component like this**

    [{assign var='toxid' value=$oViewConf->getToxid()}]
    [{ $toxid->getCmsSnippet(part1) }]

keep in mind, that search keywords will be attached in the end

and in tpl/search.tpl (if you use basic theme), or tpl/page/search/search.tpl add following code:

    [{assign var='toxid' value=$oViewConf->getToxid()}]
    [{assign var='typo3result' value=$toxid->getSearchResult($oView->getSearchParamForHtml())}]
    [{if $typo3result}]
        <div>
            [{$typo3result}]
        </div>
    [{/if}]

**6 Use OXIDs file cache for integrating snippets into navigation**

When using snippets in omnipresent parts of the OXID eShop you probably don't want TOXID to request your CMS on every shop request. In this case you can use the TTL parameter to cache the parsed snippet markup for a given amount of time:

    [{assign var='toxid' value=$oViewConf->getToxid()}]
    [{ $toxid->getCmsSnippet('cms_navigation', false, null, 1800) }]

This stores the requested snippet in OXIDs file cache for 30 minutes (1800 seconds). Further calls with the same parameter signature will be served from the file cache until one of the following conditions is met:

*    Given TTL has expired
*    The CMS has already been queried in previous snippet calls without TTL parameter. So when requesting a content page via the `toxid_curl` controller the snippet calls with TTL will be served from the fresh loaded CMS response instead of the cache content
*    When the request header `Cache-Control: no-cache` is set. This way you can refresh the cached snippets bei hitting `ctrl + shift + R` in your browser
*    When OXIDs tmp/compile directory is cleared

For further information about TTL handling see `oxUtils::toFileCache()`

**7 Set up preview functionality for Wordpress!**

Install the toxid-preview Wordpress-Plugin from this repository https://github.com/jkrug/wordpress-toxid
After setting up yor preview-password in Wordpress-Dashboard->Settings->General "password for previews" , go to Toxid-Settings in Oxid Admin.

In the input-field "Admin URL-Parameter" paste "preview=true&toxid-preview=" while "toxid-preview" gets the value of the password you set up in Wordpress before.

Add the cms-specific url-params to the input field "Allowed cms specific parameters", i.e "p" for wordpress.

Finally, log in in the shop frontend with an admin user and in your blog you will also see posts that are drafts currently.

**8 Adjust your templates!**




What we already implemented
----------------------------
*    done: Multi-Language-Support

*    done: Caching

*    done: Add products as CMS-Content, rendered by OXID

*    demo templates for CMS

*    done: Configuration via admin-area
