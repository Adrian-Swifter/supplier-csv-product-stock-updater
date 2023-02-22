=== Supplier CSV Product Stock Updater ===

Contributors: problemizer
Tags: supplier, CSV, product, stock, updater, WooCommerce
Requires at least: 5.0
Tested up to: 6.1.1
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that updates product stock from a supplier CSV file every day at a specified time. 

== Description ==

The Supplier CSV Product Stock Updater plugin allows you to specify a CSV file containing SKU and stock quantity information for your products. The plugin will automatically update the stock quantity for each product in the CSV file every day at a specified time. This is useful for keeping your product stock quantities up-to-date with your supplier's inventory.

== Installation ==

1. Upload the `supplier-csv-product-stock-updater` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the plugin settings page under Settings > Supplier CSV Product Stock Updater and enter the URL for your supplier's CSV file and the time of day when you want the stock to be updated.

== Usage ==

1. Create a CSV file containing two columns: SKU and stock quantity.
2. Upload the CSV file to a publicly accessible URL.
3. Go to the plugin settings page under Settings > Supplier CSV Product Stock Updater and enter the URL for the CSV file and the time of day when you want the stock to be updated.
4. The plugin will automatically update the stock quantity for each product in the CSV file every day at the specified time.

== Frequently Asked Questions ==

**Q. How do I format my CSV file?**

A. The CSV file should have two columns: SKU and stock quantity. The first row should be a header row with the column names.

**Q. How often will the plugin update the stock quantity?**

A. The plugin will update the stock quantity every day at the time you specify in the plugin settings.

**Q. What happens if a product in the CSV file doesn't exist in my store?**

A. The plugin will skip products that don't exist in your store and log an error in the PHP error log.

== Changelog ==

### 1.0.0

* Initial release.

== Upgrade Notice ==

None

== Screenshots ==

None

== Donation ==

None
