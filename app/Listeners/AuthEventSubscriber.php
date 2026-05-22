<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class AuthEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function handleUserLogin(Login $event): void
    {
        $user = $event->user;

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'model_name' => get_class($user),
            'object_id' => $user->id,
            'details' => [
                'email' => $user->email,
                'name' => $user->name,
                'msg' => 'User logged in successfully.',
            ],
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout(Logout $event): void
    {
        $user = $event->user;

        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'model_name' => get_class($user),
                'object_id' => $user->id,
                'details' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'msg' => 'User logged out successfully.',
                ],
                'ip_address' => request()->ip(),
            ]);
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
        ];
    }
}
