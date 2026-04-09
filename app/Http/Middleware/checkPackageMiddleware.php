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
        // Check if staff_id exists in request
        if (!$request->has('staff_id') || empty($request->staff_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Staff ID is required'
            ], 400);
        }

        // Find staff across all tables
        $staffData = $this->findStaffByStaffId($request->staff_id);

        // Check if staff data was found
        if ($staffData === null) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found. Please check your credentials or contact administrator.',
                'staff_id' => $request->staff_id
            ], 404);
        }

        // Check if school_id exists in staff data
        if (!isset($staffData['school_id']) || empty($staffData['school_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'School information not found for this staff member',
                'staff_data' => $staffData
            ], 404);
        }

        try {
            $school = school::findOrFail($staffData['school_id']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'School not found for this staff member',
                'staff_id' => $request->staff_id
            ], 404);
        }

        // Check package
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

        // Check if staff was found (not Unknown type)
        if ($applicant && isset($applicant['staff_type']) && $applicant['staff_type'] !== 'Unknown') {
            return [
                'first_name' => $applicant['first_name'],
                'last_name' => $applicant['last_name'] ?? '',
                'phone' => $applicant['phone'] ?? null,
                'email' => $applicant['email'] ?? null,
                'school_id' => $applicant['school_id'] ?? null,
                'staff_type' => $applicant['staff_type'],
                'staff_id' => $applicant['staff_id'] ?? $staffId,
                'staff_table_id' => $applicant['staff_table_id'] ?? null,
                'user_id' => $applicant['user_id'] ?? null
            ];
        }

        // Staff not found - return null instead of array with 'Unknown'
        return null;
    }
}
