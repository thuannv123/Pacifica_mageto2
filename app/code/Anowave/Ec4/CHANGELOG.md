# Changelog

All notable changes to this project will be documented in this file.

REQUIRES Anowave_Ec (## [100.5.8.17085] or higher)

## [100.8.0] - 24/05/2023

### Added

- Added custom event 'ec_view_cart' to allow third parties to modify view_cart payload

## [100.7.9] - 24/05/2023

### Fixed

- Performance updates

## [100.7.8] - 24/05/2023

### Fixed

- JS code cleanup

## [100.7.7] - 24/05/2023

### Fixed

- Added ability to import user-scoped properties from UA to GA4 items[] array

## [100.7.6] - 14/05/2023

### Fixed

- Fixed GDRP compliance

## [100.7.5] - 18/04/2023

### Fixed

- Fixed a multi-store issue related to admin orders going to wrong Measurement ID

## [100.7.4] - 28/03/2023

### Fixed

- Fixed wrong 'value' parameter linked to begin_checkout event

## [100.7.3] - 01/03/2023

### Fixed

- Added mising 'value' parameter to begin_checkout

## [100.7.2] - 22/02/2023

### Fixed

- Replaced coupon rule name with coupon code in begin_checkout payload

## [100.7.1] - 20/02/2023

### Fixed

- Added missing 'value' parameter to view_cart event
- Moved 'currency' parameter to ecommerce[] in view_cart event

## [100.7.0] - 09/02/2023

### Fixed

- Removed last underscore from item_category[N] (replaced item_category_[N] to item_category[N]) where N is the catgeory level 

## [100.6.9] - 03/02/2023

### Added

- Added google_business_vertical to items[] array

## [100.6.8] - 02/02/2023

### Fixed

- Small bug fix

## [100.6.7] - 02/02/2023

### Added

- Added missing 'coupon' & 'currency' values for begin_checkout, add_payment_info, add_shipping_info events

## [100.6.6] - 23/01/2023

### Fixed

- Fixed wrong 'affiliation' parameter for admin orders

## [100.6.5] - 20/01/2023

### Fixed

- Fixed promotion tracking issue related to items[] array

## [100.6.4] - 20/01/2023

### Added

- Added promotion tracking (BETA)

## [100.6.3] - 19/01/2023

### Fixed

- Fixed an issue with refunds when product isn't added to any category

## [100.6.2] - 16/12/2022

### Added

- Added items[].affiliation parameter to support affiliations

## [100.6.1] - 15/12/2022

### Fixed

= Fixed TypeError: Anowave\Ec4\Plugin\Api\Measurement\Protocol::afterGetUA(): Argument #3 ($order) must be of type Magento\Sales\Model\Order, null given error

## [100.6.0] - 13/12/2022

### Fixed

- Fixed Uncaught TypeError: reference is null error on cart page

## [100.5.9] - 07/11/2022

### Fixed

- Fixed Uncaught TypeError: reference is null error

## [100.5.8.17085] - 30/11/2022

### Fixed

- Fixed inconsistent category between events

## [100.5.7] - 29/11/2022

### Fixed

- Fixed discrepancy between category set in view_cart and view_item/view_item_list events (based on local storage feature)

## [100.5.6] - 27/11/2022

### Fixed

- Fixed compatibility with Anowave_Ec (103.1.3)

## [100.5.5] - 18/11/2022

### Fixed

- Fixed an issue with categories in multi-store

## [100.5.4] - 16/11/2022

### Fixed

- Fixed error related to credit memos/refunds

## [100.5.3] - 04/11/2022

### Fixed

- Fixed wrong events related to cart update

## [100.5.2] - 28/09/2022

### Fixed

- Fixed wrong items[] parameter in add_to_wishlist event

## [100.5.1] - 05/09/2022

### Fixed

- Improved view_cart parameters

## [100.5.0] - 01/09/2022

### Fixed

## [100.4.9] - 24/08/2022

### Fixed

- Fixed wrong 'index' for widgets

## [100.4.8] - 18/08/2022

### Fixed

- Fixed item_category start index (previously starting from 1)

## [100.4.7] - 15/08/2022

### Fixed

- Updated consistency of 'item_list' and 'item_list_id'

## [100.4.6] - 01/8/2022

### Fixed

- Fixed a JS error in ec4.js related to impression payload

## [100.4.5] - 22/07/2022

### Fixed

- Added custom product identity tags 42bd

## [100.4.4] - 08/06/2022

### Fixed

- Removed redundant Anowave_Package 

## [100.4.3] - 17/05/2022

### Fixed

- Removed redundant code

## [100.4.2] - 16/05/2022

### Fixed

- Added revserse transaction support

## [100.4.1] - 13/05/2022

### Fixed

- Fixed transaction_id discrepancy with cancel order

## [100.4.0] - 09/05/2022

### Fixed

- Fixed transaction_id discrepancy related to refund requests

## [100.3.9] - 08/05/2022

### Fixed

- Fixed minor typos in configuration interface and helpers

## [100.3.8] - 02/05/2022

### Added

- Added User Guide link in configuration to help starters with GA4

## [100.3.7] - 29/04/2022

### Added

- Added support for tracking native widgets

## [100.3.6] - 29/04/2022

### Added

- Added better compatibility with Anowave_Ec module

## [100.3.5] - 18/04/2022

### Added

- Added Measurement Protocol refund tracking

## [100.3.4] - 23/03/2022

### Fixed

- Fixed missing google_tag_params variable in GTM

## [100.3.3] - 23/03/2022

### Added

- Compatibility updates with Anowave_Ec

## [100.3.2] - 23/03/2022

### Added

- Added configuration option to allow for using shared or dedicated Measurement ID. Suitable for separating admin orders from frontend orders

## [100.3.1] - 22/03/2022

### Added

- Added 'traffic_type' parameter to Measurement Protocol purchase event

## [100.3.0] - 13/12/2022

### Added

- Added back EE AdWords Dynamic Remarketing tag
- Added back Event Equals Dynamic Remarketing trigger
- Added back ee conversion id and ee conversion label variables

## [100.2.9] - 01/12/2022

### Added

- Added add_to_compare event tracking
- Added new Event Equals Add To Compare trigger
- Added new EE Add To Compare tag

## [100.2.8] - 29/11/2022

### Fixed

- Fixed a JS error triggering on cart page

## [100.2.7] - 25/11/2022

### Added

- Added offline order tracking via Measurement Protocol 4

## [100.2.6] - 25/11/2022

### Fixed

- Fixed warning for missing UA-ID
- Added warning for unsupported refund tracking yet

## [100.2.5] - 21/09/2022

### Added

- Added view_cart event in cart page

## [100.2.4] - 30/08/2022

### Fixed

- Fixed duplicate begin_checkout event tracking

## [100.2.3] - 26/08/2022

### Fixed

- Removed conditional chaining to extend support for SAFARI

## [100.2.2] - 30/07/2022

### Added

- Added ability to change conversion event from 'purchase' to something else. This improves compatibility with Universal Analytics tracking using 'purchase' events

## [100.2.1] - 20/07/2022

### Fixed

- Minor composer.json updates

## [100.2.0] - 19/07/2022

### Fixed

- Fixed a composer.json psr-4 notation

## [100.1.9] - 18/05/2022

### Fixed

- Implemented some missing events (add_to_cart/remove_from_cart) related to mini-cart updates

## [100.1.8] - 23/04/2022

### Fixed

- Fixed some JS errors
- Added optional chaining to reduce JS errors caused by older Anowave_Ec versions

## [100.1.7] - 18/03/2022

### Fixed

- Small fixes

## [100.1.6] - 18/03/2022

### Fixed

- Fixed error - Uncaught exception 'TypeError' with message 'Argument 1 passed to Anowave\Ec\Helper\Data::getCategory() related to Wishlist

## [100.1.5] - 10/02/2022

### Added

- Added Add Payment Info/Add Shipping info triggers/variables etc.

## [100.1.4]

### Fixed

- Fixed wrong affiliation variable

## [100.1.3]

### Added

- Added new dataLayer[] variables in GTM -> Variables 

ee4 ecommerce purchase transaction id
ee4 ecommerce purchase value
ee4 ecommerce purchase currency
ee4 ecommerce purchase coupon
ee4 ecommerce purchase shipping
ee4 ecommerce purchase tax
ee4 ecommerce purchase affiliation

- Added missing 'transaction_id','value','coupon','tax','shipping' eventParameters to EE4 Purchase tag

## [100.1.2]

### Added

- Added {{ee4 ecommerce purchase items}} dataLayer[] variable

### Fixed

- Fixed missing 'items' eventParameter to ALL tags
- Fixed wrong value for 'items' eventParameter to EE4 purchase tag. (using {{ee4 ecommerce purchase items}} instead of {{ee4 ecommerce items}}

## [100.1.1]

### Fixed

- Fixed wrong event in EE View Item tag

## [100.1.0]

### Fixed

- Fixed productClick JS error

## [100.0.9]

### Fixed

- Minor updates

## [100.0.8]

### Fixed

- Fixed view_item event not firing properly

## [100.0.7]

### Added

- Added ability to turn on/off on demand for separate store views

## [100.0.6]

### Fixed

- Code refactor/minor updates

## [100.0.5]

### Fixed

- Fixed a mixed tags creation

## [100.0.4]

### Added

- Added view_item event

## [100.0.3]

### Added

- Added view_cart,add_to_wishlist events

## [100.0.2]

### Added

- Added add_payment_info, add_shipping_info events 

## [100.0.1]

### Fixed 

- Minor updates

### Added

- Added admin config menu

## [100.0.0]

- Initial version