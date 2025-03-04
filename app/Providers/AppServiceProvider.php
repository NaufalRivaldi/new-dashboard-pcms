<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\Education;
use App\Models\ImportedActiveStudent;
use App\Models\ImportedActiveStudentEducation;
use App\Models\ImportedFee;
use App\Models\ImportedInactiveStudent;
use App\Models\ImportedLeaveStudent;
use App\Models\ImportedNewStudent;
use App\Models\Lesson;
use App\Models\Region;
use App\Models\Summary;
use App\Models\User;
use App\Policies\BranchPolicy;
use App\Policies\EducationPolicy;
use App\Policies\ImportedActiveStudentEducationPolicy;
use App\Policies\ImportedActiveStudentPolicy;
use App\Policies\ImportedFeePolicy;
use App\Policies\ImportedInactiveStudentPolicy;
use App\Policies\ImportedLeaveStudentPolicy;
use App\Policies\ImportedNewStudentPolicy;
use App\Policies\LessonPolicy;
use App\Policies\RegionPolicy;
use App\Policies\SummaryPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            return $user->isSuperAdmin() ? true: null;
        });

        Gate::policy(Branch::class, BranchPolicy::class);
        Gate::policy(Education::class, EducationPolicy::class);
        Gate::policy(ImportedActiveStudent::class, ImportedActiveStudentPolicy::class);
        Gate::policy(ImportedActiveStudentEducation::class, ImportedActiveStudentEducationPolicy::class);
        Gate::policy(ImportedFee::class, ImportedFeePolicy::class);
        Gate::policy(ImportedInactiveStudent::class, ImportedInactiveStudentPolicy::class);
        Gate::policy(ImportedLeaveStudent::class, ImportedLeaveStudentPolicy::class);
        Gate::policy(ImportedNewStudent::class, ImportedNewStudentPolicy::class);
        Gate::policy(Lesson::class, LessonPolicy::class);
        Gate::policy(Region::class, RegionPolicy::class);
        Gate::policy(Summary::class, SummaryPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
