<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helper\Helper;
use App\Models\AudioContent;
use App\Models\AudioDescription;
use App\Models\AudioFaq;
use App\Models\AudioGuide;
use App\Models\Category;
use App\Models\InvoiceProduct;
use App\Models\ProductWish;
use App\Models\Update;
use App\Models\User;
use App\Models\UserGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AudioGuideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'Audio guide successfully retrieved',
            'data' => AudioGuide::all(),
        ], 200);
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
        if ($request->input('data_type') === 'form') {
            $validator = Validator::make($request->all(), [
                'category' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file',
                'cover' => 'required|file|mimes:jpeg,jpg,png,webp',
                'description' => 'required',
                'title' => 'required|unique:audio_guides,title',
                'call_to_action' => 'required',
                'remark' => 'required',
                'status' => 'required',
                'theme' => 'required',
                'price' => 'required',
                'faqs' => 'required',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Audio guide couldn\'t create',
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
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
            // return response()->json($sheetData);

            try {
                DB::beginTransaction();
                $audio_guide = null;
                $price = null;
                $free = false;
                foreach ($sheetData as $key => $item) {
                    if ($key === 0) {
                        $price = $item->total_price ?? null;
                        $category = 1;
                        if (!empty($item->categoria)) {
                            $new_category = str_replace(' ', '_', strtolower($$item->categoria));
                            $new_category = str_replace('/', '_', $new_category);
                            $exist = Category::where('category', $new_category)->first();

                            if ($exist) {
                                return $exist->id;
                            } else {
                                $category = Category::create([
                                    'category' => strtolower($new_category),
                                    'name' => $category,
                                ]);
                            }
                        }

                        if(!empty($item->free) && !$free){
                            $free = $item->file_mp3;
                        }
                        $audio_guide = AudioGuide::create([
                            "title" => $request->input('title') ?? $file_name[0],
                            "status" => $request->input('status'),
                            "price" => $price ?? $request->input('price'),
                            "short_description" => $request->input('short_description') ?? null,
                            "cover" => Storage::disk('public')->put('guides', $request->file('cover')),
                            "category_id" => $category,
                            "remark" => $request->input('remark') ?? null,
                            "call_to_action" => $request->input('call_to_action'),
                            "theme" => $request->input('theme') ?? null,
                            "duration" => $request->input('duration') ?? '0',
                            "lessons" => count($sheetData),
                        ]);
                    }
                    $item['audio_guide_id'] = $audio_guide->id;
                    AudioContent::create($item);
                }
                if ($audio_guide !== null && !empty($request->input('description'))) {
                    $description = AudioDescription::create([
                        'files' => null,
                        'description' => $request->input('description'),
                        'audio_guide_id' => $audio_guide->id,
                    ]);
                    if (!empty($request->input('questions'))) {
                        $questions = $request->input('questions');
                        $answers = $request->input('answers');

                        foreach ($questions as $key => $question) {
                            AudioFaq::create([
                                'question' => $question,
                                'answer' => $answers[$key],
                                'audio_description_id' => $description->id,
                            ]);
                        }
                    }
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
                if($free !== false && $audio_guide !== null){
                    AudioGuide::where('id',$audio_guide->id)->update([
                        'theme' => $free
                    ]);
                }
                if($audio_guide !== null){
                    Update::create([
                        'image' => Storage::disk('public')->put('guides', $request->file('cover')),
                        'title' => $request->input('title'),
                        'sub_title' => $request->input('short_description')
                    ]);
                }
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Audio guide successfully created',
                ], 200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage(),
                ], 400);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => "Please select a CSV | XLSX file",
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AudioGuide $audioGuide)
    {
        return response()->json([
            'status' => true,
            'message' => 'Audio guide successfully retrieved',
            'data' => $audioGuide->load(['Category', 'AudioDescription', 'AudioDescription.AudioFaq', 'UserGuide','AudioContent']),
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AudioGuide $audioGuide)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AudioGuide $audioGuide, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cover' => 'file|mimes:jpeg,jpg,png,webp',
            'description' => 'required',
            'title' => "required|exists:audio_guides,title,$audioGuide->id,id",
            'call_to_action' => 'required',
            'remark' => 'required',
            'status' => 'required',
            'theme' => 'required',
            'price' => 'required',
            'faqs' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Audio guide couldn\'t update',
                'errors' => $validator->errors(),
            ]);
        }

        try {
            $cover = $audioGuide->cover;
            if ($request->hasFile('cover')) {
                $cover = Storage::disk('public')->put('audio-guide', $request->file('cover'));
            }
            DB::beginTransaction();
            $audioGuide->update([
                "title" => $request->input('title'),
                "status" => $request->input('status'),
                "price" => $request->input('price'),
                "short_description" => $request->input('short_description') ?? $audioGuide->short_description,
                "cover" => $cover,
                "category_id" => $audioGuide->category_id,
                "remark" => $request->input('remark') ?? $audioGuide->remark,
                "call_to_action" => $request->input('call_to_action'),
                "theme" => $request->input('theme') ?? $audioGuide->theme,
                "duration" => $request->input('duration') ?? $audioGuide->duration,
            ]);
            if (!empty($request->input('description'))) {
                AudioDescription::where('audio_guide_id',$audioGuide->id)->update([
                    'files' => null,
                    'description' => $request->input('description'),
                ]);
                if (!empty($request->input('faqs'))) {
                    $faqs = json_decode($request->input('faqs'), true);
                    foreach ($faqs as $items) {
                        AudioFaq::where('id',$items['id'])->update([
                            'question' => $items['question'],
                            'answer' => $items['answer'],
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Audio guide successfully updated',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Audio guide couldn\'t update',
                'errors' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AudioGuide $audioGuide)
    {
        try {
            $invoice = InvoiceProduct::where('audio_guide_id', $audioGuide->id)->count();
            if ($invoice === 0) {
                if (!empty($audioGuide->cover)) {
                    Storage::disk('public')->delete($audioGuide->cover);
                }
                $description = AudioDescription::where('audio_guide_id', $audioGuide->id)->first();
                AudioFaq::where('audio_description_id', $description->id)->delete();
                AudioDescription::where('audio_guide_id', $audioGuide->id)->delete();
                AudioContent::where('audio_guide_id', $audioGuide->id)->delete();
                $audioGuide->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Audio guide successfully removed',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'You couldn\'t delete this guide, because some one was purchase this guide',
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Audio guide couldn\'t remove',
                'errors' => $th->getMessage(),
            ], 400);
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

    public function parseAffiliate($url)
    {}

    public function getAudioGuide($id)
    {
        $guide = AudioGuide::with(['Category', 'AudioContent','AudioDescription','AudioDescription.AudioFaq','UserGuide'])->find($id);
        return response()->json([
            'status' => true,
            'data' => $guide,
        ], 200);
    }

    // return the audio guide list
    public function onlyGuide()
    {
        return AudioGuide::paginate(
            $perPage = 1,
            $column = ['*'],
            $pageName = 'page'
        );
    }

    // return audio content by guide
    public function audioByContent($id)
    {
        return AudioContent::where('audio_guide_id', $id)->paginate(
            $perPage = 10,
            $column = ['*'],
            $pageName = 'page'
        );
    }

    public function homepage(Request $request){
        $purchase = UserGuide::where('user_id',$request->header('id'))->pluck('audio_guide_id')->toArray();
        $wishlist = ProductWish::where('user_id',$request->header('id'))->pluck('audio_guide_id')->toArray();

        $guides = AudioGuide::all()->toArray();

        $products = [];

        foreach($guides as $item){
            if(in_array($item['id'],$purchase)){
                $item['purchase'] = true;
            }
            if(in_array($item['id'],$wishlist)){
                $item['wishList'] = true;
            }
            $products[] = $item;
        }

        return response()->json($products);
    }
}
