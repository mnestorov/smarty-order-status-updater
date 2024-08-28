<p align="center"><a href="https://smartystudio.net" target="_blank"><img src="https://smartystudio.net/wp-content/uploads/2023/06/smarty-green-logo-small.png" width="100" alt="SmartyStudio Logo"></a></p>

# Smarty Studio - Order Status Updater for WooCommerce

[![Licence](https://img.shields.io/badge/LICENSE-GPL2.0+-blue)](./LICENSE)

- Developed by: [Smarty Studio](https://smartystudio.net) | [Martin Nestorov](https://github.com/mnestorov)
- Plugin URI: https://github.dev/smartystudio/smarty-order-status-updater

## Overview

**Smarty Studio - Order Status Updater for WooCommerce** is a WordPress plugin designed to automate the synchronization of order statuses between an external system and your WooCommerce store. This plugin listens for notifications from an external order management system, such as a Laravel application, and updates the WooCommerce order statuses accordingly. It ensures that your WooCommerce store reflects the most current order status, improving the efficiency of order management and customer satisfaction.

## Features

- **Automated Order Status Synchronization:** Automatically updates WooCommerce order statuses based on notifications from an external system.
- **Secure Communication:** Utilizes a secure token-based authentication system to ensure that only authorized requests can update order statuses.
- **Easy to Integrate:** Designed for easy integration with any system capable of sending HTTP POST requests, making it highly versatile.
- **Customizable:** Offers the flexibility to define and use custom order statuses to match your unique workflow.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/smarty-order-status-updater` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

**To use this plugin, you'll need to:**

1. Set a secure token in the plugin code for authentication.
2. Configure the external order management system to send HTTP POST requests to your WooCommerce site's REST API endpoint (`/wp-json/smarty-order-status-updater/v1/update-status/`). These requests must include the order ID, the new status, and the secure token in the request header for authentication.

## Requirements

- WordPress 4.7+ or higher.
- WooCommerce 5.1.0 or higher.
- PHP 7.2+

## Changelog

For a detailed list of changes and updates made to this project, please refer to our [Changelog](./CHANGELOG.md).

---

## License

This project is released under the [GPL-2.0+ License](http://www.gnu.org/licenses/gpl-2.0.txt).