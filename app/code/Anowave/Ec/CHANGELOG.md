# Changelog

All notable changes to this project will be documented in this file.

## [103.5.2.17085] - 12/06/2023

### Added

- Added missing addToCompare event tracking in categories

## [103.5.1] - 06/06/2023

### Fixed

- Fixed mixed namespaces in \vendor\FacebookAds

## [103.5.0] - 02/06/2023

### Added

- Added ability to add radius to GDRP dialog for better visual

### Fixed

- Fixed an issue related background color of GDRP not changing

## [103.4.9] - 01/06/2023

### Fixed

- Fixed an issue with unassigned categories (detached from any root)

## [103.4.8] - 01/06/2023

### Fixed

- Added PHP 8.2 support

## [103.4.7] - 26/05/2023

### Fixed

- Fixed a rare JS error related to recent update

### Removed

- Removed some unused code

## [103.4.6] - 26/05/2023

### Fixed

- Fixed small JS error related to gtag() consent decline

## [103.4.5] - 24/05/2023

### Fixed

- Fixed 'purchase' event not being GDRP compliant

## [103.4.4] - 23/05/2023

### Added

- Added gtag('consent') support based on our GDRP solution. It is now possible to update gtag() consent using the GDRP feature in segmented mode.

## [103.4.3] - 23/05/2023

### Fixed

- Fixed unescaped quotes in data-swatch attributes (rare case when attribute names contain single/doble quotes)

## [103.4.2] - 18/05/2023

### Removed

- Removed checkout option tracking triggering on cart page (due to shipping estimation). checkoutOption/add_shipping_info now triggers on CHECKOUT ONLY.

## [103.4.1] - 15/05/2023

### Fixed

- Fixed Warning: Undefined variable $category_name in app/code/Anowave/Ec/Block/Plugin.php on line 714

## [103.4.0] - 14/05/2023

### Fixed

- Fixed some events not being GDRP compliant (view_cart)

### Added

- Added custom dispatched event 'ec.consent' to allow third parties to modify consent returned from AEC.CookieConsent.getConsent() method. 

Developer ref: (how to use this event). 

AEC.EventDispatcher.on('ec.consent', consent => 
{
	constent = '<your own value here>';
});

## [103.3.9] - 11/05/2023

### Added

- Minor performance updates

## [103.3.8] - 09/05/2023

### Added

- Added extended support for some checkout modifications

## [103.3.7] - 08/05/2023

### Fixed

- Specific (view_item, view_item_list) events made GDRP compliant

## [103.3.6] - 12/04/2023

### Fixed

- Small code cleanup

## [103.3.5] - 23/03/2023

### Fixed

- Fixed add_to_wishlist/add_to_compare events not being GDRP compliant

## [103.3.4] - 21/03/2023

### Fixed

- Fixed an issue related to custom checkbox options  ('customize') event

## [103.3.3] - 02/02/2023

### Fixed

- Fixed multi-store category issue

## [103.3.2] - 02/02/2023

### Fixed

- Restored add to cart validation for setups supporting jQuery()

## [103.3.1] - 02/02/2023

### Fixed

- Fixed compilation issue related to GDRP consent log

## [103.3.0] - 23/01/2023

### Added

- Minor code updates
- Changed custom cache tags to 42bd

## [103.2.9] - 23/01/2023

### Added

- Added event_source_url parameter for FCAPI

## [103.2.8] - 20/01/2023

### Added

- Added dataLayer[] reset feature e.g. dataLayer.push({ ecommerce: null }); prior to any new push. Option can be enabled in Stores -> Configuraton -> Anowave Extensions -> Google Tag Manager -> Enhanced Ecommerce Tracking Preferences -> Reset dataLayer[] object

## [103.2.7] - 20/01/2023

### Added

- Minor code cleanup and compatibility updates with Anowave_Ec4 related to promotions

## [103.2.6] - 20/01/2023

### Added

- Added 'ec.cookie.promotion.entity' dispatched event to allow for promotion modification by third party scripts

## [103.2.5] - 20/01/2023

### Added

- Added promotion tracking compatibility with Anowave_Ec4

## [103.2.4] - 12/01/2023

### Fixed

- Fixed an issue related to wrong category in a multi-store environment

## [103.2.3] - 11/01/2023

### Fixed

- Minor code cleanup

## [103.2.2] - 11/01/2023

### Fixed

- Fixed Error: Call to a member function getRegionCode() on string in vendor/anowave/ec/Helper/Data.php

## [103.2.1] - 09/01/2023

### Fixed

- Fixed error: Exception #0 (Exception): Deprecated Functionality: preg_replace(): Passing null to parameter #3 ($subject) of type array|string is deprecated in /home/kuiperspro/domains/kuipersnautic.nl/public_html/app/code/Anowave/Ec/Helper/Data.php on line 5559

## [103.2.0] - 05/01/2023

### Fixed

- Fixed an issue with duplicate data caused by private fallback feature enabled
- Added maximum interval time for FB pixel load listener

## [103.1.9] - 05/01/2023

### Changed

- Added new AEC.CookieConsent.getConsent() method to overcome a few issues related to Varnish

## [103.1.8] - 05/01/2023

### Changed

- Optimized JS code in app/code/Anowave/Ec/view/frontend/web/js/ec.js

## [103.1.7] - 19/12/2022

### Added

- Added missing FCAPI UserData(['state'])

## [103.1.6] - 14/12/2022

### Fixed

- Fixed CSRF issue with datalayer controller.

## [103.1.5] - 09/12/2022

### Changed

- Set 'region' from getRegionId() to getRegionCode() in enhanced_conversion_data variable

## [103.1.4] - 28/11/2022

### Fixed

- Fixed compatibility issue with Anowave_Ec4

## [103.1.3] - 21/11/2022

### Added

- Added ability to track transactions via server-side implementation ONLY (Using Google Analytics Measurement Protocol)

## [103.1.2] - 17/11/2022

### Fixed

- Fixed an issue with sidebar remove event missing 'list' parameter
- Fixed custom cache tags 42bd

## [103.1.1] - 03/11/2022

### Fixed

- Fixed an issue with brand in case of multiselect brand attribute

## [103.1.0] - 01/11/2022

### Changed

- Added custom stock dimension index in checkout products array[]

## [103.0.9] - 25/10/2022

### Fixed

- Fixed wrong ec.js file included in the downloadable ZIP.

## [103.0.8] - 18/10/2022

### Fixed

- Fixed missing form_key for ViewContent request (CAPI)

## [103.0.7] - 12/10/2022

### Fixed

- Fixed CAPI ViewContent request occuring while API isn't enabled.

## [103.0.6] - 12/10/2022

### Fixed

- Fixed PHP 8.1 error: Exception: Deprecated Functionality: addcslashes(): Passing null to parameter #1 ($string) of type string


## [103.0.5] - 11/10/2022

### Changed

- Shifted CAPI ViewContent event to fire using a client-based request to overcome FPC issues

## [103.0.4] - 06/10/2022

### Changed

- Changed AEC.ajax() method name to AEC.add()
- ec.js cleanup

## [103.0.3] - 04/10/2022

### Added

- Added bundle[] to purchase payload (in case of bundle purchase)

## [103.0.2] - 28/09/2022

### Fixed

- Fixed PHP 8.1 error e.g. Deprecated Functionality: usort(): 

### Added

- Added ae_ec_log table to log some actions that occur in the background (such as Facebook Conversion API tracking etc.). Accessible in Admin -> Reports -> Logs

