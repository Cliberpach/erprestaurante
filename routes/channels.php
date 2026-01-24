<?php

use Illuminate\Support\Facades\Broadcast;
use Spatie\Multitenancy\Models\Tenant;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('alerts.{tenantId}', function ($user, $tenantId) {
    $currentTenant = Tenant::current();
    return $currentTenant && (int) $currentTenant->id === (int) $tenantId;
});
