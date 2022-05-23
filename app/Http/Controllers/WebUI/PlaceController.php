<?php

namespace App\Http\Controllers\WebUI;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\PlacePhotos;
use App\Models\PlaceType;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $places = Place::latest()->paginate(10);
        return view('place.index')->with('places', $places);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $place_types = PlaceType::all();
        return view('place.create')->with('place_types', $place_types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|regex:/^[\pL\s\-]+$/u',
            'address' => 'required|string',
            'type_id' => 'required|int',
            'summary' => 'required',
            'description' => 'required',
            'photos' => 'required',
        ]);
        //dd($request['photos'][0]);
        $place = new Place();
        $place->name = $request['name'];
        $place->address = $request['address'];
        $place->type_id = $request['type_id'];
        $place->summary = $request['summary'];
        $place->description = $request['description'];
        $place->save();

        $this->storeImages($place, $request['photos']);

        return redirect()->route('place.index')
            ->with('success','Place info stored successfully');
    }

    public function storeImages($place, $photos)
    {
        /***    php artisan storage:link     ***/





        $place_images = [];
        for ($i = 0; $i < count($photos); $i++) {

              Storage::putFile(
                    'places/',$photos[$i]) ;

            $path = Storage::url('places/'.$photos[$i]);

//            $img_data = $photos[$i];
//            $image = base64_decode($img_data);
//            $file = finfo_open();
//            $result = finfo_buffer($file, $image, FILEINFO_MIME_TYPE);
//
//            $filename = uniqid();
//            $extension = str_replace('image/', '.', $result);
//
//            $path =  Storage::put('public/places/' . $filename . $extension, $image);
//
//            $path = Storage::url('public/places/'.$photos[$i]);
           // dd($photos);
            dd($path);

            $place_images[$i] = PlacePhotos::create([
                'place_id' => $place->id,
                'path' => $path
            ]);
        }
        return $place_images;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function show($id)
    {
        $place = Place::find($id);
        $photos = $place->photos;
        return view('place.show')
            ->with('place', $place)
            ->with('photos', $photos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $place = Place::find($id);
        if($place == null){
            return view('place.index');
        }
        $place_types = PlaceType::all();

        return view('place.edit')
            ->with('place', $place)
            ->with('place_types',$place_types);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Application|Factory|View
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|regex:/^[\pL\s\-]+$/u',
            'address' => 'required',
            'type_id' => 'required|int',
            'summary' => 'required',
            'description' => 'required',
            'photos' => 'required',
        ]);
        //dd($request['photos'][0]);
        $place = Place::find($id);
        if($place != null){
            $place->name = $request['name'];
            $place->address = $request['address'];
            $place->type_id = $request['type_id'];
            $place->summary = $request['summary'];
            $place->description = $request['description'];
            $place->save();

            $this->storeImages($place, $request['photos']);

            return redirect()->route('place.index')
                ->with('success','Place info updated successfully');
        }
        else
            return view('place.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $place = Place::find($id)->delete();
        return redirect()->back();
    }
}
