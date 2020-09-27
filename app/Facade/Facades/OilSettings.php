<?php
namespace App\Facade\Facades;

use Illuminate\Support\Facades\Facade;

class OilSettings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'OilSettings';
    }
}