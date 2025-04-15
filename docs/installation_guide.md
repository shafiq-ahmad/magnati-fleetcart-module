# Installation Guide for Magnati Payment Gateway in FleetCart

This guide provides step-by-step instructions for installing and configuring the Magnati payment gateway module in your FleetCart e-commerce store.

## Prerequisites

Before installing the Magnati payment module, ensure you have:

1. A working FleetCart installation (version 3.0 or higher)
2. Composer installed on your server
3. Magnati merchant account credentials
   - Merchant ID
   - API Key
   - Secret Key

## Installation Steps

### 1. Download the Module

There are two ways to install the module:

#### Option 1: Using Composer (Recommended)

```bash
cd /path/to/your/fleetcart
composer require shafiq-ahmad/magnati-fleetcart-module
```

#### Option 2: Manual Installation

1. Download the module files
2. Extract the files to the `Modules/Magnati` directory in your FleetCart installation

### 2. Register the Module

If you installed the module manually, you need to register it in FleetCart:

1. Open `config/modules.php`
2. Add 'Magnati' to the modules array:

```php
return [
    'modules' => [
        // ... other modules
        'Magnati',
    ],
];
```

### 3. Run Migrations

Run the database migrations to create the necessary tables:

```bash
php artisan module:migrate Magnati
```

### 4. Publish Assets (Optional)

If you need to customize the module's assets, you can publish them:

```bash
php artisan module:publish Magnati
```

### 5. Clear Cache

Clear the application cache to ensure the module is properly loaded:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Configuration

After installation, you need to configure the Magnati payment gateway:

1. Log in to your FleetCart admin panel
2. Navigate to **Settings > Payment Methods**
3. Click on the **Magnati** tab
4. Configure the following settings:

### General Settings

- **Enabled**: Toggle to enable/disable the payment method
- **Label**: The name that will be displayed to customers (e.g., "Pay with Magnati")
- **Description**: A brief description of the payment method
- **Test Mode**: Enable this for testing with Magnati's test environment

### API Credentials

#### Test Environment

- **Test Username**: Your Magnati test username (default: Demo_fY9c)
- **Test Password**: Your Magnati test password (default: Comtrust@20182018)
- **Test Customer**: Your Magnati test customer name (default: Demo Merchant)
- **Test Store**: Your Magnati test store (default: 0000)
- **Test Terminal**: Your Magnati test terminal (default: 0000)
- **Test Transaction Hint**: Your Magnati test transaction hint (default: CPT:Y;VCC:Y)

#### Production Environment

- **Username**: Your Magnati production username
- **Password**: Your Magnati production password
- **Customer**: Your Magnati production customer name
- **Store**: Your Magnati production store
- **Terminal**: Your Magnati production terminal
- **Transaction Hint**: Your Magnati production transaction hint

5. Click **Save** to apply the settings

## Testing the Integration

After configuring the module, you should test the integration:

1. Enable test mode in the module settings
2. Create a test order in your store
3. Select Magnati as the payment method
4. Complete the checkout process
5. Verify that you are redirected to the Magnati payment page
6. Complete the payment using one of the test cards:
   - Visa Success: 4111111111111111
   - Mastercard Success: 5555555555554444
   - Visa Not Sufficient Funds: 4012888888881881
   - Visa Do Not Honor: 5105105105105100
7. Use any future expiry date and CVV code 123
8. Verify that you are redirected back to your store
9. Check that the order status is updated correctly

## Webhook Configuration

For automatic order status updates, you need to configure webhooks in your Magnati merchant dashboard:

1. Log in to your Magnati merchant dashboard
2. Navigate to the webhook settings
3. Add a new webhook with the following URL:
   ```
   https://your-store-url.com/magnati/webhook
   ```
4. Select the events you want to receive notifications for (at minimum: payment.completed, payment.failed)
5. Save the webhook configuration

## Troubleshooting

If you encounter issues with the Magnati payment gateway:

1. **Check Logs**: Review your Laravel logs at `storage/logs/laravel.log`
2. **Verify Credentials**: Ensure your API credentials are correct
3. **Test Mode**: Make sure you're using test credentials when in test mode
4. **Webhook URL**: Verify that your webhook URL is accessible from the internet
5. **SSL Certificate**: Ensure your site has a valid SSL certificate

## Support

If you need assistance with the Magnati payment module:

- Review the [Implementation Guide](./implementation_guide.md) for technical details
- Contact Magnati support for API-related issues
- Submit issues to the module repository for bug reports or feature requests

## Updating the Module

To update the module to the latest version:

```bash
composer update yourvendor/magnati-fleetcart-module
php artisan module:migrate Magnati
php artisan cache:clear
```

## Uninstalling the Module

If you need to remove the module:

1. Disable the payment method in the admin panel
2. Run the following commands:

```bash
php artisan module:disable Magnati
php artisan module:uninstall Magnati
```

This will remove the module and its database tables.
