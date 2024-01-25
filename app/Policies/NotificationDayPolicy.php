<?php

namespace App\Policies;

use App\Models\Common\NotificationDay;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotificationDayPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NotificationDay $notificationDay): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NotificationDay $notificationDay): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NotificationDay $notificationDay): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, NotificationDay $notificationDay): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, NotificationDay $notificationDay): bool
    {
        //
    }
}
