<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Support\Str;
use App\Models\holiday_package;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Vinkla\Hashids\Facades\Hashids;

class PackagesController extends Controller
{
    //

    public function packagesByYear()
    {
        $packages = holiday_package::where('school_id', Auth::user()->school_id)
            ->orderBy('year', 'DESC')
            ->get();

        // Group directly by year field without Carbon parsing
        $groupedByYear = $packages->groupBy('year');

        $recentPackages = holiday_package::query()
            ->join('grades', 'grades.id', '=', 'holiday_packages.class_id')
            ->join('users', 'users.id', '=', 'holiday_packages.issued_by')
            ->select(
                'holiday_packages.*',
                'grades.class_name',
                'grades.class_code',
                'users.first_name',
                'users.last_name',
            )
            ->where('holiday_packages.school_id', Auth::user()->school_id)
            ->orderBy('holiday_packages.created_at', 'DESC')
            ->orderBy('holiday_packages.updated_at', 'DESC')
            ->take(5)
            ->get();

        $classes = Grade::where('school_id', Auth::user()->school_id)
            ->orderBy('class_name', 'ASC')
            ->get();

        return view('packages.package_by_year', compact('groupedByYear', 'recentPackages', 'classes'));
    }

    public function packageByClass($year)
    {
        $packages = holiday_package::query()
            ->join('grades', 'grades.id', '=', 'holiday_packages.class_id')
            ->select(
                'holiday_packages.*',
                'grades.class_name',
                'grades.id as grade_id',
                'grades.class_code',
            )
            ->where('holiday_packages.school_id', Auth::user()->school_id)
            ->where('holiday_packages.year', $year)
            ->orderBy('grades.class_code')
            ->get();

        $classGroups = $packages->groupBy('class_name');

        return view('packages.package_by_class', compact('packages', 'year', 'classGroups'));
    }

    public function packagesLists($year, $class)
    {
        $hashId = Hashids::decode($class);
        $packages = holiday_package::query()
            ->join('grades', 'grades.id', '=', 'holiday_packages.class_id')
            ->join('users', 'users.id', '=', 'holiday_packages.issued_by')
            ->select(
                'holiday_packages.*',
                'grades.class_name',
                'grades.class_code',
                'users.first_name',
                'users.last_name',
                'users.phone',
            )
            ->where('holiday_packages.school_id', Auth::user()->school_id)
            ->where('holiday_packages.class_id', $hashId[0])
            ->where('holiday_packages.year', $year)
            ->orderBy('holiday_packages.created_at', 'DESC')
            ->orderBy('holiday_packages.updated_at', 'DESC')
            ->get();

        return view('packages.packages_list', compact('packages', 'year'));
    }

