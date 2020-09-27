<?php

namespace Modules\Portal\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Modules\Portal\PortalAlias;

class AliasImport implements ToModel
{
    public function model(array $row)
    {
        if (isset($row[0]) && $row[0]){
            return new PortalAlias([
                'admin_id'           =>  Auth::user() ?  Auth::user()->id : null ,
                'sku'     => $row[0] ? $row[0] : null,
                'portal_id'     => $row[1] ? $row[1] : 'Unknown',
                'is_published'     => 'Y',
                'created_at' => Carbon::now()
            ]);
        }

    }
}