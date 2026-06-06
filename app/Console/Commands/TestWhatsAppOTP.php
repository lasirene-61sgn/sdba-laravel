<?php

namespace App\Console\Commands;

use App\Services\WhatsAppOTPService;
use Illuminate\Console\Command;

class TestWhatsAppOTP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:whatsapp-otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WhatsApp OTP functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing WhatsApp OTP Service...');

        $whatsappOTPService = new WhatsAppOTPService();

        // Test OTP generation
        $otp = $whatsappOTPService->generateOTP();
        $this->info('Generated OTP: ' . $otp);

        // Validate that it's a 6-digit number
        if (is_numeric($otp) && strlen($otp) == 6) {
            $this->info('✓ OTP format is correct (6 digits)');
        } else {
            $this->error('✗ OTP format is incorrect');
        }

        // Test mobile number formatting
        $testNumbers = [
            '9876543210',
            '09876543210',
            '919876543210',
            '+919876543210',
            '91-9876543210'
        ];

        foreach ($testNumbers as $number) {
            $formatted = $whatsappOTPService->formatMobileNumber($number);
            $this->line("Original: $number -> Formatted: $formatted");
        }

        $this->info('\nWhatsApp OTP service test completed.');
        
        if (empty(config('msg91.auth_key'))) {
            $this->warn('MSG91 configuration is not set. Please add MSG91 credentials to your .env file:');
            $this->line('- MSG91_AUTH_KEY=your_auth_key');
            $this->line('- MSG91_INTEGRATED_NUMBER=your_integrated_number');
            $this->line('- MSG91_WHATSAPP_NAMESPACE=your_namespace');
        } else {
            $this->info('MSG91 configuration is set. Testing actual OTP sending...');
            
            // Test sending OTP to a mobile number (replace with actual test number)
            $testMobile = '919361590913'; // You should replace this with a test number
            $this->info("Sending OTP to: $testMobile");
            
            $result = $whatsappOTPService->sendOTP($testMobile, $otp);
            
            if ($result['success']) {
                $this->info('✓ OTP sent successfully via WhatsApp!');
                $this->info('Response: ' . json_encode($result['data']));
            } else {
                $this->error('✗ Failed to send OTP: ' . $result['message']);
                if (isset($result['error'])) {
                    $this->error('Error details: ' . $result['error']);
                }
            }
        }
    }
}