## [103.0.1] - 28/09/2022

### Fixed

- Fixed GA4 compatibility with add_to_wishlist event from categories

## [103.0.0] - 27/09/2022

### Fixed

- Fixed performance issues related to category loading

## [102.1.9] - 09/09/2022

### Added

- Added 'customize' event pushed into dataLayer[] object whenever a customizable product option is selected/checked

## [102.1.8] - 09/09/2022

### Fixed

- Fixed some wrong dates in CHANGELOG.md

## [102.1.7] - 08/09/2022

### Fixed

- Fixed an exception triggered when credit memos are created for orders with deleted products

## [102.1.6] - 08/09/2022

### Added

Added 'login' event dataLayer[] pusg after successful login

## [102.1.5] - 07/09/2022

### Added

Added 'eventEmail' with customer email in newsletterSubscribe event

## [102.1.4] - 05/09/2022

### Fixed

- Fixed duplicate impressions for widgets with multiple selectors

## [102.1.3] - 15/08/2022

### Fixed

- Updated consistency of 'list' attribute in checkout and other parts of the site

## [102.1.2] - 10/08/2022

### Fixed

- Fixed wrong error message in Plugin/OrderService.php

## [102.1.1] - 09/08/2022

### Fixed

- Fixed wrong visitorLifetimeValue due to picking orders with status cancelled, fraud etc.

## [102.1.0] - 05/08/2022

### Added

- Added *.facebook.com in form-action policy (csp_whitelist.xml)

## [102.0.9] - 29/07/2022

### Added

- Added extended compatibility with Hyva compatibility module

## [102.0.8] - 26/07/2022

### Fixed

- Fixed a search results page issue related to a third party module custom cache tags 42bd

## [102.0.7] - 22/07/2022

### Fixed

- Fixed scope config errror in config screen

## [102.0.6] - 07/07/2022

### Fixed

- Fixed Anowave_Package composer.json issue

## [102.0.5] - 07/07/2022

### Fixed

- Fixed invalid checkout redirect on mobile (Safari/Chrome)

## [102.0.4] - 05/07/2022

### Fixed

- Changed constructor parameters of DepresonalizePlugin from factory to direct model instance. Caused some session issues on some setups

## [102.0.3] - 29/06/2022

### Added

- Added extended support for some third party modules

## [102.0.2] - 20/06/2022

### Fixed

- Fixed performance issues in PDP pages

## [102.0.1] - 16/06/2022

### Fixed

- Added custom product identity tags 42bd

## [102.0.0] - 16/06/2022

### Fixed

- Fixed main.CRITICAL: Error: Call to undefined method Magento\Customer\Model\Data\Customer::getAttribute() in app/code/Anowave/Ec/Helper/Data.php:5303

## [101.9.9] - 16/06/2022

### Added

- Improved support for wildcard licensing
- Added extended versioning to support sub-package updates

## [101.9.8] - 16/06/2022

### Fixed

- Fixed some performance updates

## [101.9.7] - 16/06/2022

### Added
 
- Added custom event footer_after_get_result to allow for footer modification

## [101.9.6] - 14/06/2022

### Fixed

- Added performance tracking to consent queue

## [101.9.5] - 14/06/2022

### Fixed

- Reduced jQuery() dependency

## [101.9.4] - 10/06/2022

### Fixed

- Fixed compatibility with Anowave_Package

## [101.9.3] - 08/06/2022

### Fixed

- Fixed trim() notice in PHP 8.1

## [101.9.2] - 08/06/2022

### Fixed

- Fixed compatibility with updated Anowave_Package

## [101.9.1] - 07/06/2022

### Fixed

- Added ec_track => 1 filter for cancelled orders to repevent reverse transactions for orders that weren't tracked in the first place.

## [101.9.0] - 30/05/2022

### Fixed

- Removed alert() call 

## [101.8.9] - 27/05/2022

### Fixed

- Fixed 8.1 related warning message appearing on refunds

## [101.8.8] - 26/05/2022

### Fixed

- Fixed Google_Auth_Exeception issue triggering when module is disabled

## [101.8.7] - 17/05/2022

### Fixed

- Fixed a bug related to transactions not being recorded in local log table if sent via Mass action in Order Sales Grid

## [101.8.6] - 17/05/2022

### Fixed

- Fixed typos in few warning messages related to API usage
- Fixed some compatibility issues with Anowave_Ec4 package

## [101.8.5] - 13/05/2022

### Fixed

- Minor notice message text changes.

## [101.8.4] - 13/05/2022

### Fixed

- Changed EE Async Purchase and EE Async Impression tags to non-interaction hits to prevent wrong bounce rate on category and success pages

## [101.8.3] - 11/05/2022

### Fixed

- Fixed a trim() triggered notice message in PHP 8.1

## [101.8.2] - 10/05/2022

### Fixed

- Fixed openssl issue

## [101.8.1] - 09/05/2022

### Fixed

- Fixed compilation issues related to PHP 8.1

## [101.8.0] - 08/05/2022

### Added

- Added GDPR consent log (see Admin -> Reports -> Marketing -> GDPR consent) 

## [101.7.9] - 04/05/2022

### Fixed

Fixed PHP 8.1 compatibility issues

## [101.7.8] - 04/05/2022

### Fixed

- Removed redundant code

## [101.7.7] - 04/05/2022

### Fixed

- Fixed typehint issue with Helper/Data::getIdentifier() method

## [101.7.6] - 02/05/2022

### Fixed

- Fixed PHP 8.1 compatibility issues in Anowave_Package

## [101.7.5] - 02/05/2022

### Improved

- Improved support for PHP 8.1

## [101.7.4] - 28/04/2022

### Fixed

- Minor bug fixes

## [101.7.3] - 28/04/2022

### Added

- Extended GA4 support for native widgets

## [101.7.2] - 28/04/2022

### Added

- Added ae_ec_gdpr table to store granted consent.

## [101.7.0] - 28/04/2022

### Added

- Added new setting called Identifier which allows to set the 'id' attribute source used in Enhanced Ecommerce tracking

## [101.6.9] - 26/04/2022

### Changed

- Optimized data-* attributes

### Added

- Added support for PHP 8.1

## [101.6.8] - 19/04/2022

### Changed

- Change session model to session factory dependency for customer

### Fixed

- Fixed an issue with configurable swatches in categories

## [101.6.7] - 19/04/2022

### Added

- Added compatibility support for PHP 8.0

## [101.6.6] - 14/04/2022

### Fixed

- Fixed an error for logged customers related to Facebook Api Conversion UserData()

## [101.6.5] - 13/04/2022

### Added

- Added Facebook Conversion API User_Data in case of non logged purchase

## [101.6.4] - 29/03/2022

### Added

- Added ability to pick the cookie consent event that fires the Facebook Pixel tracking. 

## [101.6.3] - 23/03/2022

### Fixed

- Added *.google.com in connect-src CSP policy

## [101.6.2] - 23/03/2022

### Fixed

- Compatibility updates for Anowave_Ec4 (Disabling mixing of tags/triggers/variables, added ability to managed this by enabling/disabling Anowave_Ec4 module)

## [101.6.1] - 23/03/2022

### Fixed

- Compatibility updates for Anowave_Ec4

## [101.6.0] - 21/03/2022

### Fixed

- Fixed a minor bug in previous version related to Facebook Conversion API

## [101.5.9] - 18/03/2022

### Added

