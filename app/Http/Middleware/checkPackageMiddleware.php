<?php

namespace App\Http\Middleware;

use App\Models\school;
use App\Traits\ResolveApplicantTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class checkPackageMiddleware
{
    use ResolveApplicantTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Find staff across all tables
        $staffData = $this->findStaffByStaffId($request->staff_id);
        // Log::info('Staff data', ['staff_data' => $staffData]);

        $school = school::findOrFail($staffData['school_id']);

        // Log::info('school data '. $school);
        if ($school->package === 'basic') {
            return response()->json([
                'success' => false,
                'message' => 'Kifurushi cha shule hakitoshelezi kutumia huduma hii',
                'staff_data' => $staffData,
            ]);
        }

        return $next($request);
    }

    private function findStaffByStaffId($staffId)
    {
        // Use trait method directly
        $applicant = $this->resolveApplicantDetails($staffId, null);

        // Check if found
        if ($applicant['staff_type'] !== 'Unknown') {
            return [
                'first_name' => $applicant['first_name'],
                'last_name' => $applicant['last_name'] ?? '',
                'phone' => $applicant['phone'],
                'school_id' => $applicant['school_id'] ?? null,
                'staff_type' => $applicant['staff_type'],
                'staff_id' => $applicant['staff_id']
            ];
        }

        return null;
    }
}
