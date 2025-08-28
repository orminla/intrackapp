<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Set bahasa Carbon ke Indonesia
        Carbon::setLocale('id');

        // Gate untuk admin
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate untuk inspector
        Gate::define('isInspector', function (User $user) {
            return $user->role === 'inspector';
        });

        // Inject data profil ke semua view
        View::composer('*', function ($view) {
            $user = Auth::user();

            if ($user) {
                $photoDefault = asset('login_assets/images/profile/user-7.jpg');
                $photo = $user->photo_url;

                if ($photo && str_starts_with($photo, 'storage/')) {
                    $photo = asset($photo);
                }

                $profile = [
                    'name'       => optional($user->admin ?? $user->inspector)->name ?? '-',
                    'email'      => $user->email ?? '-',
                    'role'       => $user->role,
                    'photo_url'  => $photo ?: $photoDefault,
                    'nip'        => optional($user->admin ?? $user->inspector)->nip ?? '-',
                    'gender'     => optional($user->admin ?? $user->inspector)->gender ?? '-',
                    'phone_num'  => optional($user->admin ?? $user->inspector)->phone_num ?? '-',
                    'portfolio'  => optional(optional($user->admin ?? $user->inspector)->portfolio)->name ?? '-',
                    'department' => optional(optional(optional($user->admin ?? $user->inspector)->portfolio)->department)->name ?? '-',
                ];

                $view->with('profile', $profile);
            }
        });
    }
}
