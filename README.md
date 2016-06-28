# Payer Prestashop Module

This is the payment module to get started with Payers payment services in Prestashop.

For more information about our payment services, please visit [www.payer.se](http://www.payer.se).

## Requirements

  * [Prestashop](https://www.prestashop.com): Version 1.6.X
  * [Payer Configuration](https://payer.se) - Missing the configuration file? Contact the [Customer Service](mailto:kundtjanst@payer.se).

## Installation

  1. The `payer__common` folder consists your e.g. your Payer Configuration. Copy it into the `module` directory in your Prestashop installation.
  2. Then copy each payment method module folder to use into the `module` directory in your Prestashop installation.
  3. Click `Install` on the module to use in the `Payment Modules` section in your Prestashop administration.
  4. Turn On/Off the test invironment

## Configuration

You need to have your `PayReadConf` file available. Replace that file with the placeholder in the `payer__common` folder.

## Environment

You can switch between the `test` and `live` environment in the payment method interface through the `Payment Modules` section in Prestashop. 

**NOTICE** Remember to turn off the test environment before you go in production mode.

## Support

For questions regarding your payment module integration, please contact the Payer [Technican Support](mailto:teknik@payer.se). 