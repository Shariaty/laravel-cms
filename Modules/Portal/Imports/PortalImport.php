<?php

namespace Modules\Portal\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Modules\Portal\PortalTempRecord;

class PortalImport implements ToModel
{

    protected $portal_id;

    /**
     * PortalImport constructor.
     * @param null $portal_id
     */
    public function __construct($portal_id = null)
    {
        $this->portal_id = $portal_id;
    }

    public function model(array $row)
    {
        if (isset($row[2]) && $row[2] && ctype_digit($row[2])){
            return new PortalTempRecord([
                'admin_id'           =>  Auth::user() ?  Auth::user()->id : null ,
                'sku'     => $row[2] ? $row[2] : null,
                'eachStock'     => $row[12] ? $row[12] : 0,
                'portal_id'     => $this->portal_id,
                'created_at' => Carbon::now()
            ]);
        }

    }
}