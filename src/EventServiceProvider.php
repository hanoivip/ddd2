<?php

namespace Hanoivip\Ddd2;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'Hanoivip\Events\User\PassUpdated' => [
            'Hanoivip\Ddd2\Services\IdpAuthen'
        ]
    ];
    
    public function boot()
    {
        parent::boot();
    }
}