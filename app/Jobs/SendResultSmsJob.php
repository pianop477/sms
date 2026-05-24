<?php

namespace App\Jobs;

use App\Models\Parents;
use App\Models\User;
use App\Services\NextSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendResultSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $student;
    protected $sender;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($student, $sender, $message)
    {
        $this->student = $student;
        $this->sender = $sender;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $parent = Parents::find($this->student['parent_id']);

            if (!$parent) {
                Log::warning('Parent not found', [
                    'student_id' => $this->student['student_id']
                ]);
                return;
            }

            $user = User::find($parent->user_id);

            if (!$user || empty($user->phone)) {
                Log::warning('Parent phone not found', [
                    'student_id' => $this->student['student_id']
                ]);
                return;
            }

            // Format number
            $phoneNumber = $this->formatPhoneNumber($user->phone);

            if (!$phoneNumber) {
                Log::warning('Invalid phone number', [
                    'student_id' => $this->student['student_id']
                ]);
                return;
            }

            $nextSmsService = new NextSmsService();

            $response = $nextSmsService->sendSmsByNext(
                $this->sender,
                $phoneNumber,
                $this->message,
                $this->student['student_id'] . '_' . uniqid()
            );

            if (!$response || !isset($response['success']) || !$response['success']) {

                Log::error('SMS sending failed', [
                    'student_id' => $this->student['student_id'],
                    'response' => $response
                ]);

                throw new \Exception('SMS API failed or invalid response');
            }

            Log::info('SMS sent successfully', [
                'student_id' => $this->student['student_id'],
                'phone' => $phoneNumber
            ]);

        } catch (\Exception $e) {

            Log::error('Queue SMS Error', [
                'message' => $e->getMessage(),
                'student_id' => $this->student['student_id']
            ]);

            throw $e;
        }
    }

    /**
     * Format phone number
     */
    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (substr($phone, 0, 1) == '0') {
            $phone = '255' . substr($phone, 1);
        }

        if (substr($phone, 0, 3) != '255') {
            return false;
        }

        return $phone;
    }
}
