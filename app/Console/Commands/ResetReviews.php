<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset to 3 the number of reviews a reviewer can do in a year';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $reviewers = User::role(User::REVIEWER_ROLE)->get();
        foreach ($reviewers as $reviewer) {
            if (isset($reviewer->number_of_reviews)) {
                $reviewer->number_of_reviews = env('NUMBER_OF_REVIEWS_PER_YEAR');
                $reviewer->save();
            }
        }
    }
}