- Added extended UserData() to Facebook Pixel Conversion API

## [101.5.8] - 18/03/2022

### Added

- Added new option to allow for GDPR pre-selection. Set in Stores -> Configuration -> Anowave Extensions -> Google Tag Manager -> Cookie Consent Directive / GDRP -> Check all by default (Available only in SEGMENTED mode)

## [101.5.7] - 04/03/2022

### Added

- Changed ae_ec.ec_order_id to unique index to prevent duplicates

## [101.5.6] - 22/02/2022

### Added

- Added newsletterUnsibscribeSubmit event upon newsletter unsubscribe

## [101.5.5] - 22/02/2022

### Added

- Added 'customerRegister' event

payload = 
{
	event: 		 'customerRegister', 
	eventAction: 'createAccount'
}

## [101.5.4] - 16/02/2022

### Fixed

- Fixed an issue causing JS error when ec.js is minified.

## [101.5.3] - 08/01/2022

### Fixed

- Fixed an error related to missing user agent

## [101.5.2] - 25/01/2022

### Fixed

- Fixed small JS errors result of reducing jQuery usage

## [101.5.1] - 25/01/2022

### Fixed

- Fixed missing form validate

## [101.5.0] - 25/01/2022

### Changed

- Less jQuery

## [101.4.9] - 25/01/2022

### Changed

- Reduced jQuery usage (part of no_jquery plans) in favour of vanilla JS code

## [101.4.8] - 06/01/2022

### Fixed

- Fixed a nasty bug related to PHPSESID getting regenerated from 404 requests in Developer mode.

## [101.4.7] - 06/01/2022

### Added

- Added IP non-user ID exclusion option.

## [101.4.6] - 03/01/2022

### Added

- Added IP exclusion list. Suitable for disabling module for specific IPs (for cache warmers etc.)

## [101.4.5] - 01/12/2021

### Removed

- Removed onclick attribute event handler tracking
- Removed support for Internet Explorer (less than 2.15% global usage)

### Added

- Improved compatibility with Anowave_Ec4 module

### Changed

- Removed jQuery() dependency in \app\code\Anowave\Ec\view\frontend\templates\events.phtml
- Added xmlhttprequest listener (via PerformanceObserver) to re-apply click listeners for dynamically loaded content (via AJAX)

## [101.4.4(3)] - 26/11/2021

### Changed

- Added indexes to ae_ec table to perform better in sales order grid

## [101.4.2] - 26/11/2021

### Changed

- Moved self-assessment statistics to separate modal dialog to avoid inflating dashboard with charts

## [101.4.1] - 26/11/2021

### Fixed

- Fixed a compilation error related to recent update (statistics)

## [101.4.0] - 25/11/2021

### Fixed

- Fixed better compatibility with Anowave_Ec4 (Measurement Protocol 4 implementation)

## [101.3.9] - 25/11/2021

### Added

- Updated self-assessment charts, added order type

## [101.3.8] - 25/11/2021

### Fixed

- Fixed wrong 'currentStore' value for direct add to cart tracking from categories

## [101.3.7] - 25/11/2021

### Added

- Added Google Analytics Tracking statistics on dashboard

### Fixed

- Better compatibility with Anowave_Ec4 (https://www.anowave.com/marketplace/magento-2-extensions/magento-2-google-analytics-4-enhanced-ecommerce-tracking-gtm/) extension

## [101.3.6] - 24/11/2021

### Added

- Added ec_track flag in (ae_ec) table
- Added ec_placed_at timestamp column in (ae_ec) table
- Added ec_updated_at timestamp column in (ae_ec) table
- Added timestamp tracking to placed order
- Added ability to correlated placed orders and success page hits to improve tracking accuracy.
- Added ability to filter orders by failed and successfull tracking


### Fixed

- Fixed an issue with configurable child products when "Use simple SKU(s)" option is turned on

## [101.3.5] - 23/11/2021

### Added

- Added new column UA/GA4 in sales grid to mark orders that were tracked in GA (on success page)

## [101.3.4] - 10/11/2021

### Fixed

- Fixed wrong pagination for some themes
- Added ProductView model to extend support for deferred scripts

## [101.3.3] - 04/11/2021

### Fixed

- Removed a die() statement left from debugging.


## [101.3.2] - 03/11/2021

### Fixed

- Fixed minor issue with upsell products related warning.

## [101.3.1] - 22/10/2021

### Fixed

- Fixed an issue with $_SERVER['HTTP_HOST'] throwing errors in cronjobs

## [101.3.0] - 18/10/2021

### Fixed

- Small code updates

## [101.2.9] - 18/10/2021

### Changed

- Changed enhanced_conversion_data to global JS variable independable of conversion settings.

### Added

- Added Facebook Conversions API test_event_code to allow for proper debugging and testing
- Improved event deduplication

## [101.2.8] - 15/10/2021

### Fixed

- Fixed missing shipping method tracking caused by recent update

## [101.2.7] - 15/10/2021

### Added

- Added Facebook Conversions API support (Supported events: ViewContent, AddToCart, InitiateCheckout, Purchase)
- Added Event Deduplication support between Conversions API and Pixel

## [101.2.6] - 13/10/2021

### Fixed

- Fixed wrong price for grouped product detail view. (Was excl. tax, now incl. tax)

## [101.2.5] - 13/10/2021

### Added

- Added support for enhanced conversions (only for gtag.js based conversion tracking). If enabled, the module will create a global variable: enhanced_conversion_data

## [101.2.4] - 13/10/2021

### Added

- Added eventID parameter for fbq('track') (Facebook Pixel Tracking) to allow for event deduplication
- Added 'facebook_latest_uuid' cookie that holds the latest event pushed in pixel. This should allow for server-side deduplication.

Example: 

facebook_latest_uuid => 
{
	uuid: '9839f8ae-1c37-4f1d-a11a-900e1c288c9f',
	event: 'AddToCart'
}

## [101.2.3] - 12/10/2021

### Changed

### Added Facebook Pixel load listener to handle lazy consent-dependable pixel actions.

## [101.2.2] - 27/09/2021

### Fixed

- Fixed wrong AdWords Conversion Tracking value when no  currency is set in AdWords Configuration section

## [101.2.1] - 23/09/2021

### Fixed

- Minor issues related to some setup with multi-store category tree

## [101.2.0] - 08/09/2021

### Fixed

- Fixed compatibility with Anowave_Ec4

### Added

- Added a new feature called 'Summary'. If enabled (Stores -> Configuration -> Anowave Extensions -> Google Tag Manager -> Enhanced Ecommerce tracking preferences -> Use summary) it will do a second dataLayer[] push with current cart products

e.g. 

dataLayer = { event: 'summary', eventTrigger: ['ec.cookie.remove.item.data','ec.cookie.update.item.data','ec.cookie.add.data'], items:[] }

## [101.1.9] - 30/08/2021

### Fixed

- Fixed compatibility with GA4 module (https://www.anowave.com/marketplace/magento-2-extensions/magento-2-google-analytics-4-enhanced-ecommerce-tracking-gtm/)

## [101.1.8] - 30/08/2021

### Fixed

- Fixed wrong conversion currency/revenue data in previous Google AdWords Conversion tracking (does not happen with gtag.js implementation)

## [101.1.7] - 18/08/2021

### Fixed

- Added AEC.Const check in select-payment-method mixin to support a case when module is disabled from admin panel

## [101.1.6] - 16/08/2021

### Fixed

- Fixed wrong content_ids[] (FB pixel) when Use simple SKU's enabled + Use first child sku

## [101.1.5] - 10/08/2021

### Fixed

- Fixed a wrong category chain in product detail page for some themes (very rare case)

## [101.1.4] - 06/08/2021

### Fixed

- Fixed wrong return value in \app\code\Anowave\Ec\view\frontend\web\js\sidebar.js

## [101.1.3] - 30/07/2021

### Fixed

- Changed global 'data' variable in checkout.phtml to 'checkout_data' to reduce conflicts with other modules defining global 'data' variable

## [101.1.2] - 27/07/2021

### Fixed

- Sorted child products for first variant SKU to set the lowest possible price

## [101.1.1] - 26/07/2021

### Fixed

- Minor code optimisation

## [101.1.0] - 26/07/2021

### Added

- Added "Use first variant SKU(s)" option. If enabled, the module will push the first variant SKU on product detail page instead of the configurable SKU. This allows for better ecomm_prodid[] matches.
- Added 'variant_id' in success payload to allow for accessing the variant SKU of a configurable product 

## [101.0.9] - 14/07/2021

### Added

- Added "ACCEPT ALL" menu in Cookie popup

## [101.0.8] - 14/07/2021

### Fixed

- Fixed wrong error message related to missing ae_ec table

## [101.0.7] - 02/07/2021

### Fixed

- Fixed an issue related to widgets and PageBuilder. 

## [101.0.6] - 30/06/2021

### Fixed

- Fixed duplicate impression data caused by multiple productClick events in Search results

## [101.0.5] - 30/06/2021

### Fixed

- Fixed duplicate impressions list caused by multiple productClick events

## [101.0.4] - 24/06/2021

### Changed (IMPORTANT)

- Transaction tracking is now handled via event 'purchase' opposed to being send via Pageview. You MUST run the API again to create:

a) NEW TAG - EE Async Purchase
b) NEW TRIGGER - Event Equals Purchase

