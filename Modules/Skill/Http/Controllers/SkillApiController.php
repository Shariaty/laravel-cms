<?php

namespace Modules\Skill\Http\Controllers;
use Illuminate\Routing\Controller;
use Modules\Skill\Skill;


class SkillApiController extends Controller
{
    public function getAllSkills()
    {
        $skills = Skill::orderBy('sort', 'ASC')->real()->get();
        return response()->json($skills);
    }
}
