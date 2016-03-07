=== CC Child Pages ===

Plugin Name: CC Child Pages
Contributors: caterhamcomputing
Plugin URI: http://ccchildpages.ccplugins.co.uk/
Author URI: https://caterhamcomputing.net/
Donate Link: http://ccchildpages.ccplugins.co.uk/donate/
Requires at least: 4.0
Tested up to: 4.3.1
Stable tag: 1.30
Version: 1.30
Tags: child pages widget, child pages shortcode, child pages, child page, shortcode, widget, list, sub-pages, subpages, sub-page, subpage, sub page, responsive, child-page, child-pages, childpage, childpages, siblings, sibling pages

Adds a responsive shortcode to list child and sibling pages. Pre-styled or specify your own CSS class for custom styling. Includes child pages widget.

== Description ==

CC Child Pages is a simple plugin to show links to child pages via a shortcode.

Child Pages are displayed in responsive boxes, and include the page title, an excerpt and a "Read more..." link.

You can choose between 1, 2, 3 & 4 column layouts.

3 & 4 column layouts will resize to a 2 column layout on small devices to ensure that they remain readable.

= CC Child Pages editor button =

**CC Child Pages now adds a button to the WordPress text editor, allowing you to quickly insert the shortcode and select many common options**

= CC Child Pages widget =

CC Child Pages also includes a widget for displaying child pages within your sidebars.

The widget can be set to show the children of the current page or a specific page, or to show all pages.

Pages can be sorted by their menu order, title or ID. You can also select the depth of pages to be displayed.

You can now also tick the checkbox to show all pages, in which case the widget will behave much like the standard Pages widget but with additional options.

= Using the shortcode =

The simplest usage would be to use the shortcode with no parameters:

`[child_pages]`

This would show the Child Pages for the current page in 2 columns.

You can add the `cols` parameter to choose the number of columns:

`[child_pages cols="1"]`
`[child_pages cols="2"]`
`[child_pages cols="3"]`
`[child_pages cols="4"]`

... if `cols` is set to anything other than 1, 2, 3 or 4 the value will be ignored.

You can also show the child pages of a specific page by adding the `ID` of the page as follows:

`[child_pages id="42"]`

... or you can specify multiple IDs in a comma-separated list (does not work when using `list="true"`)

`[child_pages id="42,53,76"]`

To exclude pages, use the `exclude` parameter. This allows you to specify a comma separated list of Page IDs to be exclude from the output of the shortcode.

`[child_pages exclude="5,33,45"]`

If you want to prefer to use text other than the standard "Read more ..." to link to the pages, this can be specified with the `more` parameter:

`[child_pages more="More..."]`

You may also hide the "Read more ..." link altogether by setting the `hide_more` parameter to `"true"`:

`[child_pages hide_more="true"]`

Since there is no other way for the visitor to link to the child page, you can choose to make the page titles link to the child page by setting the `link_titles` parameter to `"true"`:

`[child_pages link_titles="true"]`

(This is mainly designed to be used with the `hide_more` parameter, but can be used independently if you want to have both the titles and "Read more ..." text link to the child page.)

When specifying `link_titles="true"`, you may wish to apply your own styling to the links. To do so, you can specify a style using the `title_link_class` parameter:

`[child_pages link_titles="true" title_link_class="my_title_link_class"]`

You can display a thumbnail of the featured image for each page (if set) by setting the `thumbs` parameter to `"true"`:

`[child_pages thumbs="true"]`

You can now also display thumbnails at different sizes to the default ('medium') size. Simply specify the thumbnail size in the `thumbs` parameter. You can even specify custom image sizes.

`[child_pages thumbs='large']`

You can make thumbnails link to the related child page by setting the `link_thumbs` to `"true"`:

`[child_pages thumbs='large' link_thumbs="true"]`

... note that specifying the `link_thumbs` parameter will have no effect unless the `thumbs` parameter is set to either `true` or a thumbnail size.

You can limit the length of the excerpt by specifying the `words` parameter:

`[child_pages words="10"]`

You can hide the excerpt altogether by setting the `hide_excerpt` parameter to `"true"`:

`[child_pages hide_excerpt="true"]`

You can stop Custom Excerpts from being truncated by seting the `truncate_excerpt` parameter to "false":

`[child_pages truncate_excerpt="false"]`

... this will display custom excerpts exactly as entered without being shortened. (Especially useful if using the Rich Text Excerpts plugin, in which case all styling will be preserved.)

