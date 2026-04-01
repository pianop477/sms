<?php


namespace App\Traits;

trait formatPhoneTrait
{
    /**
     * Format phone number to international format (e.g. +2547XXXXXXXX)
     */
    public function formatPhoneNumberForSms($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure the number starts with the country code (e.g., 255 for Tanzania)
        if (strlen($phone) == 9) {
            $phone = '255' . $phone;
        } elseif (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        return $phone;
    }
}