These are mandatory for transaction tracking to work properly

### Fixed

- Fixed multi-shipping tracking

## [101.0.3] - 22/06/2021

### Fixed

- Added 'list' parameter to product[] collection in categories and search results. actionField.list seems to not work despite documentation from Google.

## [101.0.2] - 21/06/2021

### Fixed

- Remove optional chaining for wider browser support (SAFARI < 12)

## [101.0.1] - 14/06/2021

### Fixed

- Minor pre-release fixes

## [101.0.0] - 14/06/2021

### Added

- Added transaction track table ae_ec
- Disabled order cancel for orders that were not sent to GA in the first place

## [100.9.8] - 14/06/2021

### Fixed

- Fixed small footer JS issue

## [100.9.7] - 24/05/2021

### Fixed

- Fixed a small issue in \app\code\Anowave\Ec\Plugin\JsFooterPlugin.php related to API calls compatibility

## [100.9.6] - 18/05/2021

### Fixed

- Implemented better compatibility with Anowave_Ec4

## [100.9.5] - 13/05/2021

### Fixed

- Fixed a small JS bug

## [100.9.4] - 11/05/2021

### Fixed

- Fixed a small JS error occuring when module is not enabled (from config) but GTM snippet is present

## [100.9.3] - 23/04/2021

### Fixed

- Reduced some jQuery usage. Improved script performance.

## [100.9.2] - 19/04/2021

### Fixed

- Small fixes related to merge/bundle/minify/move_to_bottom

## [100.9.1] - 19/04/2021

### Fixed

- Updated cookiewidget

## [100.9.0] - 19/04/2021

### Added

- Improved GDPR visual / added better-looking checkboxes

## [100.8.9] - 19/04/2021

### Fixed

- Fixed 'Search' event not firing correctly with GDPR

## [100.8.8] - 19/04/2021

### Fixed

- Fixed 'InitateCheckout' event not firing correctly with GDRP

## [100.8.7] - 18/04/2021

### Fixed

- Fixed FB Pixel / GDPR compatibility issue

## [100.8.6] - 18/04/2021

### Fixed

- Fixed a problem related to ec_cache

## [100.8.5] - 07/04/2021

### Fixed

- Fixed invalid GTIN parameters in Customer Reviews

## [100.8.4] - 07/04/2021

### Fixed

- Fixed FB pixel not working if consent mode is not enabled.

## [100.8.3] - 05/04/2021

### Fixed

- Fixed caching issue with GDPR widget

## [100.8.2] - 25/03/2021

### Fixed

- Fixed an autoloading composer issue

## [100.8.1] - 24/03/2021

### Fixed

- Fixed minor issue related to composer 2

## [100.8.0] - 24/03/2021

### Added

- Added a 'placeOrder' event when button is clicked

## [100.7.9] - 24/03/2021

### Fixed

- Fixed 'composer dump-autoload -o' warning messages (API)(psr-0 compatibility with Composer 2)

## [100.7.8] - 04/03/2021

### Fixed

- Fixed mixin applying chain (sidebar.js)

## [100.7.7] - 22/02/2021

### Changed

- Changed FB pixel to load after consent is granted

## [100.7.6] - 17/02/2021

### Fixed

- Removed a var_dump()

## [100.7.5] - 17/02/2021

### Fixed

- Updated composer.json version

## [100.7.4] - 17/02/2021

### Fixed

- Fixed fbq() grant sequence

## [100.7.3] - 11/02/2021

### Fixed

- Fixed a small issue with fbq()

## [100.7.2] - 10/02/2021

### Fixed

- Fixed fbq('grant') & fbq('revoke') not firing on subsequent requests

## [100.7.1] - 02/02/2021

### Fixed

- Added data-attributes to impression payload

## [100.7.0] - 02/02/2021

### Added

- Added visitorLifetimeOrders to reflect number of orders placed by current logged customer

## [100.6.9] - 31/01/2021

### Fixed

- Fixed a bug occuring with active AdBlocker (inability to remove product from cart)

## [100.6.8]

### Fixed

- Fixed a small bug in cross-sell items tracking

## [100.6.7]

### Added

- Added fbq('consent','revoke') (on cookieConsentDeclined event)
- Added fbq('consent','grant') (on cookieConsentGranted event)

## [100.6.6]

### Fixed

- Fixed bug when categories do no list products but static blocks

## [100.6.5]

### Fixed

- Casted coupon code to upper case to avoid ungrouped reporting

## [100.6.4]

### Fixed

- Minor updates

## [100.6.3]

### Fixed

- Minor updates

## [100.6.2]

### Fixed

- Fixed a Composer package version issue

## [100.6.1]

### Added

- Added data-simple-id parameter to "Add to cart" button

### Fixed

- Fixed elasticsearch pagination issues

## [100.6.0]

### Fixed

- Refactored API class/(better compatibility with the new Anowave_Ec4 module)

## [100.5.9]

### Fixed

- Minor documentation updates

## [100.5.8]

### Added