When `truncate_excerpt` is set to `true`, excerpts will be truncated only if they exceed the specified word count (default 55). When custom excerpts are truncated, any HTML will be removed.

To change the order in which the child pages are listed, you can use the `orderby` and `order` parameters:

`[child_pages orderby="title" order="ASC"]`

The `orderby` parameter can have one of the following values:
`menu_order` (the default value) - shows the pages sorted by the order in which they appear within the WordPress admin

`id` sorts the pages according to the ID of the page
`title` sorts the pages alphabetically by the title
`slug` sorts the pages alphabetically according to the slug (page_name) of the page
`author` sorts the pages by author
`date` sorts the pages by the date they were created
`modified` sorts the pages by the date they were modified

The `order` parameter can be set to:

`ASC` shows the pages in ascending order, sorted by the value of `orderby`
`DESC` shows the pages in descending order, sorted by the value of `orderby`

You can now also use the `skin` parameter to choose a colour scheme for the Child Pages as follows:

`[child_pages skin="simple"]` (the default colour scheme)
`[child_pages skin="red"]`
`[child_pages skin="green"]`
`[child_pages skin="blue"]`

If you want to style the child page boxes yourself, you can also specify the `class` parameter. If used, this overrides the `span` parameter and adds the specified class name to the generated HTML:

`[child_pages class="myclass"]`

Finally, you can also display just an unordered list (`<ul>`) of child pages by adding the `list` parameter. In this case, all other parameters are ignored **except for `class`, `cols`, `exclude`, `orderby`, `order` and `id`**.

`[child_pages list="true"]`

When using the `list` parameter, you can also specify the `depth` parameter to specify how many levels in the hierarchy of pages are to be included in the list.

The `depth` parameter accepts the following values:

* 0 (default) Displays pages at any depth and arranges them hierarchically in nested lists 
* -1 Displays pages at any depth and arranges them in a single, flat list 
* 1 Displays top-level Pages only 
* 2, 3 … Displays Pages to the given depth

`[child_pages list="true" depth="4"]`

Specifying the `cols` parameter with `list="true"` will show the child pages as an unordered list ordered into a number of columns (I would recommend avoiding the use of the `depth` parameter when listing child pages within columns - the results are likely to be fairly unreadable!).

`[child_pages list="true" cols="3"]`

The columns are responsive, and should adjust according to the browser being re-sized or the size of the screen being used.

**N.B. Because the shortcode uses the WordPress `wp_list_pages` function to output the list, columns are acheived by applying CSS styling to the functions standard output. This CSS should work fine in modern browsers, but in older browsers (such as Internet Explorer 8) the list will not be split into columns**

= Sibling Pages =

The shortcode also allows you to display sibling pages (those at the same level as the current page within the hierarchy).

To do this, set the `siblings` parameter to `true`.

This will override the `id` parameter and will append the current page to the `exclude` parameter.

`[child_pages siblings="true"]`

This can also be used with the `list` parameter

`[child_pages siblings="true" list="true"]`

== Installation ==

1. Upload the plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. One column: `[child_pages cols="1"]`
2. Two columns (default): `[child_pages]` or `[child_pages cols="2"]`
3. Three columns: `[child_pages cols="3"]`
4. Four columns: `[child_pages cols="4"]`
5. Skin for Red colour schemes: `[child_pages skin="red"]`
6. Skin for Green colour schemes: `[child_pages skin="green"]`
7. Skin for Blue colour schemes: `[child_pages skin="blue"]`
8. Custom class defined for custom styling: `[child_pages class="myclass"]`
9. Show child pages as a list: `[child_pages list="true"]`
10. Using featured images as thumbnails: `[child_pages thumbs="true"]`
11. Limit word count of excerpt: `[child_pages words="10"]`
12. CC Child Pages widget options

== Changelog ==

= 1.30 =
* Bug fix - internationalization now works correctly (translations need work though - currently only French, which is known to be poor)
* Added more filters to widget, list and shortcode to allow extension of plugin

= 1.29 =
* Bug fix - widget will now show on all pages/posts if "All Pages" or a specific parent page is selected
* Bug fix - shortcode now closes query correctly (was causing issues with some themes)
* The shortcode will now work with custom post types
* You can now specify multiple parent page IDs (when using `list="true"`, only a single parent ID can be specified)

= 1.28 =
* Further improvements to integration when used with Video Thumbnails plugin

