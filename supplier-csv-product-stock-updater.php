<?php

/**
 * Plugin Name: Supplier CSV Product Stock Updater
 * Description: A plugin that updates product stock from a supplier CSV file every day.
 * Version: 1.0.0
 * Author: wrdprssifix.com
 * Author URI: https://wrdprssifix.com/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Register the settings page under Settings
add_action('admin_menu', 'scpsu_settings_page');
function scpsu_settings_page()
{
  add_options_page(
    'Supplier CSV Product Stock Updater Settings',
    'Supplier CSV Product Stock Updater',
    'manage_options',
    'scpsu_settings',
    'scpsu_settings_page_callback'
  );
}

// Display the settings page
function scpsu_settings_page_callback()
{
  if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }

  if (isset($_POST['scpsu_csv_link'])) {
    update_option('scpsu_csv_link', sanitize_text_field($_POST['scpsu_csv_link']));
    update_option('scpsu_update_time', sanitize_text_field($_POST['scpsu_update_time']));
    echo '<div class="updated"><p>Settings saved.</p></div>';
  }

  $csv_link = get_option('scpsu_csv_link');
  $update_time = get_option('scpsu_update_time');

  echo '<div class="wrap">';
  echo '<h1>Supplier CSV Product Stock Updater Settings</h1>';
  echo '<form method="post">';
  echo '<table class="form-table">';
  echo '<tr>';
  echo '<th scope="row"><label for="scpsu_csv_link">Supplier CSV Link</label></th>';
  echo '<td><input type="text" id="scpsu_csv_link" name="scpsu_csv_link" value="' . esc_attr($csv_link) . '" class="regular-text"></td>';
  echo '</tr>';
  echo '<tr>';
  echo '<th scope="row"><label for="scpsu_update_time">Update Time</label></th>';
  echo '<td><input type="time" id="scpsu_update_time" name="scpsu_update_time" value="' . esc_attr($update_time) . '" class="regular-text"></td>';
  echo '</tr>';
  echo '</table>';
  echo '<p class="submit"><input type="submit" class="button-primary" value="Save Changes"></p>';
  echo '</form>';
  echo '</div>';
}

// Update product stock at the specified time of day
add_action('scpsu_update_product_stock', 'scpsu_update_product_stock_callback');
function scpsu_update_product_stock_callback()
{
  $csv_link = get_option('scpsu_csv_link');
  $update_time = get_option('scpsu_update_time');

  if (empty($csv_link) || empty($update_time)) {
    return;
  }

  $now = time();
  $update_time_parts = explode(':', $update_time);
  $update_time_hours = $update_time_parts[0];
  $update_time_minutes = $update_time_parts[1];
  $update_time_seconds = 0;
  $update_time_today = strtotime("today $update_time_hours:$update_time_minutes:$update_time_seconds");

  if ($now < $update_time_today) {
    return;
  }

  $csv_data = file_get_contents($csv_link);

  if (empty($csv_data)) {
    return;
  }
  // Parse the CSV data and update the product stock
  $products = array();

  $rows = str_getcsv($csv_data, "\n");
  foreach ($rows as $row) {
    $data = str_getcsv($row, ",");
    $sku = $data[0];
    $stock_quantity = $data[1];
    $products[] = array(
      'sku' => $sku,
      'stock_quantity' => $stock_quantity
    );
  }

  foreach ($products as $product) {
    $product_id = wc_get_product_id_by_sku($product['sku']);
    if ($product_id) {
      $product_obj = wc_get_product($product_id);
      $product_obj->set_stock_quantity($product['stock_quantity']);
      $product_obj->save();
      error_log('Product stock updated for SKU ' . $product['sku']);
    } else {
      error_log('Product not found for SKU ' . $product['sku']);
    }
  }
}

// Schedule the daily product stock update
add_action('wp', 'scpsu_schedule_daily_product_stock_update');
function scpsu_schedule_daily_product_stock_update()
{
  if (!wp_next_scheduled('scpsu_update_product_stock')) {
    wp_schedule_event(strtotime(get_option('scpsu_update_time')), 'daily', 'scpsu_update_product_stock');
  }
}

// Activate the plugin
register_activation_hook(__FILE__, 'scpsu_activate');
function scpsu_activate()
{
  if (!wp_next_scheduled('scpsu_update_product_stock')) {
    wp_schedule_event(strtotime(get_option('scpsu_update_time')), 'daily', 'scpsu_update_product_stock');
  }
}

// Deactivate the plugin
register_deactivation_hook(__FILE__, 'scpsu_deactivate');
function scpsu_deactivate()
{
  $timestamp = wp_next_scheduled('scpsu_update_product_stock');
  wp_unschedule_event($timestamp, 'scpsu_update_product_stock');
}