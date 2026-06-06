# MSG91 WhatsApp OTP Setup Guide

This guide explains how to set up and use MSG91's WhatsApp template API for sending OTPs in your Laravel application.

## Configuration

Add the following environment variables to your `.env` file:

```env
MSG91_AUTH_KEY=your_msg91_auth_key
MSG91_INTEGRATED_NUMBER=919360777089
MSG91_WHATSAPP_NAMESPACE=bc3735fb_a2e9_4e83_8b62_377bca25c09f
MSG91_WHATSAPP_API_URL=https://api.msg91.com
MSG91_WHATSAPP_BUTTON_URL=https://example.com
```

## WhatsApp Template Setup

1. Log in to your MSG91 dashboard
2. Navigate to WhatsApp > Templates
3. Create a new template with the name `logintest`
4. The template should have:
   - A body parameter named `body_1` (for the OTP)
   - A button parameter named `button_1` (for the URL, optional)

## Testing

### Using Artisan Command

```bash
php artisan test:whatsapp-otp
```

### Using Test Script

```bash
php test_whatsapp_otp_template.php
```

## API Integration

The WhatsApp OTP service is integrated with:
- Customer login flow (both web and API)
- Customer forgot password flow (both web and API)

## Template Structure

The current implementation uses the following template structure:

```json
{
  "integrated_number": "919360777089",
  "content_type": "template",
  "payload": {
    "messaging_product": "whatsapp",
    "type": "template",
    "template": {
      "name": "logintest",
      "language": {
        "code": "en",
        "policy": "deterministic"
      },
      "namespace": "bc3735fb_a2e9_4e83_8b62_377bca25c09f",
      "to_and_components": [
        {
          "to": ["<mobile_number>"],
          "components": {
            "body_1": {
              "type": "text",
              "value": "<otp_value>"
            },
            "button_1": {
              "subtype": "url",
              "type": "text",
              "value": "https://example.com"
            }
          }
        }
      ]
    }
  }
}
```

## Troubleshooting

1. If you get authentication errors, verify your `MSG91_AUTH_KEY`
2. If you get template errors, ensure your template name matches exactly (`logintest`)
3. If you get number format errors, ensure the mobile number is in international format (e.g., 91XXXXXXXXXX)
4. Check that your MSG91 account is enabled for WhatsApp Business API