= 1.27 =
* Added the `siblings` parameter to show siblings of current page
* Improved integration when used with Video Thumbnails plugin
* Minor bug fixes for CC Child Pages Widget

= 1.26 =
* The CSS for displaying child pages has been re-written to allow for custom CSS to be more easily written - for example, specifying a border should no longer cause problems in the responsive layout. Fallbacks have been put in place for older versions of Internet Explorer.
* The handling of Custom CSS from the settings page has been improved.
* The loading of the plugin CSS has been returned to the default manner. While this means that CSS is loaded on pages where the shortcode is not used, it means that the CSS can be correctly minified by other plugins and ensures that valid HTML is generated.

= 1.25 =
* New option added to widget to show all top-level pages and their children. This can now be used as a complete replacement for the standard Pages widget
* New option added to the plugins settings page allowing custom CSS code to be specified from within the plugin. This feature has been requested several times. This functionality will be expanded on in the future.

= 1.24 =
* Further enhancements to CSS when using both the `list` and `cols` parameters

= 1.23 =
* Minor fix for CSS when using both the `list` and `cols` parameters

= 1.22 =
* Changes to how excerpts are generated from content when no custom excerpt is specified.
* Changed how CSS is queued - the CSS file will now only be included in the page if the shortcode is specified, helping to keep page sizes to a minimum.

= 1.21 =
* Change to allow `cols` parameter to be used when `list` parameter is set to `true`.
* Changed `.ccpages_excerpt` container to `<div>` (was `<p>`) to avoid potentially invalid HTML when HTML excerpts are used.

= 1.20 =
* Change to improve efficiency when the plugin attempts to force thumbnail creation via Video Thumbnails plugin
* Minor change to avoid output of empty links when applying links to thumbnails and no thumbnail is present
* Minor change to escaping special characters in `more` parameter

= 1.19 =
* Small change to how the plugin works with thumbnails. It will now use thumbnails generated by the Video Thumbnails plugin if it is installed.
* Added `link_thumbs` parameter. If set to `"true"`, thumbnails will link to the child page.
* CSS is no longer minified, in order to make it easier to view existing CSS when defining your own custom styles. The CSS can be minified by other plugins if required. 

= 1.18 =
* Added settings page to allow disabing of button in Visual Editor (TinyMCE)
* Added the `truncate_excerpt` parameter to the shortcode, defaults to `true` but setting to `false` stops custom excerpts from being shortened (where no custom excerpt exists, page content will still be truncated)

= 1.17 =
* Small change to how custom excerpts are handled for interoperability with Rich Text Excerpts plugin. 

= 1.16 =
* Added the `hide_excerpt` parameter

= 1.15 =
* Added `hide_more` parameter to hide "Read more ..." links.
* Added `link_titles` parameter to make titles link to pages.
* Added `title_link_class` parameter for styling links in page titles.

= 1.14 =
* Bug fix: Corrected missing `<ul>` tags in widget
* Minor CSS changes to improve compatibility with certain themes

= 1.13 =
* Bug fix: Corrected problem with titles including special characters
* Added orderby and order parameters to control the display order of child pages

= 1.12 =
* Bug fix: Corrected problem when automatic excerpt returns value including a shortcode

= 1.11 =
* Bug fix: Corrected small bug introduced in version 1.10 when using `list="true"`

= 1.10 =
* Added `exclude` parameter
* Added `depth` parameter (only used if `list` is set to `"true"`)

= 1.9 =
* Added editor button
* Added custom excerpt capability to pages by default
* Refined generation of page excerpt where no custom excerpt exists
* Enhanced functionality of the `thumbs` option - you can now set this to the desired thumbnail size e.g. `thumbs="large"`, `thumbs="full"`, `thumbs="my-custom-size"`, etc.

= 1.8 =
* CC Child Pages widget enhanced to allow display of children of current page or a specific page
* CC Child Pages widget enhanced to allow depth to be specified

= 1.7 =
* Changed plugin author to show business name (Caterham Computing)
* Added CC Child Pages widget
* Added various new classes to help with custom CSS styling

= 1.6 =
* Added the `words` parameter. When set to a value greater than 0, the number of words in the excerpt will be trimmed if greater than the specified value.

= 1.5 =
* Added the `thumbs` parameter. If set to `"true"`, the featured image (if set) of a page will be shown.

= 1.4 =
* Added `more` parameter to override standard "Read more ..." text
* Internationalisation ...

