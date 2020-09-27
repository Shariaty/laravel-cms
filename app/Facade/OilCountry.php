<?php

namespace App\Facade;

use Illuminate\Support\Facades\DB;

class OilCountry
{

    public function get($ShortName = null , $user_id = null)
    {
        if(isset($ShortName))
        {
            $countries      = DB::table('countries')->select('id', 'name')->whereSortname($ShortName)->first();
            $_states        = DB::table('states')->select('id', 'name')->whereCountryId($countries->id)->get();

            $citites = array();
            $states_ids = array();
            if(isset($_states) AND count($_states))
            {
                foreach ($_states as $state)
                    $states_ids[] = $state->id;

                $citites[] = DB::table('cities')->select('id', 'name')->whereIn('state_id', $states_ids)->whereUserId(0)->orderBy('name', 'ASC')->get();

                if ($user_id)
                    $citites[] = DB::table('cities')->select('id', 'name')->where('user_id', $user_id)->get();
            }


            $states_id = array();
            $states_name = array();
            $states_select2 = array();
            if(isset($_states) AND count($_states))
                foreach ($_states as $state)
                {
                    $states_id[$state->id] = $state->name;
                    $states_name[$state->name] = $state->name;
                    $states_select2[] = [
                        'id'    =>  $state->name,
                        'text'  =>  $state->name
                    ];
                }





            $citites_id = array();
            $citites_name = array();
            $citites_select2 = array();
            if(isset($citites) AND count($citites))
                foreach ($citites as $citite)
                    foreach ($citite as $ci)
                    {
                        $citites_id[$ci->id] = $ci->name;
                        $citites_name[$ci->name] = $ci->name;
                        $citites_select2[$ci->id] = [
                            'id'    =>  $ci->name,
                            'text'  =>  $ci->name
                        ];
                    }




            asort($citites_name);
            asort($citites_select2);

            return [
                'countries'                     =>  $countries,
                'states'                        =>  $_states,
                'cities'                        =>  $citites,
                'states_id'                     =>  $states_id,
                'cities_id'                     =>  $citites_id,
                'states_name'                   =>  $states_name,
                'cities_name'                   =>  $citites_name,
                'cities_name_empty_first'       =>  $citites_name, //array_merge( ['' => ''] , $citites_name),
                'states_select2'                =>  $states_select2,
                'cities_select2'                =>  $citites_select2,
                'cities_select2_empty_first'    =>  array_merge( [['id' => '' , 'text' =>  '']] , $citites_select2)
            ];
        }

        return null;
    }

    public function getAllCountries()
    {
        return DB::table('countries')->select('sortname', 'name')->get()->pluck('name', 'sortname');
    }

    public function getNameCountry($ShortName)
    {
        $country = DB::table('countries')->select('id', 'name')->whereSortname($ShortName)->first();
        return (isset($country->name)) ? $country->name : null;
    }
}