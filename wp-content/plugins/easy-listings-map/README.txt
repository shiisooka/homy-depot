=== Easy Listings Map ===
Contributors: c0dezer0
Donate link: http://www.asanaplugins.com/
Tags: easy property listings, epl, easy property listings extension, easy property listings extensions, easy property listings map, easy property listings google maps, epl extension, epl extensions, epl map, epl google maps, property listings, property management, property listings map, property management map, real estate, real estate connected, real estate map, easy listings map, easy google map
Requires at least: 3.3
Tested up to: 4.7.1
Stable tag: 1.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy to use and advanced map extension for Easy Property Listings Wordpress plugin.

== Description ==

Easy Listings Map is an easy to use, advanced and free map extension for Easy Property Listings which allows site owners to add Google Maps to their site that shows listings in the map.

This extension also allows ability to show Google Maps for listing in single listing page.

Requires [Easy Property Listings](https://wordpress.org/plugins/easy-property-listings/)

**[Upgrade to Pro version to getting more useful features and supporting us](http://www.asanaplugins.com/products/easy-listings-map-pro/?utm_source=wordpress_org&utm_campaign=easy_listings_map&utm_medium=link)**

[Premium Support](https://asanaplugins.freshdesk.com/support/tickets/new)

Features of the plugin include:

* Google maps shortcode for showing listings in the map.
* Google maps in the single listing page.
* Clustering listings in the map.
* Showing custom markers in the map that user uploaded.
* Ability to show specific listings in the map that site owner choosed.
* Ability to customize map size.
* Ability to show details of listing in the map ( image of the listing, ... ).
* Ability to show details of the listings in the map that are in same position( tabbed Google Map info window ).
* Ability to auto zoom in the map for showing more listings in the map.
* Ability to show specific location listings.
* Ability to choose Google Maps view types like Roadmap, Sattelite, Hybrid, Terrain.
* Ability to choose a Google Maps view type as a default view type.
* Supports Map Styles of [snazzymaps](https://snazzymaps.com/).
* Supports Google Maps API key.
* Supports template overriding feature so developers can customize it.
* Compatible with all of Easy Property Listings versions.
* A fast and efficient plugin written using WordPress standards.

**Pro Version Features**

* A custom address meta-box in listings admin page that is easy to use for setting listing address by Google Maps auto complete address or moving marker on the map.
* An address bar on the map to finding address.
* Supports Google Maps auto complete address.
* Ability to show [multiple maps](http://www.demos.asanaplugins.com/multiple-maps/?utm_source=wordpress_org&utm_campaign=easy_listings_map&utm_medium=link) in same page.
* Supports Google Maps Traffic, Transit and Bicycling layers.
* Ability to show map in full screen size.
* Finding user location in the map over HTTPS.
* Supports Google Maps direction service.
* Finding direction from specific location to specific property location.
* Animating markers on loading markers.

**[Try Pro version demo](http://www.demos.asanaplugins.com/?utm_source=wordpress_org&utm_campaign=easy_listings_map&utm_medium=link)**

More features will come to pro version of the plugin if you support us by **[purchasing](http://www.asanaplugins.com/products/easy-listings-map-pro/?utm_source=wordpress_org&utm_campaign=easy_listings_map&utm_medium=link)** it.

Refer to [documentation](https://asanaplugins.freshdesk.com/support/solutions/16000037719) of the plugin for more information.

= Tags =

easy property listings, epl, easy property listings extension, easy property listings extensions, easy property listings map,
easy property listings google maps, epl extension, epl extensions, epl map, epl google maps, property listings, property management,
property listings map, property management map, real estate, real estate connected, real estate map, easy listings map, easy google map

== Installation ==

1. Upload `easy-listings-map` to the `/wp-content/plugins/` directory.
2. Activate `easy-property-listings` in your wordpress site if it is not activated already.
3. Activate `easy-listings-map` through the 'Plugins' menu in WordPress.
4. For detailed setup instructions, vist the official [Documentation](https://asanaplugins.freshdesk.com/support/solutions/16000037719) page.

== Frequently Asked Questions ==

= How Easy Listings Map works? =

**Easy Listings Map** works by means of coordinates of **listings**, so if a **listing** has not filled coordinates field it will not shown in the map of **Easy Listings Map** in other words if you want to **listings** shown in the map you should add coordinates of them exactly.

= Why Easy Listings Map works by coordinates of listings? =

Because it is a best way to showing **listings** in the map from speed view. If it uses address of **listings** to showing them in the map it will reduce speed because address needs to geocoded and geocoding listings address will reduce speed of the site and map loading.

= How can I show listings in the Google Maps? =

**Easy Listings Map** has a shortcode for Google maps for showing listings in the map, so you can use this shortcode for showing listings in the map, also this shortcode has a user interface for adding it pages or posts. For detailed information refer to [Adding Listings Google Maps](https://asanaplugins.freshdesk.com/support/solutions/articles/16000012539-how-to-create-a-map-for-showing-listings).

= How can I customize dimension of Google Maps? =

* For customizing dimension of single listing page map refer to [single listing page map dimension](https://asanaplugins.freshdesk.com/support/solutions/articles/16000012547-general-tab-of-settings-menu).
* For customizing dimension of Google Maps listings shortcode refer to [listings map dimension](https://asanaplugins.freshdesk.com/support/solutions/articles/16000012542-shortcode-form-items-description).

= Is it possible to customize markers in the Google Maps shortcode? =

Yes it is possible, please refer to [customizing markers](https://asanaplugins.freshdesk.com/support/solutions/articles/16000012549-markers-tab-of-settings-menu).

== Changelog ==

= 1.2.4 =

* New : Adding plugin documentation links to the plugin.

= 1.2.3 =

* New : Adding Google Maps API key support to the plugin.
* Fix : Issue of not loading map because of Google Maps API key support.

= 1.2.2 =

* Fix : Set loading map message position in center of the map.

= 1.2.1 =

* New : Adding getting started page to plugin.
* Fix : Changing name of location walker class.

= 1.2.0 =

* New : Making plugin template files overridable so developers can change functionality of map by overriding template files.
* New : Adding default map display type to plugin settings.
* New : Adding zoom_events to Google Maps shortcode of the plugin for loading listings when map zoom changes.
* New : Changing Google Maps infowindow listings default image.
* New : Adding map styles setting to changing map style based on entered style.
* Fix : Close button positioning issue.
* Fix : An issue in saving setting tab fields.
* Fix : An issue in loading bound markers.

= 1.1.3 =

* New : Showing specific location listings in the map by choosing desired location from Google Maps shortcode.
* New : Adding functionality to showing Google maps inside Bootstrap, jQuery and ZozoUi tabs.

= 1.1.2 =

* Fix : An issue that caused google map shortcode shown on top of page content.

= 1.1.1 =

* Fix : An issue that cause to not loading some of listings that are in bound of the map.
* Fix : An issue that cause to not showing listings on the map when auto zoom feature enabled.
* New : A feature added to not loading markers when all of them loaded to the map already.

= 1.1.0 =

* New: Making maps responsive.
* Fix: An issue in the Google Maps shortcode when setting it's title that cause map doesn't shown.

= 1.0.1 =

* Fix: issues of the plugin in wordpress 3.3

= 1.0.0 =

* First official release!
