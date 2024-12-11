<?php

namespace App\Http\Controllers;

use App\Mail\AudioUpload;
use App\Models\AudioDescription;
use App\Models\AudioFaq;
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
        $validator = Validator::make($request->all(), [
            'cover' => 'required|file|mimes:jpeg,jpg,png,webp',
            'short_description' => 'required',
            'title' => 'required|unique:audio_guides,title',
            'call_to_action' => 'required',
            'status' => 'required',
            'faqs' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Audio guide couldn\'t create',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            DB::beginTransaction();
            $audio_guide = AudioGuide::create([
                "title" => $request->title,
                "short_description" => $request->short_description,
                "status" => $request->status,
                "price" => $request->price,
                "remark" => "Special",
                "cover" => Storage::disk('public')->put('guides', $request->file('cover')),
                "category_id" => 2,
                "type" => "special",
                "lessons" => 0,
            ]);

            $person = $this->getSheetData($request,'person');
            $object = $this->getSheetData($request,'object');
            $events = $this->getSheetData($request,'event');
            $location = $this->getSheetData($request,'location');

            $this->storeData('person',$person, $audio_guide->id);
            $this->storeData('event',$events, $audio_guide->id);
            $this->storeData('location',$location, $audio_guide->id);
            $this->storeData('object',$object, $audio_guide->id);

            if ($audio_guide !== null && !empty($request->input('description'))) {
                $description = AudioDescription::create([
                    'files' => null,
                    'description' => $request->input('description'),
                    'audio_guide_id' => $audio_guide->id,
                ]);
                if (!empty($request->input('faqs'))) {
                    $faqs = json_decode($request->input('faqs'), true);
                    foreach ($faqs as $items) {
                        AudioFaq::create([
                            'question' => $items['question'],
                            'answer' => $items['answer'],
                            'audio_description_id' => $description->id,
                        ]);
                    }
                }
            }

            $users = User::where('role', '!=','admin')->where('role','!=','business')->get();
            foreach($users as $user){
                Mail::to($user->email)->send(new AudioUpload($request->title,"New Special Audio Guide Uploaded",$request->price));
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Special Guide successfully created'
            ],201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ],400);
        }
    }

    /**
     * Store sheet data
     */
    public function storeData($class, $sheetData,$guideId){
        try {
            DB::beginTransaction();
            $free = null;
            foreach ($sheetData as $key => $items) {
                $data = $items;
                $item = (object) $items;
                $data['audio_guide_id'] = $guideId;
                if(!empty($item->free) && $free === null){
                    $free = $item->free;
                }
                if($guideId){
                    if($class == 'person'){
                        $key = strtolower(str_replace(' ','_',$item->nome_e_cognome));
                        $key = str_replace("'",'_',$key);
                        $data['person_name'] = $key;
                        if($free !== null && $guideId){
                            AudioGuide::where('id',$guideId)->update([
                                'theme' => $item->file_mp3
                            ]);
                        }
                        Person::create($data);
                    }elseif($class == 'object'){
                        $data['person_id'] = $this->getExistData($item->persone,'person')->id ?? Null;
                        $data['person_event_id'] = $this->getExistData($item->eventi,'event')->id ?? Null;
                        $data['person_location_id'] = $this->getExistData($item->luoghi,'location')->id ?? Null;
                        PersonObject::create($data);
                    }elseif($class == 'event'){
                        $key = strtolower(str_replace(' ','_',$item->eventi));
                        $key = str_replace("'",'_',$key);
                        $data['event_name'] = $key;
                        PersonEvent::create($data);
                    }else{
                        $key = strtolower(str_replace(' ','_',$item->luoghi));
                        $data['location_name'] = $key;
                        $key = str_replace("'",'_',$key);
                        PersonLocation::create($data);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            return false;
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

    public function getExistData($key,$table){
        $key = trim(strtolower(str_replace(' ','_',$key)));
        $key = str_replace("'",'_',$key);
        if($table == 'person'){
            $item = Person::where('person_name',$key)->first();
        }elseif($table == 'event'){
            $item = PersonEvent::where('event_name',$key)->first();
        }else{
            $item = PersonLocation::where('location_name',$key)->first();
        }
        return $item;
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
