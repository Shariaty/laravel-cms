<?php

namespace App\Http\Controllers\CountryCities;

use App\City;
use App\Country;
use App\Http\Controllers\Controller;

use App\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Stores\Store;
use Modules\Stores\StoreType;

class CountryCitiesController extends Controller
{

    public function getStates()
    {
        $states = State::all();
        $finalStates = [];

        foreach ($states as $state) {
            $finalStates[] = (object)[ 'label' =>  $state->p_name , 'value' => $state->id ];
        }

        $categories = StoreType::all();
        $finalCategories = [];

        foreach ($categories as $category) {
            $finalCategories[] = (object)[ 'label' =>  $category->p_title , 'value' => $category->id ];
        }

        return response()->json(['states' => $finalStates , 'categories' => $finalCategories]);
    }
    // -----------------------------------------------------------------------------------
    public function getCities(Request $request)
    {
        $finalCities = [];
        $finalCategories = [];

        $cities = City::all();
        $categories = StoreType::all();

        if ($cities) {
            if ($request->has('type')) {
                $type = $request->input('type');
                if ($type == 'select2') {
                    foreach ($cities as $city) {
                        $finalCities[] = (object)[ 'text' =>  $city->p_name , 'id' => $city->id , 'position' => $city->position];
                    }
                }
            } else {
                foreach ($cities as $city) {
                    $finalCities[] = (object)[ 'label' =>  $city->p_name , 'value' => $city->id , 'position' => $city->position];
                }
            }
        }


        foreach ($categories as $category) {
            $finalCategories[] = (object)[ 'label' =>  $category->p_title , 'value' => $category->id ];
        }

        return response()->json(['cities' => $finalCities , 'categories' => $finalCategories]);
    }
    // -----------------------------------------------------------------------------------
    public function getDistricts(Request $request)
    {
        $finalCities = [];

        $stateId = $request->input('cityId');

        $districts = Store::where('city_id' , $stateId)->get();

        if ($districts) {
            if ($request->has('type')) {
                $type = $request->input('type');
                if ($type == 'select2') {
                    foreach ($districts as $district) {
                        if ($district->district) {
                            $finalCities[] = (object)[ 'text' =>  $district->district , 'id' => $district->district ];
                        }
                    }
                }
            } else {
                foreach ($districts as $district) {
                    if ($district->district) {
                        $finalCities[] = (object)['label' => $district->district, 'value' => $district->district];
                    }
                }
            }
        }
        return response()->json(['districts' => $finalCities]);
    }

    // -----------------------------------------------------------------------------------
    public function stateIndex()
    {
        $states = State::withCount('cities')->paginate(20);
        return view('admin.countryCities.stateList' , compact('states'))->with('title' , 'States List');
    }
    // -----------------------------------------------------------------------------------
    public function stateAdd()
    {
        return view('admin.countryCities.stateAdd')->with('title' , 'Add State');
    }
    // -----------------------------------------------------------------------------------
    public function stateCreate(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|max:100',
            'p_name' => 'required|max:100'
        ])->validate();

        $data = $request->except(['_token']);
        State::create($data);

        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.state.list'));
    }
    // -----------------------------------------------------------------------------------
    public function stateDelete(State $state)
    {
        $state->delete();
        return redirect(route('admin.state.list'))->with('success' , trans('notify.DELETE_SUCCESS_NOTIFICATION'));

    }
    // -----------------------------------------------------------------------------------
    public function stateEdit(State $state)
    {
        return view('admin.countryCities.stateEdit' , compact('state'))->with('title' , 'Edit State :'.$state->name);
    }
    // -----------------------------------------------------------------------------------
    public function stateUpdate(State $state , Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|max:100',
            'p_name' => 'required|max:100'
        ])->validate();

        $data = $request->except(['_token']);
        $state->update($data);
        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.state.list'));
    }
    // -----------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------
    // -----------------------------------------------------------------------------------
    public function citiesIndex(State $state)
    {
        $cities = $state->cities()->paginate(20);
        return view('admin.countryCities.cityList' , compact('cities' , 'state'))->with('title' , 'Cities List of '.$state->name);
    }
    // -----------------------------------------------------------------------------------
    public function cityAdd(State $state)
    {
        return view('admin.countryCities.cityAdd' , compact('state'))->with('title' , 'Add City');
    }
    // -----------------------------------------------------------------------------------
    public function cityCreate(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|max:100',
            'p_name' => 'required|max:100',
            'state_id' => 'required'
        ])->validate();

        $state = State::whereId($request->input('state_id'))->first();

        if($state) {
            $lat = $request->input('lat');
            $lng = $request->input('lng');
            $zoom = $request->input('zoom');

            $position = [
                'centerLocation' => [ 'lat' => floatval($lat) , 'lng' => floatval($lng)] ,
                'zoomLevel' => intval($zoom)
            ];

            $request->merge(['position' => json_encode($position) ]);
            $data = $request->except(['_token' , 'lat' , 'lng' , 'zoom']);

            $state->cities()->create($data);
            $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
            return redirect(route('admin.city.list' , $request->input('state_id')));
        }

        $request->session()->flash('Success', trans('notify.CREATE_FAILED_NOTIFICATION'));
        return redirect(route('admin.city.list' , $request->input('state_id')));
    }
    // -----------------------------------------------------------------------------------
    public function cityDelete(City $city)
    {
        $city->delete();
        return redirect(route('admin.city.list' , $city->state))->with('success' , trans('notify.DELETE_SUCCESS_NOTIFICATION'));
    }
    // -----------------------------------------------------------------------------------
    public function cityEdit(City $city)
    {
        $position = json_decode($city->position);
        return view('admin.countryCities.cityEdit' , compact('city' , 'position'))->with('title' , 'Edit City :'.$city->name);
    }
    // -----------------------------------------------------------------------------------
    public function cityUpdate(City $city , Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|max:100',
            'p_name' => 'required|max:100'
        ])->validate();

        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $zoom = $request->input('zoom');

        $position = [
            'centerLocation' => [ 'lat' => floatval($lat) , 'lng' => floatval($lng)] ,
            'zoomLevel' => intval($zoom)
        ];

        $request->merge(['position' => json_encode($position) ]);
        $data = $request->except(['_token' , 'lat' , 'lng' , 'zoom']);

        $city->update($data);
        $request->session()->flash('Success', trans('notify.CREATE_SUCCESS_NOTIFICATION'));
        return redirect(route('admin.city.list' , $city->state->id ));

    }
}