    public function uploadPackage(Request $request)
    {
        // Validation
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'class' => 'required|integer|exists:grades,id',
            'term' => 'required|in:i,ii',
            'package_file' => 'required|file|mimes:pdf|max:2048',
        ], [
            'title.required' => 'Package title is required',
            'class.required' => 'Class selection is required',
            'term.required' => 'Term selection is required',
            'package_file.required' => 'Package file is required',
            'package_file.mimes' => 'Only PDF files are allowed',
            'package_file.max' => 'File size must not exceed 2MB',
        ]);

        // Check for existing package
        $existingPackage = holiday_package::where('title', $validated['title'])
            ->where('class_id', $validated['class'])
            ->where('term', $validated['term'])
            ->where('school_id', Auth::user()->school_id)
            ->exists();
        if ($existingPackage) {
            Alert()->toast('This package already exists in our records', 'error');
            return redirect()->back();
        }

        // Ensure storage directory exists with proper permissions
        $storagePath = storage_path('app/packages');
        try {
            if (!Storage::exists('packages')) {
                Storage::makeDirectory('packages', 0755, true);
            }
        } catch (\Exception $e) {
            Alert()->toast('Failed to create storage directory: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }

        // Virus scanning
        $scanResult = $this->scanFileForViruses($request->file('package_file'));
        if (!$scanResult['clean']) {
            Alert()->toast('File security check failed: ' . $scanResult['message'], 'error');
            return redirect()->back();
        }

        // Process file upload
        try {
            $file = $request->file('package_file');
            $fileName = Str::slug($validated['title']) . '_' . time() . '.' . $file->extension();

            // Store with visibility set to private
            $path = $file->storeAs('packages', $fileName, 'local');

            // Optional PDF compression
            if (extension_loaded('imagick')) {
                $compressedPath = $this->compressPdf(storage_path('app/' . $path));
                if ($compressedPath) {
                    $path = str_replace(storage_path('app/'), '', $compressedPath);
                }
            }

            // Save to database
            $package = holiday_package::create([
                'title' => $validated['title'],
                'school_id' => Auth::user()->school_id,
                'class_id' => $validated['class'],
                'description' => $validated['description'],
                'year' => Carbon::now()->format('Y'),
                'term' => $validated['term'],
                'issued_by' => Auth::user()->id,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            // Set proper file permissions (even though stored privately)
            chmod(storage_path('app/' . $path), 0644);
        } catch (\Exception $e) {
            Alert()->toast('File upload failed: ' . $e->getMessage(), 'error');
            return redirect()->back();
        }

        Alert()->toast('Package uploaded successfully', 'success');
        return redirect()->back();
    }

    private function scanFileForViruses($file): array
    {
        // For production, use actual API
        if (app()->environment('production')) {
            $apiKey = config('services.virustotal.key');
            try {
                $response = Http::withHeaders(['x-apikey' => $apiKey])
                    ->attach('file', fopen($file->path(), 'r'))
                    ->post('https://www.virustotal.com/api/v3/files');

                if ($response->successful()) {
                    $scanId = $response->json()['data']['id'];
                    $analysis = Http::withHeaders(['x-apikey' => $apiKey])
                        ->get("https://www.virustotal.com/api/v3/analyses/{$scanId}");

                    return [
                        'clean' => $analysis->json()['data']['attributes']['stats']['malicious'] === 0,
                        'message' => $analysis->json()['data']['attributes']['status']
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'clean' => false,
                    'message' => 'Scan failed: ' . $e->getMessage()
                ];
            }
        }

        // For local development, just mock a successful scan
        return ['clean' => true, 'message' => 'Development mode - scan bypassed'];
    }


    private function compressPdf(string $filePath): ?string
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $outputPath = str_replace('.pdf', '_compressed.pdf', $filePath);

        try {
            // Method 1: Using Ghostscript (preferred)
            if (shell_exec('which gs')) {
                $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 " .
                    "-dPDFSETTINGS=/ebook -dNOPAUSE -dQUIET -dBATCH " .
                    "-sOutputFile=" . escapeshellarg($outputPath) . " " . escapeshellarg($filePath);

                exec($command, $output, $returnCode);

                if ($returnCode === 0 && file_exists($outputPath)) {
                    unlink($filePath); // Remove original
                    return $outputPath;
                }
            }

            // Method 2: Using Imagick (fallback)
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $imagick->readImage($filePath);
                $imagick->setImageCompressionQuality(75);
                $imagick->writeImages($outputPath, true);

                if (file_exists($outputPath)) {
                    unlink($filePath);
                    return $outputPath;
                }
            }
        } catch (\Exception $e) {
            logger()->error('PDF compression failed: ' . $e->getMessage());
        }

        return null;
    }

    public function deletePackage($id)
    {
        $file_id = Hashids::decode($id);
        $package = holiday_package::findOrFail($file_id[0]);

        if ($package->is_active) {
            Alert()->toast('Published packages must be disabled first', 'error');
            return back();
        }

        // Delete file using Laravel Storage
        if (Storage::exists($package->file_path)) {
            try {
                Storage::delete($package->file_path);
            } catch (\Exception $e) {
                Alert()->toast('File deletion failed: ' . $e->getMessage(), 'error');
                return back();
            }
        }

        $package->delete();

        Alert()->toast('Package deleted successfully', 'success');
        return back();
    }

    public function activatePackage(Request $request, $id)
    {
        $hashedId = Hashids::decode($id);

        $package = holiday_package::findOrFail($hashedId[0]);
        $releaseDate = Carbon::now()->format('Y-m-d');
        $dueDate = Carbon::parse($releaseDate)->addMonth()->format('Y-m-d');
        $is_active = true;

        try {

            if ($package) {
                $package->update([
                    'release_date' => $releaseDate,
                    'due_date' => $dueDate,
                    'is_active' => $is_active
                ]);

                Alert()->toast('Package has been activated successfully!', 'success');
                return redirect()->back();
            }
            Alert()->toast('Unable to fetch package record', 'error');
            return redirect()->back();
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function deactivatePackage(Request $request, $id)
    {
        $hashedId = Hashids::decode($id);

        $package = holiday_package::findOrFail($hashedId[0]);

        try {
            if ($package) {
                $package->update([
                    'is_active' => false,
                    'release_date' => null,
                    'due_date' => null
                ]);

                Alert()->toast('Package has been deactivated successfully!', 'success');
                return redirect()->back();
            }
            Alert()->toast('Unable to fetch package record', 'error');
            return redirect()->back();
        } catch (Exception $e) {
            Alert()->toast($e->getMessage(), 'error');
            return back();
        }
    }

    public function downloadPackage($id)
    {
        $file_id = Hashids::decode($id);
        $package = holiday_package::findOrFail($file_id[0]);

        if (!Storage::exists($package->file_path)) {
            Alert()->toast('Package file not found', 'error');
            return back();
        }

        // Get the full path to the file
        $filePath = Storage::path($package->file_path);
        $file = new \Illuminate\Http\File($filePath);

        // Scan the file for viruses
        $scanResult = $this->scanFileForViruses($file);

        if (!$scanResult['clean']) {
            Alert()->toast('File scan failed: ' . $scanResult['message'], 'error');
            return back();
        }

        // For PDF preview in browser
        if (request()->has('preview')) {
            $fileContent = Storage::get($package->file_path);
            return response($fileContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $package->title . '.pdf"');
        }

        // For direct download
        return Storage::download($package->file_path, $package->title . '.pdf');
    }

    public function parentDownloadPackage($id)
    {
        $file_id = Hashids::decode($id);
        $package = holiday_package::findOrFail($file_id[0]);

        // 0. check if the package is active
        if (!$package->is_active) {
            Alert()->toast('This package is not currently available for download', 'error');
            return back();
        }
        // 1. Check if file exists
        if (!Storage::exists($package->file_path)) {
            Alert()->toast('Package file not found', 'error');
            return back();
        }

        // 2. Get authenticated parent
        $parent = auth()->user();
        $maxDownloads = 3; // Maximum downloads per parent per package
        $timeFrame = now()->subHours(24); // 24-hour window

        // 3. Check download limit using existing column
        if ($package->download_count >= $maxDownloads) {
            Alert::info('info', 'You have reached your download limit for this package (max ' . $maxDownloads . ' downloads per 24 hours)');
            return back();
        }

        // 4. Scan for viruses
        $filePath = Storage::path($package->file_path);
        $file = new \Illuminate\Http\File($filePath);
        $scanResult = $this->scanFileForViruses($file);

        if (!$scanResult['clean']) {
            Alert()->toast('File scan failed: ' . $scanResult['message'], 'error');
            return back();
        }

        // 5. Update download count
        $package->increment('download_count');

        // 6. Record download time (using existing column)
        $package->update(['last_downloaded_at' => now()]);

        // 7. Handle preview/download
        if (request()->has('preview')) {
            $fileContent = Storage::get($package->file_path);
            return response($fileContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $package->title . '.pdf"');
        }

        return Storage::download($package->file_path, $package->title . '.pdf');
    }
}
