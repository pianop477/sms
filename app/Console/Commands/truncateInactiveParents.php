<?php

namespace App\Console\Commands;

use App\Models\Parents;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class truncateInactiveParents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parents:truncate-inactive-parents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oneYearAgo = now()->subYear();

        $parents = Parents::whereDoesntHave('students')
            ->where('updated_at', '<=', $oneYearAgo)
            ->with(['user:id,image'])
            ->chunkById(200, function ($parentsChunk) {

                DB::transaction(function () use ($parentsChunk) {

                    foreach ($parentsChunk as $parent) {

                        $user = $parent->user;

                        if ($user && $user->image) {

                            $path = 'profile/' . $user->image;

                            if (Storage::disk('public')->exists($path)) {
                                Storage::disk('public')->delete($path);
                            }
                        }

                        $parent->delete();

                        if ($user) {
                            $user->delete();
                        }
                    }
                });

            });

        $this->info('Parent cleanup completed successfully.');
    }
}
