<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\ForumTopic;
use App\Models\Submission;
use App\Policies\CoursePolicy;
use App\Policies\ForumTopicPolicy;
use App\Policies\SubmissionPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(ForumTopic::class, ForumTopicPolicy::class);
        Paginator::useTailwind();

        // Rate limiter para login: máximo 10 intentos en 5 minutos por email+IP (RNF-SEG-08)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(5, 10)
                ->by($request->input('email') . '|' . $request->ip());
        });
    }
}
