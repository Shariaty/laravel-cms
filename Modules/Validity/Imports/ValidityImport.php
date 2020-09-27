<?php

namespace Modules\Validity\Imports;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Modules\Validity\Validity;

class ValidityImport implements ToModel
{
    public function model(array $row)
    {
        if (isset($row[0]) && $row[0]){
            return new Validity([
                'admin_id'           =>  Auth::user() ?  Auth::user()->id : null ,
                'identification'     => $row[0] ? $row[0] : null,
                'title'     => $row[1] ? $row[1] : 'Unknown',
                'date'     => $row[2] ? $row[2] : null
            ]);
        }

    }
}
