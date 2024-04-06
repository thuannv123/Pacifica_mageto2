To install this module do next:

1. Copy folder Isobar/Megamenu into app/code

2. Run
composer require isobar/module-megamenu

3. Run
php bin/magento module:enable Isobar_Megamenu

4. Run
php bin/magento setup:upgrade

5. If not shown on frontend
Clear pub/static folder
rm -rf pub/static

6. Run
bin/magento setup:static-content:deploy -f