= 1.3 =
* Corrected small error when using `list` parameter

= 1.2 =
* Added the `list` parameter

= 1.1 =
* Added the `skin` parameter
* Added the `class` parameter

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 1.30 =
* Bug fix - internationalization now works correctly (translations need work though - currently only French, which is known to be poor)
* Added more filters to widget, list and shortcode to allow extension of plugin

= 1.29 =
* Bug fix - widget will now show on all pages/posts if "All Pages" or a specific parent page is selected
* Bug fix - shortcode now closes query correctly (was causing issues with some themes)
* The shortcode will now work with custom post types
* You can now specify multiple parent page IDs (when using `list="true"`, only a single parent ID can be specified)

= 1.28 =
* Further improvements to integration when used with Video Thumbnails plugin

= 1.27 =
* Added the `siblings` parameter to show siblings of current page
* Improved integration when used with Video Thumbnails plugin
* Minor bug fixes for CC Child Pages Widget

= 1.26 =
* The CSS for displaying child pages has been re-written to allow for custom CSS to be more easily written - for example, specifying a border should no longer cause problems in the responsive layout. Fallbacks have been put in place for older versions of Internet Explorer.
* The handling of Custom CSS from the settings page has been improved.
* The loading of the plugin CSS has been returned to the default manner. While this means that CSS is loaded on pages where the shortcode is not used, it means that the CSS can be correctly minified by other plugins and ensures that valid HTML is generated.

= 1.25 =
* New option added to widget to show all top-level pages and their children. This can now be used as a complete replacement for the standard Pages widget
* New option added to the plugins settings page allowing custom CSS code to be specified from within the plugin. This feature has been requested several times. This functionality will be expanded on in the future.

= 1.23 =
* Minor fix for CSS when using both the `list` and `cols` parameters

= 1.22 =
* Changes to how excerpts are generated from content when no custom excerpt is specified.
* Changed how CSS is queued - the CSS file will now only be included in the page if the shortcode is specified, helping to keep page sizes to a minimum.

= 1.21 =
* Change to allow `cols` parameter to be used when `list` parameter is set to `true`.
* Changed `.ccpages_excerpt` container to `<div>` (was `<p>`) to avoid potentially invalid HTML when HTML excerpts are used.

= 1.20 =
* Change to improve efficiency when the plugin attempts to force thumbnail creation via Video Thumbnails plugin
* Minor change to avoid output of empty links when applying links to thumbnails and no thumbnail is present
* Minor change to escaping special characters in `more` parameter

= 1.19 =
* Small change to how the plugin works with thumbnails. It will now use thumbnails generated by the Video Thumbnails plugin if it is installed.
* Added `link_thumbs` parameter. If set to `"true"`, thumbnails will link to the child page.
* CSS is no longer minified, in order to make it easier to view existing CSS when defining your own custom styles. The CSS can be minified by other plugins if required.

= 1.18 =
* Added settings page to allow disabing of button in Visual Editor (TinyMCE)
* Added the `truncate_excerpt` parameter to the shortcode, defaults to `true` but setting to `false` stops custom excerpts from being shortened (where no custom excerpt exists, page content will still be truncated)

= 1.17 =
* Small change to how custom excerpts are handled for interoperability with Rich Text Excerpts plugin. 

= 1.16 =
* Added the `hide_excerpt` parameter

= 1.15 =
* Added `hide_more` parameter to hide "Read more ..." links.
* Added `link_titles` parameter to make titles link to pages.
* Added `title_link_class` parameter for styling links in page titles.

= 1.14 =
* Bug fix: Corrected missing `<ul>` tags in widget
* Minor CSS changes to improve compatibility with certain themes

= 1.13 =
Bug fix: Corrected problem with titles including special characters
Added orderby and order parameters to control the display order of child pages

= 1.12 =
Bug fix: Corrected problem when automatic excerpt returns value including a shortcode

= 1.11 =
Bug fix: Corrected small bug introduced in version 1.10 when using `list="true"`

= 1.10 =
Added exclude parameter for shortcode, and depth parameter for shortcode when list output is selected.

= 1.9 =
Added editor button, added custom excerpt capability to pages, enhanced thumbnail options and refined excerpt generation from page content

= 1.8 =
CC Child Pages widget enhanced to allow display of children of current page or a specific page. Depth can also be specified.

= 1.7 =
Added new CC Child Pages Widget. Added lots of new classes to help with custom CSS styling.

