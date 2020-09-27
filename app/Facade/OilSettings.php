<?php

namespace App\Facade;

use Illuminate\Support\Facades\DB;
use stdClass;

class OilSettings
{
    /**
     * Get Settings WebSite
     *
     * @param null $fields | ex : 'field' | multiple fields with string ex : field1,field2,field3 | multiple fields with array ex : array(field1,field2,field3)
     * @param string $type_result | if object , '' , null return object | if array return array
     * @return array|bool|object
     */
    function get($fields = null, $type_result = 'object')
    {
        if(!is_array($fields) AND str_contains($fields, ','))
        {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        if(!is_array($fields))
            $fields = (array) $fields;

        if(count($fields))
            $settings = DB::table('settings')->whereIn('group', $fields)->get();
        else
            $settings = DB::table('settings')->get();

        if($settings)
        {
            $settings_list_array    = array();
            $settings_list_object   = new stdClass();

            foreach ($settings as $setting)
            {

                if($type_result == 'array')
                {
                    $settings_list_array[$setting->key] = [
                        'id'    => $setting->id,
                        'value' => $setting->value
                    ];
                }
                else
                {
                    $key = $setting->key;
                    $settings_list_object->$key = (object) [
                        'id'    => $setting->id,
                        'value' => $setting->value
                    ];
                }

            }

            if($type_result == 'array')
                return $settings_list_array;
            else
                return $settings_list_object;
        }

        return false;
    }


    function getApiStyle($fields = null) {
        $result =  $this->get($fields);
        $final = [];
        foreach ($result as $key => $value) {
            $final[$key] = $value->value;
        }
        return $final;
    }

}