- Added extended set of dispatched attributes to enable support for Google Analytics 4 extension by Anowave (@see https://www.anowave.com/marketplace/magento-2-extensions/magento-2-google-analytics-4-enhanced-ecommerce-tracking-gtm/)

## [100.5.7]

### Added

- Added wildcard domain support (@see \app\code\Anowave\Package\CHANGELOG.md)

## [100.5.6]

### Changed

- Changed 'category' parameter in [addToCart,removeFromCart,productClick,checkout,checkoutStep] events to include the category full path as segments e.g. "Parent category/Child category" instead of just Child category

## [100.5.5]

### Added

- Added AEC.EventDispatcher() to allow for third party scripts to modify dataLayer[] payload via JS on the fly.

AEC.EventDispatcher.on('ec.cookie.impression.data', (data) => 
{
	data['customData'] = 'sample data';
})

## [100.5.4]

### Fixed

- Fixed undefined variants error related to Facebook Pixel Tracking

## [100.5.3]

### Fixed

- Updated CSP policy (csp_whitelist.xml) to include doubleclick fonts/images

## [100.5.2]

### Fixed

- Updated Anowave_Package composer version

## [100.5.1]

### Fixed

- Fixed composer version

## [100.5.0]

### Fixed

- Changed customer reviews md5 checksum to actual email address

## [100.4.9]

### Fixed

- Added a notification related to required impression payload model configuration change for Magento 2.4. 

## [100.4.8]

### Fixed

- Minor PHP 7.4 compatibility issues

## [100.4.7]

### Fixed

- Fixed wrong swatch selector in Magento 2.4
- Changed content_type from 'product' to 'product_group' for configurable products (Facebook Pixel Tracking)

## [100.4.6]

### Added

- Added PHP 7.4 support

## [100.4.5]

### Added

- Added tracking for multicheckout (checkout with multiple addresses). Steps funnel:

Step 1 - Login
Step 2 - Addresses
Step 3 - Shipping
Step 4 - Billing
Step 5 - Overview

(Cart page isn't considered a checkout step because checkout may not start from cart always)

### Fixed

- Added requirejs-min-resolver.js to exclusion list (in minify mode)

## [100.4.4]

### Fixed

- Magento 2.4.0 compatibility updates

## [100.4.3]

### Fixed

- Fixed small typehint error

## [100.4.2]

### Fixed

- Fixed missing currency code in refund payload (added 'cu' parameter)
- Fixed missing currency code in offline order tracking (added 'cu' parameter)

## [100.4.1]

### Added

- Added pageType global key in dataLayer[] object
- Added shipping_country in success push (dataLayer.ecommerce.purchase.actionField.shipping_country)

## [100.4.0]

### Fixed

- Added extended compatibility with 3rd party modules with inline scripts.

## [100.3.9]

### Fixed

- Fixed a (nasty) little bug in Chrome (price-box.js) setting wrong data-price attribute for discounted modules only.

## [100.3.8]

### Fixed

- Added CSP whitelist (Content Security Policy)(see app/code/Anowave/Ec/etc/csp_whitelist.xml
- Added productClick track for Cross Sell products in cart

## [100.3.7]

### Fixed

- Added support for PHP 7.3

## [100.3.6]

### Fixed

- Fixed Cannot read property 'COOKIE_DIRECTIVE' of undefined in Anowave_Ec/js/swatch-renderer.js error when module is disabled

## [100.3.5]

### Fixed

- Added post load method for search results
- Fixed wrong position parameter for search results, position on second and next pages will start based on the page number.

## [100.3.4]

### Fixed

- Fixed a data-omit not added to *.min.js files (in production mode)

## [100.3.3]

### Fixed

- Fixed invalid getEmail() on null error related to customer reviews on success page

## [100.3.2]

### Added

- Added data-ommit="true" to scripts to ensure proper dataLayer[] initialization sequence

## [100.3.1]

### Added

- Added 'products' [] to ec_get_purchase_attributes event

## [100.3.0]

### Fixed

- Removed optional dependency in  \app\code\Anowave\Ec\view\frontend\web\js\price-box.js

## [100.2.9]

### Added 

- Added 'product' to ec_get_add_attributes event
- Added Developer Guide.pdf describing all events dispatched by the module

## [100.2.8]

### Added 

- Added new event - ec_get_visitor_data

## [100.2.7]

### Added 

- Added new event - ec_get_update_quantity_attributes

## [100.2.6]

### Added 

- Added new event - ec_get_checkout_products

## [100.2.5]

### Added

- Added 'product' as event parameter in the following events:

ec_get_impression_item_attributes
ec_get_detail_attributes
ec_get_impression_related_attributes
ec_get_impression_upsell_attributes
ec_get_detail_data_after

## [100.2.4]

### Fixed

- Fixed a problem with Downloadable products generating fatal error on success page

## [100.2.3]

### Fixed

- Fixed an undefined error in a few mixins triggered when module is disabled from config

## [100.2.2]

### Fixed

- Made Cookie Consent Decline text translateable

## [100.2.1]

### Fixed

- Fixed missing stock item error

### Added

- Added a performance optimization in categories (should be activated explicitly via configuration option). (See Stores -> Configuration -> Anowave Extensions -> Google Tag Manaher -> Enhanced Ecommerce Tracking Preferences -> Impression payload model)

## [100.2.0]

### Fixed

- Fixed a performance issue (approx. 1 sec.) in Data::getImpressionPushForward() method caused by backwards compatibility with Magento 2.2

## [100.1.9] (20/11/2019)

### Added

- Added built-in support for Google Customer Reviews

## [100.1.8]

### Fixed

- Fixed missing stock items

## [100.1.7]

### Added

- Added stock tracking via custom dimension. Dimension index can be set in Stores -> Configuration -> Anowave Extensions -> Google Tag Manager -> Custom Dimensions -> Stock dimension index. Default is 10

## [100.1.6]

### Fixed

- Fixed a missing callback resetsSwatchSelection

## [100.1.5]

### Added

- Added 'stock' parameter in product detail payload. 

## [100.1.4]

### Fixed

- Fixed a JS error triggered when module is in disabled state (configurable.js mixin fix)

## [100.1.3]

### Fixed

- Changed CategoryAttributeInterface::ENTITY_TYPE_CODE to ProductAttributeInterface::ENTITY_TYPE_CODE

## [100.1.2]

### Added

- Added ability to set custom ecomm_prodid (customer attribute or default to SKU)

## [100.1.1]

### Added

- Added a new feature to allow visitors to DECLINE cookies.

## [100.1.0]

### Fixed

- Fixed incorrect removeFromCart tracking triggered on cart page (in case of 2+ products). Fixed proposed by @Rafał Tarnowski (Fast White Cat)

## [100.0.9]

### Fixed

- Magento 2.3.3 compatibility updates

## [100.0.8]

### Added

- Added cross sell list tracking for 3rd parties implemented on product detail page.

## [100.0.7]

### Fixed

- Added base_price in removeFromCart payload

## [100.0.6]

### Fixed

- Fixed wrong 'price' parameter used in removeFromCart push

## [100.0.5]

### Added

- Added Facebook Pixel Advanced Matching Parameters init helper

## [100.0.4]

### Fixed

- Fixed a problem with Page Builder (Magento 2.x EE)

## [70.0.0]

### Fixed

- Composer/bitbucket issues

## [60.0.9]

### Fixed

- Fixed an issue with timingValue (changed from seconds to milliseconds)

## [60.0.8]

### Added

- Added variant tracking for attrubutes added via additional_attributes[]

## [60.0.7]

### Added

- Added tracking for "Customizable options" for SIMPLE products. Selected options are tracked in 'variant' parameter.
- Update API settings (minor changes)

## [60.0.6]

### Fixed

- Changed Google Sign In button to comply with Google sign-in branding guidelines (https://developers.google.com/identity/branding-guidelines)

## [60.0.5]

### Fixed

- Fixed undefined error related to AEC.CONFIGURABLE_SIMPLES when license is not inserted

## [60.0.4]

### Added

- Private browser fallback import (from the version for Magento 1.x)

## [60.0.3]

### Fixed

- Fixed configurable/swatch JS error when all options get unselected

## [60.0.2]

### Fixed

- Fixed Magento 2.3.2 comptibility issues (updated _loadCache() methods in Block/Track.php and Block/Cookie.php). Thanks to Visma Digital Commerce AS (www.visma.com)

## [60.0.1]

### Added

- Added virtualVariantView to track detail view of configurable swatches (pushesh virtualVariantView event in dataLayer[])

## [60.0.0]

### Added

- Added content_type: product_group for Search (Facebook Pixel)
- Added value: 0 or visitor id (Facebook Pixel)
- Added content_type to AddPaymentInfo

## [50.0.9]

### Fixed

- Minor API optimisations

## [50.0.8]

### Added

- Added API throttle option

## [50.0.7]

### Added

- Added advanced API usage/configuration for avoiding quota limitations (by Google). Requires advanced configuration skills and Google App

## [50.0.6]

### Fixed

- Fixed missing dependency

## [50.0.5]

### Fixed

- GTM API compatibility/quota issues

### Added 

- Added banner promotion tracking for Magento 2.x Enterprise versions

## [50.0.4]

### Fixed

- Fixed ReferenceError: assignment to undeclared variable l error at checkout

## [50.0.3]

### Changed

- Minor updates

## [50.0.2]

### Added

- Added virtualVariantView to track detail view of configurable variants
- Added new API trigger - Event Equals Virtual Variant View
- Added new API tag - EE Virtual Variant View

## [50.0.1]

### Fixed

- Implemented a configurable.js mixin update (Zac Chapman suggestion)

## [50.0.0]

### Fixed

- Fixed a configurable mixin issue
- Fixed incorrect impression pricing when multi-currency/catalog rules involved

## [40.0.9]

### Fixed

- Fixed a JS error at shipping to payment transition

## [40.0.8]

### Added

- Added CustomizeProduct event to Facebook Pixel tracking

## [40.0.7]

### Added

- Added AddPaymentInfo event to Facebook Pixel tracking

## [40.0.6]

### Fixed

- Fixed compatibility issue with Magento 2.1.x

## [40.0.5]

### Fixed

- Added better inline script pre-processing to extend compatibility with 3rd parties 

## [40.0.4]

### Fixed

- Fixed default values for typehints (PHP 7.2 compatibility)

## [40.0.3]

### Changed

- Added composer.json PHP 7.2 dependecy support in ec/package


## [40.0.2]

### Changed

- Added composer.json PHP 7.2 dependecy support

## [40.0.1]

### Fixed

- Fixed empty value for 'brand' key for bundle products

## [40.0.0]

### Fixed

- Fixed minicart rendering issue in Magento 2.3.x

## [30.0.9]

### Added

- Added "checkEmailAvailability" event dataLayer[] push at standard Magento 2.x checkout 
- Added "backToShippingMethod" event dataLayer[] push at standard Magento 2.x checkout

## [30.0.8]

### Fixed

- Refactored sidebar.js plugin to call parent methods as well.

## [30.0.7]

### Added

- Added 'currentCategory' parameter in category impression push to allow correlation between list/sort modes ex.:

 currentCategory => 
 {
	mode: 'list',
	sort' 'position'
 }

- Added coupon apply/cancel event tracking

 event: 			'applyCouponCode',
 eventCategory:	'Coupon',
 eventAction:	'Apply',
 eventLabel: 	 this.couponCode()

## [30.0.6]

### Fixed

- Fixed compatibility issue with Magento 2.3 in file \app\code\Anowave\Ec\view\frontend\web\js\step-navigator\plugin.js

Replaced: 

steps.sort(this.sortItems).forEach(function(element, index)

with: 

steps().sort(this.sortItems).forEach(function(element, index)

## [30.0.5]

### Fixed

- Fixed wrong category in Search results. 
- Fixed potential minor tracking issue with cart quantity item update (mini-cart). Occurs only on certain setups with particular set of third party plugins.

### Added

- Magento 2.3.x compatibility

## [30.0.4]

### Fixed

- Fixed a problem with upsells impressions not working on product detail page

## [30.0.3]

### Added

- Added Performance API implementation (optional) allowing to measure:

a) Homepage load time

These reports can be later correlated by country, city and other axis in Google Analytics -> Behaviour -> Site speed -> User timings. This feature if accepted well, will be extended in future versions of the module to allow for more metrics and measurements

## [30.0.2]

### Added

- Added a new option that allows pre-processing strip of inline HTML generated by 3rd parties to prevent breaking HTML DOM

## [30.0.1]

### Fixed

- Fixed a warning message related to missing $_GET['q'] parameter in search results

## [30.0.0]

### Fixed

- Refactored Serializer\Json for backwards compatiblity < 2.2.6

## [20.0.8]

## [20.0.9]

### Fixed

- Refactored SwatchTypeChecker for backwards compatiblity < 2.2.6

## [20.0.8]

### Added

- Direct addToCart track for configurable swatches from categories

### Fixed

- Wrong quantity pushed in dataLayer[] from direct addToCart tracking from categories. It resolved to NaN in some themes

## [20.0.7]

### Added

- Added catalogCategoryAddToCartRedirect event in dataLayer[] to track a situation where customer gets redirect to product detail page when trying to add configurable product from categories.

## [20.0.6]

### Added

- Added google_conversion_order_id parameter to allow for conversion de-duplication and prevent duplicate AdWords Conversion tracks

## [20.0.5]

### Added

- Added Facebook Pixel CompleteRegistration event

## [20.0.4]

### Fixed

- Added missing Facebook Pixel "Add to cart" event fired from direct add to cart buttons in listings/categories

## [20.0.3]

### Fixed

- Added support for PHP 7.1.0 in composer.json

## [20.0.2]

### Fixed

- Fixed a problem with AdWords Conversion Tracking when compiled, doubleslash caused a generation issues.

## [20.0.1]

### Fixed

- Fixed an event callback use when using an "onclick" Event handler.

## [20.0.0]

### Fixed

- Fixed a possible issue related to pulling wrong category (defaults to Default Category) when product is a added in categories that are not direct children of Root category and/or are invisible/inactive.

## [19.0.9]

### Fixed

- Fixed a possible issue related to wrong UA-ID when refunding an order / refund

## [19.0.8]

### Fixed

- Fixed a possible related to wrong UA-ID when cancelling an order / transaction reversal

## [19.0.7]

### Fixed

- Made 'list' parameter optional in 'detail' actionField due to discrepancy in Google's documentation. Presense of 'list' parameter may result in corrupted 'position' in reports.

## [19.0.6]

### Fixed

- Monor updates

## [19.0.5]

### Fixed

- Fixed a problem with AdWords Conversion Tracking not working when Cookie Consent is not active by default. This fix applies for setups with Cookie Consent disabled by default.

## [19.0.4]

### Fixed

- Fixed a potential issue with empty widgets
		
## [19.0.4]

### Fixed

- Fixed scope related issues when using API from Website or Store view

## [19.0.3]

### Fixed

- Fixed an FPC (Full Page Cache) issue related to Cookie Consent in Segment mode
- Monor code cleanup/tidy comments

## [19.0.2]

### Added

- Updated dutch locale
- Added new trigger Event Equals Cookie Consent Granted

## [19.0.1]

### Fixed

- Added quote escape for translated strings in AEC.Message constants (confirmRemoveTitle etc.)

### Added

- Added translation files - i18n/en_US.csv & i18n/nl_NL.csv

## [19.0.0]

### Fixed

- Fixed cache issue involving multiple product list widgets in single page

## [18.0.9]

### Added

- Fixed some FPC issues related to 'visitor'/User ID feature using a built-in fallback to private data

## [18.0.8]

### Added

- Added cookie type clarification for Segment mode GDPR consent

## [18.0.7]

### Added

- Added NewProducts Widget GDPR compatibility
- Added User Timing GDPR compatibility

## [18.0.6]

### Fixed

- Fixed GDPR/Consent not applying for AdWords Dynamic Remarketing

## [18.0.5]

### New

- Implemented segmented consent to allow for triggering different types of tracking/cookies. Main update is in ec.js, make sure you've deployed it's latest version

## [18.0.4]

### Fixed

- Fixed AdWords Conversion Tracking triggering automatically without consent

## [18.0.3]

### Added

- Added a cookie consent modes Generic/Segment to allow for more profiled consent. In Segment mode, consent can be segmented e.g. 

GENERIC MODE

cookieConsentGranted

SEGMENT MODE

cookieConstentGranted
cookieConsentMarketingGranted
cookieConstentPreferencesGranted
cookieConsentAnalyticsGranted

This can allow running more profiled tags based on different consent scopes.

## [18.0.2]

### Fixed

- Hide Facebook Pixel Code if functionality is disabled from admin panel

## [18.0.1]

### Added

- Transaction filter-out by payment method (available in BETA mode only). Allows for filtering out specific transactions and not getting them recorded in GA. Suitable for financial options.

## [18.0.0]

### Added

- Added extended support for Google Optimize. Assisted and Standalone implementation based on pure Google Analytics

## [17.0.9]

### Added

- NEW: Tracking for Catalog Products List widget (impression and event tracking)

## [17.0.8]

### Added

- Added 2 new tags in GTM API

a) EE Add To Wishlist
b) EE Add To Compare

### Fixed

- Minor bug between version 17.0.4 and 17.0.7 related to API not pulling proper account id and therefore not loading container IDs

## [17.0.7]

### Added

- Added missing Add to wishlist/Add to Compare tracking.

## [17.0.6]

### Added

- NEW: GDPR/Cookie Consent to apply for Facebook Pixel Tracking automatically.

To make Facebook Pixel GDPR compatible, a change in Facebook Pixel Code is required e.g.

PREVIOUS SNIPPET (ORIGINAL FACEBOOK PIXEL SNIPPET)

<script>

	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
	
	fbq('init', '<your pixel id>'); 
	fbq('track', 'PageView');
	
</script>

NEW SNIPPET

<script>

	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
	
	AEC.CookieConsent.queue(function()
	{
		fbq('init', '<your pixel id>'); 
		fbq('track', 'PageView');
	});
	
</script>


## [17.0.5]

### Changed

- Updated app\code\Anowave\Package\CHANGELOG.md

## [17.0.4]

### Added

- Added product-specific coupon code in purchase push (if sales rules Actions is per matching items only)

## [17.0.3]

### Added

- Added 2 new variables in purchase JSON push (to allow for using them as custom dimensions) e.g.

a) Added payment method in purchase push
b) Added shipping method in purchase push

## [17.0.2]

### Added

- Added new event - ec_get_purchase_push_after

## [17.0.1]

### Fixed

- Improved cookie consent behaviour with FPC (Full Page Cache)

## [17.0.0]

### Fixed

- Changed name of consent cookie to cookieConsentGranted 
- Minor updates

## [16.0.9]

### Added

- GDPR rules for ALL events

## [16.0.8]

### Fixed

- Possible XSS vulnerability in app\code\Anowave\Ec\view\frontend\templates\search.phtml 

## [16.0.7]

### Changed

- Container ID is now dropdown (changed from text) to ease customers in inserting/configuring proper container ID.

## [16.0.6]

### Fixed

- Missing brand param for configurable products
- Small speed optimization with regards to brand (backreference)

## [16.0.5]

### Fixed

- Fixed if (current[i].id.toString().toLowerCase() === reference.id.toString().toLowerCase())

## [16.0.4]

### Fixed

- Ability to incl/excl. tax on product item (purchase push)

## [16.0.3]

### Added

- AdWords Dynamic Remarketing dynx_* support (optional)

## [16.0.2]

### Fixed

- Add to cart not firing for bundle product type

## [16.0.1]

### Added

- Extended support for static brands (text fields)

## [16.0.0]

### Fixed

- Changed default bind to "Use jQuery() on". Deprecated binding using onclick attribute
- Minor improvements in licensing instructions

## [15.0.9]

### Fixed

- Extended support for unicode characters (Greek, Arabic etc.) + Reduced JSON payload size for unicode characters

## [15.0.8]

### Fixed

- Minor updates

## [15.0.7]

### Fixed

- ReferenceError: data is not defined (on product click)

## [15.0.6]

### Fixed

- Invalid entity_type specified: customer error while running: php bin/magento setup:install

## [15.0.5]

### Fixed

- Wrong 'value' parameter at InitiateCheckout (Facebook Pixel tracking)

## [15.0.4]

### Added

- Ability to send transactions to Google Analytics via Mass Actions (Order Grid)

## [15.0.3]

### Fixed

- Updated dependent Anowave_Package extension to remove Undefined offset 1 error.

## [15.0.2]

### Fixed

- Bug related to cancellation of pending orders.

## [15.0.1]

### Fixed

- Minor updates in localStorage feature

### Added

- Ability to show/hide remove from cart confirmation popup

## [15.0.0]

### Added

- Backreference for categories based on localStorage. Allows for assigning correct category in checkout push (e.g. category from which product was added in cart). Fixes multi-category products issues.

## [14.0.9]

### Added

- Optional order cancel tracking / Ability to disable order cancel tracking

## [Events]

ec_get_widget_click_attributes 			- Allows 3rd party modules to modify widget click attributes e.g. data-attributes="{[]}"
ec_get_widget_add_list_attributes 		- Allows 3rd party modules to modify widget add to cart attributes e.g. data-attributes="{[]}"
ec_get_click_attributes 				- Allows 3rd party modules to modify product click attributes e.g. data-attributes="{[]}"
ec_get_add_list_attributes 				- Allows 3rd party modules to modify add to cart from categories attributes e.g. data-attributes="{[]}"
ec_get_click_list_attributes 			- Allows 3rd party modules to modify category click attributes e.g. data-attributes="{[]}"
ec_get_remove_attributes				- Allows 3rd party modules to modify remove click attributes e.g. data-attributes="{[]}"
ec_get_add_attributes					- Allows 3rd party modules to modify add to cart attributes e.g. data-attributes="{[]}"
ec_get_search_click_attributes			- Allows 3rd party modules to modify search list attributes e.g. data-attributes="{[]}"
ec_get_checkout_attributes 				- Allows 3rd party modules to modify checkout step attributes e.g. data-attributes="{[]}"
ec_get_impression_item_attributes		- Allows 3rd party modules to modify single item from impressions
ec_get_impression_data_after			- Allows 3rd party modules to modify impressions array []
ec_get_detail_attributes				- Allows 3rd party modules to modify detail attributes array []
ec_get_impression_related_attributes	- Allows 3rd party modules to modify related attributes
ec_get_impression_upsell_attributes		- Allows 3rd party modules to modify upsell attributes
ec_get_detail_data_after				- Allows 3rd party modules to modify detail array []
ec_order_products_product_get_after		- Allows 3rd party modules to modify single transaction product []
ec_order_products_get_after				- Allows 3rd party modules to modify transaction products array
ec_get_purchase_attributes				- Allows 3rd party modules to modify purchase attributes
ec_get_search_attributes				- Allows 3rd party modules to modify search array attributes
ec_api_measurement_protocol_purchase	- Allows 3rd party modules to modify payload for measurement protocol
ec_get_purchase_push_after				- Allows 3rd party modules to modify the purchase push

## [14.0.1]

### Added

- ec_get_detail_data_after event


## [14.0.8]

### Added

- Added selectable brand attribute in Stores -> Configuration -> Anowave Extensions -> Google Tag Manager -> Enhanced Ecomerce Tracking Preferences

## [14.0.7]

### Added

- gtag.js based AdWords Conversion Tracking

## [14.0.6]

### Changed

- Minor updates and tidying system options (for better usability)

## [14.0.5]

### Changed

- Refactored Cookie consent feature to load via AJAX (overcome FPC related issues)

## [14.0.4]

### Fixed

- Typo addNoticeM() to addNoticeMessage() in credit memos

## [14.0.3]

### Fixed

- Illegal string offset 'qty' error related to Gift cards 

## [14.0.2]

### Added 

- Added new custom event - ec_order_products_get_after
- Added new custom event - ec_get_purchase_attributes

## [14.0.1]

### Added

- ec_get_detail_data_after event

## [14.0.0]

### Added

- Transaction reversal
- Adjustable ecomm_prodid attribute. Can be now ID or SKU depending on configuration 

## [13.0.9]

### Added 

- Ability to customize cookie consent dialog.

## [13.0.8]

### Added

- GTM frienldy, built-in Cookie Law Directive Consent
- Adjustable tax settings 

## [13.0.7]

### Changed 

- Minor updates

## [13.0.6]

- Fixed problems with products distributed in categories from different stores. 

## [13.0.5]

## New

- Added 3rd step "Place order" in checkout step tracking. This is to confirm whether customer actually clicked "Place order" button. 
Existing funnel step labels (Google Analytics -> Admin -> E-Commerce -> Funnel step labels) should be updated to:

a) Step 1 (Shipping address)
b) Step 2 (Review & Payments)
c) Step 3 (Place order) 

