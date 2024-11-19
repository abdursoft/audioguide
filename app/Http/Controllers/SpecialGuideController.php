<?php

namespace App\Http\Controllers;

use App\Mail\AudioUpload;
use App\Models\AudioGuide;
use App\Models\Category;
use App\Models\Person;
use App\Models\PersonEvent;
use App\Models\PersonLocation;
use App\Models\PersonObject;
use App\Models\SpecialGuide;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SpecialGuideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(empty($request->hasFile('person'))){
            return response()->json([
                'status' => false,
                'message' => 'Person guide is required'
            ],400);
        }

        try {
            $person = $this->getSheetData($request,'person');
            $object = $this->getSheetData($request,'object');
            $events = $this->getSheetData($request,'event');
            $location = $this->getSheetData($request,'location');

            $pp = $this->storeData('person',$request->person_title,$person);
            $po = $this->storeData('object',$request->object_title,$object);
            $pe = $this->storeData('event',$request->event_title,$events);
            $pl = $this->storeData('location',$request->location_title,$location);

            SpecialGuide::create([
                "person_id" => $pp->id,
                "person_event_id" => $pe->id,
                "person_object_id" => $po->id,
                "person_location_id" => $pl->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Guide successfully created'
            ],201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Store sheet data
     */
    public function storeData($class,$title, $sheetData){
        try {
            DB::beginTransaction();
            $audio_guide = null;
            $price = 0;
            $free = null;
            $cat_id = 1;
            foreach ($sheetData as $key => $items) {
                $data = $items;
                $item = (object) $items;
                if ($key === 0) {
                    $price = $item->total_price ?? 0;
                    if (!empty($item->categoria)) {
                        $new_category = str_replace(' ', '_', strtolower($item->categoria));
                        $new_category = str_replace('/', '_', $new_category);
                        $exist = Category::where('category', $new_category)->first();

                        if ($exist) {
                            $cat_id = $exist->id;
                        } else {
                            $category = Category::create([
                                'category' => strtolower($new_category),
                                'name' => $item->categoria,
                            ]);
                            $cat_id = $category->id;
                        }
                    }

                    $audio_guide = AudioGuide::create([
                        "title" => $title,
                        "status" => 'active',
                        "price" => $price,
                        // "cover" => Storage::disk('public')->put('guides', $request->file('cover')),
                        "category_id" => $cat_id,
                        "type" => "special",
                        "lessons" => count($sheetData),
                    ]);
                }
                $data['audio_guide_id'] = $audio_guide->id;
                if($audio_guide){
                    if($class == 'person'){
                        Person::create($data);
                    }elseif($class == 'object'){
                        PersonObject::create($data);
                    }elseif($class == 'event'){
                        PersonEvent::create($data);
                    }else{
                        PersonLocation::create($data);
                    }
                }
                if(!empty($item->free) && $free === null){
                    $free = $item->free;
                }
            }

            if($free !== null && $audio_guide !== null){
                AudioGuide::where('id',$audio_guide->id)->update([
                    'theme' => $free
                ]);
            }

            DB::commit();

            $users = User::where('role', '!=','admin')->get();
            foreach($users as $user){
                Mail::to($user->email)->send(new AudioUpload($title,"New Special Audio Guide Uploaded",$price));
            }
            return $audio_guide;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SpecialGuide $specialGuide)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpecialGuide $specialGuide)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpecialGuide $specialGuide)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpecialGuide $specialGuide)
    {
        //
    }

    public function getSheetData(Request $request,$fileName){
        if ($request->hasFile($fileName)) {
            $file = $request->file($fileName);
            $extension = $file->getClientOriginalExtension();
            $file_name = $file->getClientOriginalName();
            $file_name = explode('.', $file_name);

            if ('csv' == $extension) {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'File type must be CSV',
                ], 400);
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setReadDataOnly(true);
            }
            $reader->setInputEncoding('UTF-8');
            $reader->setDelimiter(',');
            $reader->setEnclosure('');
            $reader->setSheetIndex(0);
            $spreadsheet = $reader->load($file);

            $data = $spreadsheet->getActiveSheet()->toArray();
            $keys = $data[0];
            $sheetData = [];

            for ($i = 1; $i < count($data); $i++) {
                $sheetData[] = $this->combineData($data[$i], $keys);
            }

            return $sheetData;
        }else{
            return false;
        }
    }


    /**
     * Sanitize header data
     */
    public function sanitizeTitle($data)
    {
        $titles = [];

        foreach ($data as $item) {
            $item = strtolower($item);
            $item = str_replace(' ', '_', $item);
            $item = str_replace('/', '_', $item);
            $titles[] = $item;
        }
        return $titles;
    }

    /**
     * Combine the data
     */
    public function combineData($row, $keys)
    {
        $row = $row;
        $newKeys = $this->sanitizeTitle($keys);
        if (count($newKeys) > count($row)) {
            $newKeys = array_slice($newKeys, 0, count($row));
        }
        if (count($newKeys) < count($row)) {
            $row = array_slice($row, 0, count($newKeys));
        }
        $filter = [];
        foreach ($newKeys as $index => $key) {
            if ($row[$index] !== null) {
                $filter[$key] = $row[$index];
            }
        }
        return $filter;
    }
}
