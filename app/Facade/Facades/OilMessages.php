<?php
namespace App\Facade\Facades;

use Illuminate\Support\Facades\Facade;

class OilMessages extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'OilMessages';
    }
}