## Added

- Non-cached private data pushed to dataLayer[] object (beta feature, to be evolved in future)
- Click/Add to cart tracking for homepage widgets (NewProduct widget)
- New selectors for homepage widgets tracking
- Fixed empty widget scenario
- Custom cache for homepage widgets
- New tag (EE NewProducts View)
- New tigger (Event Equals NewProducts View Non Interactive)

## [13.0.4]

## Fixed

- Fixed Fatal error in detail page for products unassigned to any category

## [13.0.3]

## Changed

- Cast 'price','ecomm_pvalue','ecomm_totalvalue' to float insetad of strings. Values are also no longer single quoted.

## [13.0.2]

## Fixed

- Added missing namespace declarations in vendor/Google API

## [13.0.1]

## Fixed

- Cast ecomm_total value to numeric (Facebook Pixel)

## [13.0.0]

## Changed

- Refactored the Google Tag Manager API library

## [12.0.0]

## Changed 

- Updated Google Tag Manager API to use Google Analytics Settings variable for all tags (common)
- Removed unused API files

## [11.0.9]

### Fixed

- Fixed fatal error for Out of stock grouped products
- Refactored/removed direct calls to ObjectManager

## [11.0.8]

### Fixed

- Cast ecomm_totalvalue to float in cart page to remove quotes

