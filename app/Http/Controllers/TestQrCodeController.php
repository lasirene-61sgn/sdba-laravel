<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestQrCodeController extends Controller
{
    public function test()
    {
        try {
            // Create a simple QR code for testing
            $qrCode = QrCode::size(300)->generate('https://laravel.com');
            
            // Return the QR code as HTML
            return response($qrCode, 200)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function testEventQrCode()
    {
        try {
            // Get the first event from the database or create a dummy one
            $event = Event::first();
            
            if (!$event) {
                // Create a dummy event for testing
                $event = new Event();
                $event->id = 1;
                $event->admin_id = 1;
                $event->name = 'Test Event';
                $event->description = 'This is a test event for QR code generation';
                $event->posted_date = now();
                $event->status = 'active';
            }
            
            // Generate QR code for the event
            $qrCode = $event->qr_code;
            
            // Return the QR code as HTML
            return response($qrCode, 200)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function testEventQrCodeJson()
    {
        try {
            // Get the first event from the database or create a dummy one
            $event = Event::first();
            
            if (!$event) {
                // Create a dummy event for testing
                $event = new Event();
                $event->id = 1;
                $event->admin_id = 1;
                $event->name = 'Test Event';
                $event->description = 'This is a test event for QR code generation';
                $event->posted_date = now();
                $event->status = 'active';
            }
            
            // Generate QR code for the event
            $qrCode = $event->qr_code;
            
            // Return a JSON response with event details and QR code
            return response()->json([
                'status' => 'success',
                'message' => 'Event QR Code generated successfully',
                'event' => [
                    'id' => $event->id,
                    'name' => $event->name,
                    'description' => $event->description,
                    'qr_code_html_length' => strlen($qrCode)
                ],
                'qr_code_html' => (string) $qrCode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function testQrCodeScanning($eventId = 1)
    {
        try {
            // This simulates what happens when a customer scans a QR code
            $url = route('customer.event.qr-attend', ['eventId' => $eventId]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'QR Code scanning simulation',
                'scanning_url' => $url,
                'instructions' => 'When a customer scans the QR code, they will be directed to this URL which will mark their attendance'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}