<?php

return [
    'auth_key' => env('MSG91_AUTH_KEY', ''),
    'template_name' => env('MSG91_WHATSAPP_TEMPLATE_ID', 'village'), // Use the name here
    'sender_id' => env('MSG91_SENDER_ID', ''),
    'api_base_url' => env('MSG91_WHATSAPP_API_URL', 'https://api.msg91.com'),
    'integrated_number' => env('MSG91_INTEGRATED_NUMBER', '919360777089'),
    'whatsapp_namespace' => env('MSG91_WHATSAPP_NAMESPACE', 'bc3735fb_a2e9_4e83_8b62_377bca25c09f'),
];