## [11.0.7(6)]

### Fixed

- Missing brand value in checkout push

## [11.0.5]

### Fixed

- Fixed stackable "Add to cart" products array.
- Fixed incorrect grouped products array [] passed with addToCart event

## [11.0.4]

### Checked

- Checked Magento 2.2.x compatibility. 

### Fixed

- Fixed wrong product category in Search results. 

## [11.0.3]

### Added

- Flexible affiliation tracking (NEW)

### Fixed

- Fixed Payment method selection not working when module is disabled from configuration screen

## [11.0.2]

### Changed

- Refactored ObjectManager calls
- Disabled "Add to cart" from lists for configurable/grouped products with required variants/options

## [11.0.1]

### Changed

- Refactored to use mixins instead of rewrite in terms of shipping/payment method tracking

## [11.0.0]

### Fixed

- Visual Swatches price change not working in previous version

## [10.0.9]

### Changed

- Minor updates, added a few self-checks regarding module output
- Added self-check regarding 3rd party checkout solutions

## [10.0.8]

### Added

- Added Google Analytics Measurement Protocol / Offline orders tracking

## [10.0.7]

### Added

- Added ability to create Universal Analytics via the API itself.

## [10.0.6]

### Fixed

- Added afterFetchView() method in app\code\Anowave\Ec\Block\Result.php

