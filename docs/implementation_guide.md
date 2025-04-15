# Magnati Payment Gateway Implementation for FleetCart

This document provides a comprehensive guide on how the Magnati payment gateway was implemented for FleetCart e-commerce solution.

## Overview

The Magnati payment module for FleetCart follows a modular architecture pattern, consistent with FleetCart's design principles. The implementation allows merchants to accept payments through Magnati's payment gateway with a seamless integration into the FleetCart checkout process.

## Implementation Structure

The module is structured as follows:

```
Modules/Magnati/
├── Admin/                  # Admin panel configuration
├── Config/                 # Configuration files
├── Constants/              # Constants used throughout the module
├── Database/               # Database migrations
│   └── Migrations/
├── Entities/               # Eloquent models
├── Events/                 # Event classes
├── Gateways/               # Payment gateway implementation
├── Http/                   # HTTP layer
│   └── Controllers/
├── Listeners/              # Event listeners
├── Providers/              # Service providers
├── Resources/              # Views and language files
│   ├── lang/
│   └── views/
├── Responses/              # Response handling
├── Routes/                 # Route definitions
└── Services/               # Service classes for API communication
```

## Key Components

### 1. Gateway Class

The `Magnati.php` gateway class implements the `GatewayInterface` and serves as the main entry point for the payment process. It handles:

- Payment initialization
- Redirecting customers to the Magnati payment page
- Processing payment responses

### 2. API Service

The `MagnatiApiService.php` class handles all communication with the Magnati API, including:

- Creating payment sessions through the InitiatePayment endpoint
- Checking payment status through the GetPaymentStatus endpoint
- Verifying callbacks from Magnati

### 3. Database Structure

The module includes a migration to create the `magnati_orders` table, which stores:

- Order references
- Payment status
- Transaction IDs
- Transaction data

### 4. Admin Configuration

The admin interface allows merchants to configure:

- API credentials for both test and production environments:
  - Username and Password
  - Customer, Store, and Terminal values
  - Transaction Hint parameters
- Payment method display settings
- Test mode toggle

### 5. Event Handling

The module implements an event-driven architecture for payment status updates:

- `MagnatiPaymentCompleted` event is fired when a payment is completed
- `HandlePaymentCompleted` listener processes the event and updates order status

## Payment Flow

1. **Initiation**: When a customer selects Magnati as the payment method and submits the order, the `purchase` method in the `Magnati` gateway class is called.

2. **API Request**: The gateway uses the `MagnatiApiService` to create a payment session with Magnati by calling the InitiatePayment endpoint.

3. **Redirect**: The customer is redirected to the Magnati payment page to complete the payment.

4. **Callback**: After payment completion, Magnati redirects the customer back to the store with payment status information including TransactionID, ResponseCode, and ResponseDescription.

5. **Status Check**: The module verifies the payment status by calling the GetPaymentStatus endpoint with the TransactionID.

6. **Order Update**: Based on the payment status (ResponseCode '0' indicates success), the order status is updated accordingly.

## Magnati API Details

The module integrates with the following Magnati API endpoints:

- **InitiatePayment**: `https://demo-ipg.ctdev.comtrust.ae:2443/PaymentEx/MerchantPayment/Payment/InitiatePayment` (Test)
- **GetPaymentStatus**: `https://demo-ipg.ctdev.comtrust.ae:2443/PaymentEx/MerchantPayment/Payment/GetPaymentStatus` (Test)

The production endpoints follow the same pattern but use the production domain.

### Authentication

Authentication is performed using the following parameters:

```json
{
  "Authentication": {
    "UserName": "Demo_fY9c",
    "Password": "Comtrust@20182018"
  }
}
```

### Test Cards

For testing purposes, the following test cards can be used:

| Card Type | Card Number | Result |
|-----------|-------------|--------|
| Visa | 4111111111111111 | Success |
| Mastercard | 5555555555554444 | Success |
| Visa | 4012888888881881 | Not Sufficient Funds |
| Visa | 5105105105105100 | Do Not Honor |

All test cards should use any future expiry date and CVV code 123.

## Security Considerations

The implementation includes several security measures:

- Secure storage of API credentials
- Proper error handling and logging
- Input validation
- Verification of payment status through API

## Testing

The module includes comprehensive tests:

- Unit tests for the gateway class
- Tests for callback handling
- Tests for payment status verification
- Structure verification tests

## Integration with FleetCart

The module integrates with FleetCart through:

- Service provider registration
- Gateway registration with the payment system
- Admin panel integration
- Event listeners for order processing

## Conclusion

This implementation provides a secure and reliable way to accept payments through Magnati in a FleetCart store. The modular design allows for easy maintenance and updates as needed.
