WPUTheme
=================

WPUTheme is a responsive FrameWork theme for WordPress.
You should use it with its plugins & mu-plugins, for an optimal experience !

## How to use

* Install this theme as a parent theme.
* Create a child theme. You can use the folder examplechildtheme/ to get started.
* Add/Remove/Edit/Hide blocks using hooks.

## Examples

### Example : How to add a block on all pages, over content

```php
add_action('wputheme_main_overcontent','mychildtheme_home_block');
function mychildtheme_home_block(){
    echo '<div>my block</div>';
}
```

### Example : How to hide the search form in the banner

```php
add_filter('wputheme_display_searchform','__return_false');
```

## Theme elements

### Page templates

* **Big Pictures** : A gallery of big views of attached pictures.
* **Contact** : A contact form sending an email.
* **Download** : A list of attached files.
* **FAQ** : A list of all the pages.
* **Gallery** : A gallery of thumbnails of attached pictures, with a link to the full picture.
* **Sitemap** : A list of all the pages.
* **Webservice** : Used for some apps or AJAX requests in front.

## Theme Options

### Conditional hooks

* **wputheme_display_header** : (bool:true) Display or hide header ( header.php )
* **wputheme_display_footer** : (bool:true) Display or hide footer ( footer.php )
* **wputheme_display_mainwrapper** : (bool:true) Display or hide main wrapper ( header.php & footer.php )

#### Header

* **wputheme_display_title** : (bool:true) Display or hide search form ( header.php )
* **wputheme_display_searchform** : (bool:true) Display or hide search form ( header.php )
* **wputheme_display_social** : (bool:true) Display or hide social links ( header.php )
* **wputheme_social_links** : (array) Add, edit or delete social links ( header.php )
* **wputheme_display_mainmenu** : (bool:true) Display or hide main menu ( header.php )
* **wputheme_mainmenu_settings** : (array) Add options for main menu ( header.php )

#### Content

* **wputheme_display_languages** : (bool:true) Display or hide languages ( header.php )
* **wputheme_display_breadcrumbs** : (bool:true) Display or hide breadcrumbs ( header.php )
* **wputheme_display_jsvalues** : (bool:true) Display or hide JS Values ( header.php )

#### Single

* **wputheme_display_single_share** : (bool:true) Display or hide share links ( single.php )
* **wputheme_share_methods** : (array) Add, edit or delete share methods ( single.php )
* **wputheme_display_single_prevnext** : (bool:true) Display or hide prev/next post ( single.php )

### Action hooks

#### Functions

* **wputh_functionsphp_start** : Executed at the start of functions.php
* **wputh_functionsphp_end** : Executed at the end of functions.php

#### Content

* **wputheme_header_items** : Load blocks over default header.
* **wputheme_header_banner** : Load blocks into default header.
* **wputheme_header_elements** : Load blocks under default header.
* **wputheme_main_overcontent** : Load blocks into main wrapper, over content.
* **wputheme_main_overcontent_inajax** : Load blocks into main wrapper, over content, even in AJAX Requests.
* **wputheme_main_undercontent_inajax** : Load blocks into main wrapper, under content, even in AJAX Requests.
* **wputheme_main_undercontent** : Load blocks into main wrapper, under content.
* **wputheme_footer_elements** : Load blocks under main wrapper.
* **wputheme_home_content** : Blocks on home page