## [10.0.5]

### Fixed

- Changed from getBaseGrandTotal() to getGrandTotal() at success page to obtain correct revenue correlated to selected currency

## [10.0.5]

### Fixed

- Added missing 'value' parameter on InitiateCheckout (Facebook Pixel)

## [10.0.4]

### Fixed

- Improved Product list attribution / Product position to event correlation
- Fixed wrong remove from cart ID

## [10.0.3]

### Added

- Ability to switch between onclick() binding and jQuery on() binding to increase support for 3rd party AJAX based solutions

## [10.0.2]

### Added

- Added Facebook Pixel Search

## [10.0.1]

### Added

- Cart update tracking (smart addFromCart and removeFromCart)
 
## [9.0.8]

###Added

- Combined product detail views with Related/Upsells/Cross-Sell impressions

## [9.0.7]

###Added

- Mini Cart update tracking (smart addFromCart and removeFromCart)

## [9.0.6]

###Changed

- Cleanup

## [9.0.5]

###Changed

- Refactored DI() (more)

## [9.0.4]

###Changed

- Refactored DI()

## [9.0.3]

###Changed

 - Added explicit "Adwords Conversion Tracking" activating. All previous versions MUST enable it to continue using AdWords Conversion Tracking

## [9.0.2]

### Fixed

- Non-standard Facebook Pixel ViewCategory event
 
## [9.0.1] 

### Changed

### Fixed

- Unable to continue to Payment if license is invalid
- Removed AEC.checkoutStep() method and created AEC.Checkout() with step() and stepOption() methods

## [9.0.0] - 13.06.2017


## [8.0.9] - 07.06.2017

### Added

- controller_front_send_response_before listener to allow for response modification in FPC

## [8.0.8] - 07.06.2017

### Fixed

data-category attribute in "Remove from cart" event

## [4.0.3 - 8.0.7] - 07.06.2017

### Added

- Contact form submission tracking
- Newsletter submission tracking

## [4.0.3]

### Fixed

- Shipping and payment method options tracking for Magento 2.1.3+

## [4.0.2]

### Added

- Added custom cache for categories, minor improvements

## [2.0.8]

### Changed

- GTM snippet insertion approach to match the new splited GTM code. May affect older versions if upgraded.

## [2.0.1 - 2.0.3]

### Fixed

- Incorrect configuration readings in multi-store environment.

## [2.0.0]

### Added

- "Search results" impressions tracking.

## [1.0.9]

### Fixed

- Fixed bug(s) related to using both double and single quotes in product/category names

## [1.0.0]

- Initial version