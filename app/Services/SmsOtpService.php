<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client as TwilioClient;

class SmsOtpService
{
    public function __construct(private TwilioClient $twilio) {}

    public function send(string $phone): void
    {
        $code = (string) random_int(100000, 999999);
        Cache::put("otp:{$phone}", $code, now()->addMinutes(10));

        $this->twilio->messages->create($phone, [
            'from' => config('services.twilio.from'),
            'body' => "Your DISC Report verification code: {$code}",
        ]);
    }

    public function verify(string $phone, string $code): bool
    {
        $stored = Cache::pull("otp:{$phone}");
        return $stored !== null && hash_equals($stored, $code);
    }
}
