<?php
namespace App\Facade\Facades;

use Illuminate\Support\Facades\Facade;

class OilCountry extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'OilCountry';
    }
}