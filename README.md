# CodingBunny WhatsApp Chat

![License: GPL v3](https://img.shields.io/badge/license-GPL%20v3-blue.svg)
![WordPress Version](https://img.shields.io/badge/WordPress-%3E%3D%206.0-blue.svg)
![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.0-orange.svg)
![Version](https://img.shields.io/badge/version-1.2.0-green.svg)

**CodingBunny WhatsApp Chat** is a WordPress plugin that allows visitors to contact you via WhatsApp with a single click. This user-friendly plugin adds a WhatsApp chat button to your site, making it easy for users to reach you. An optional PRO version adds advanced features for enhanced user interaction.

## Features

- **One-Click WhatsApp Contact**: Instantly connect site visitors with you via WhatsApp chat.
- **Customizable Display Options**: Customize the chat button and settings through the admin menu.
- **PRO Version Available**: Offers additional chat features and customization options.
- **Multi-language Support**: The plugin is translation-ready for multilingual use.

## Installation

1. Download the plugin and unzip it.
2. Upload the `coding-bunny-whatsapp-chat` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin via the 'Plugins' menu in WordPress.
4. Access the **Settings** page through the WordPress admin to configure WhatsApp contact options.

## Usage

Once activated, configure the WhatsApp chat button in the **Settings** page under the CodingBunny WhatsApp Chat menu. The button can be customized to fit your site design, and advanced options are available with the PRO version.

## PRO Version

Access advanced features with the PRO version. Click the **Get CodingBunny WhatsApp Chat PRO!** link in the plugins list to learn more.

## Actions & Filters

- **`plugin_action_links_coding-bunny-whatsapp-chat`**: Adds "Settings" and "Get PRO" links to the plugin's row on the WordPress plugins page.
- **`plugins_loaded`**: Loads the text domain for translations.

## Development

For developers interested in customizing or contributing:

1. Clone this repository: `git clone https://github.com/CodingBunny/whatsapp-chat.git`
2. Navigate to the plugin's folder: `cd coding-bunny-whatsapp-chat`
3. Make your changes and submit a pull request. Contributions are welcome!

### File Structure

- `inc/admin-menu.php` - Configures the admin menu for the plugin.
- `inc/licence-validation.php` - Manages PRO license validation.
- `inc/settings-page.php` - Defines the settings page options.
- `inc/display-button.php` - Logic for displaying the WhatsApp chat button.
- `inc/enqueue-scripts.php` - Enqueues necessary CSS and JS files.
- `inc/updates-check.php` - Checks for plugin updates.

## Text Domain & Translations

This plugin is translation-ready, using the text domain `coding-bunny-whatsapp-chat`. Translation files are located in the `/languages` folder.

## License

This plugin is licensed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.html).

## Author

**CodingBunny**  
[Website](https://coding-bunny.com)  
[Support](https://coding-bunny.com/support)

## Changelog

### 1.0.1
Fix - Solved button visibility problem in the free version.
Fix - Solved problem linking settings page returned error.

### 1.0.0
New - Initial release.

---

Thank you for using CodingBunny WhatsApp Chat!
