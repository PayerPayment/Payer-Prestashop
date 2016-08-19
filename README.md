![Payer Logotype](http://payer.se/public/PAYER_LOGO_GITHUB_2016.jpg)

# Payer Prestashop Module

This is the payment module to get started with Payers payment services in Prestashop.

For more information about our payment services, please visit [www.payer.se](http://www.payer.se).

## Requirements

  * [Prestashop](https://www.prestashop.com): Version 1.6.X
  * [Payer Credentials](https://payer.se) - Missing credentials? Contact the [Customer Service](mailto:kundtjanst@payer.se).

## Installation

  1. Copy all directories into the `modules` folder in the root of your Prestashop installation.
  2. Setup the configuration by using your Payer Credentials. See the `Configuration` section below for further details
  3. Click `Install` on the module to use in the `Payment Modules` section in your Prestashop administration.
  4. Enable the module in the checkout options and make sure that `test mode` is turned off when in production
  5. You are now live!

## Configuration

Each module has to be configured correctly with your unique Payer Credentials before it can be used in production. The credentials corresponds to the following parameters:

  * `AGENT ID`
  * `KEY 1`
  * `KEY 2`

The key values can be found under the `Settings/Account` section in [Payer Administration](https://secure.payer.se/adminweb/inloggning/inloggning.php).

Setup the module by replacing the placeholders in the `PayReadConf.php` file with these values. The configuration file can be found in the `payer__common` folder in the `modules` folder. And that's it!

## Support

For questions regarding your payment module integration, please contact the Payer [Technican Support](mailto:teknik@payer.se). 