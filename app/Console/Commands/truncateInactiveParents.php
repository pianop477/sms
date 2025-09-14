<?php

namespace App\Console\Commands;

use App\Models\Parents;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;

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

        // Tafuta wazazi ambao hawana mwanafunzi kwa mwaka mmoja
        $parentsToDelete = Parents::whereDoesntHave('students') // relation ya Parent->students
                        ->where('updated_at', '<=', $oneYearAgo)
                        ->get();

        $deletedCount = 0;

        foreach ($parentsToDelete as $parent) {
            // Futa kwanza user account
            if ($parent->user_id) {
                User::where('id', $parent->user_id)->delete();
            }

            // Halafu futa parent mwenyewe
            $parent->delete();

            $deletedCount++;
        }

        $this->info("Cleanup done. {$deletedCount} parents deleted.");
    }

}
