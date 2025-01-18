<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\JWTAuth;
use App\Mail\AudioUpload;
use App\Models\AudioContent;
use App\Models\AudioDescription;
use App\Models\AudioFaq;
use App\Models\AudioGuide;
use App\Models\Category;
use App\Models\InvoiceProduct;
use App\Models\Person;
use App\Models\PersonEvent;
use App\Models\PersonLocation;
use App\Models\PersonObject;
use App\Models\ProductReview;
use App\Models\ProductWish;
use App\Models\Update;
use App\Models\User;
use App\Models\UserGuide;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
            'data' => AudioGuide::with('UserGuide')->get(),
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
                'short_description' => 'required',
                'title' => 'required|unique:audio_guides,title',
                'call_to_action' => 'required',
                'remark' => 'required',
                'status' => 'required',
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

            try {
                DB::beginTransaction();
                $audio_guide = null;
                $price = null;
                $free = null;
                $cat_id = 1;
                foreach ($sheetData as $key => $items) {
                    $data = $items;
                    $item = (object) $items;
                    if ($key === 0) {
                        $price = $item->total_price ?? null;
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
                            "title" => $request->input('title') ?? $file_name[0],
                            "status" => $request->input('status'),
                            "price" => $price ?? $request->input('price'),
                            "short_description" => $request->input('short_description') ?? null,
                            "cover" => Storage::disk('public')->put('guides', $request->file('cover')),
                            "category_id" => $cat_id,
                            "remark" => $request->input('remark') ?? null,
                            "call_to_action" => $request->input('call_to_action'),
                            "theme" => $request->input('theme') ?? null,
                            "duration" => $request->input('duration') ?? '0',
                            "lessons" => count($sheetData),
                        ]);
                    }
                    if (!empty($item->free) && $free === null) {
                        $free = $item->file_mp3;
                    }
                    $data['audio_guide_id'] = $audio_guide->id;
                    AudioContent::create($data);
                }

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
                if ($free !== null && $audio_guide !== null) {
                    AudioGuide::where('id', $audio_guide->id)->update([
                        'theme' => $free
                    ]);
                }
                if ($audio_guide !== null) {
                    Update::create([
                        'image' => Storage::disk('public')->put('guides', $request->file('cover')),
                        'title' => $request->input('title'),
                        'sub_title' => $request->input('short_description'),
                        'reference_id' => $audio_guide->id
                    ]);
                }

                DB::commit();

                $users = User::where('role', '!=', 'admin')->get();
                foreach ($users as $user) {
                    Mail::to($user->email)->send(new AudioUpload($request->title, $request->short_description, $price ?? $request->price));
                }

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
                'message' => "Please select a CSV file",
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AudioGuide $audioGuide)
    {
        if($audioGuide->type === 'special'){
            return response()->json([
                'status' => true,
                'message' => 'Audio guide successfully retrieved',
                'data' => $audioGuide->load(['Category', 'AudioDescription', 'AudioDescription.AudioFaq', 'UserGuide','person','personObject','personLocation','personEvent']),
            ],200);
        }
        return response()->json([
            'status' => true,
            'message' => 'Audio guide successfully retrieved',
            'data' => $audioGuide->load(['Category', 'AudioDescription', 'AudioDescription.AudioFaq', 'UserGuide', 'AudioContent']),
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
                AudioDescription::where('audio_guide_id', $audioGuide->id)->update([
                    'files' => null,
                    'description' => $request->input('description'),
                ]);
                if (!empty($request->input('faqs'))) {
                    $faqs = json_decode($request->input('faqs'), true);
                    foreach ($faqs as $items) {
                        AudioFaq::where('id', $items['id'])->update([
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
                if($audioGuide->type !== 'special'){
                    AudioContent::where('audio_guide_id', $audioGuide->id)->delete();
                }
                $description = AudioDescription::where('audio_guide_id', $audioGuide->id)->first();
                if(!empty($description)){
                    AudioFaq::where('audio_description_id', $description->id)->delete();
                    $description->delete();
                }
                if($audioGuide->type === 'special'){
                    PersonObject::where('audio_guide_id',$audioGuide->id)->delete();
                    PersonEvent::where('audio_guide_id',$audioGuide->id)->delete();
                    PersonLocation::where('audio_guide_id',$audioGuide->id)->delete();
                    Person::where('audio_guide_id',$audioGuide->id)->delete();
                }
                $delete = $audioGuide->delete();
                if (!empty($audioGuide->cover) && $delete == '1') {
                    Storage::disk('public')->delete($audioGuide->cover);
                }
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
                'message' => 'Audio guide couldn\'t remove, maybe its related with a user cart list, wishlist or some one already purchased',
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

    public function parseAffiliate($url) {}

    public function getAudioGuide(Request $request, $id)
    {
        $guide = AudioGuide::with(['Category', 'AudioContent', 'AudioDescription', 'AudioDescription.AudioFaq', 'UserGuide', 'ProductReview'])->find($id);

        if ($request->header('Authorization')) {
            $token = JWTAuth::verifyToken($request->header('Authorization'), false);
            if ($token) {
                $user = User::find($token->id);
                $exist = UserSubscription::where('user_id', $token->id)->where('guide_id', $id)->where('ended_at', '>', date('Y-m-d'))->orderBy('id', 'desc')->first();
                $status = (new UserController)->subscriptionStatus($token->id);
                $wishlist = ProductWish::where('user_id', $token->id)->where('audio_guide_id', $id)->count();
                $review = ProductReview::where('user_id', $token->id)->where('audio_guide_id', $id)->count();

                if (!empty($exist) && ($exist->status === 'paid' || $exist->status === 'complete' || $exist->status === 'active')) {
                    $guide['purchase'] = true;
                    $guide['wishlist'] = $wishlist > 0 ? true : false;
                    $guide['review'] = $review > 0 ? true : false;
                } elseif ($user->demo == '1' || $user->role == 'admin') {
                    $guide['purchase'] = true;
                    $guide['wishlist'] = $wishlist > 0 ? true : false;
                    $guide['review'] = $review > 0 ? true : false;
                } else {
                    $guide['wishlist'] = $wishlist > 0 ? true : false;
                    $guide['review'] = $review > 0 ? true : false;
                    if ($status === 'autorenew' || $status === 'lifetime') {
                        $guide['purchase'] = true;
                    }else{
                        $guide['purchase'] = false;
                    }
                }
            } else {
                $guide['purchase'] = false;
                $guide['wishlist'] = false;
                $guide['review'] = false;
            }
        } else {
            $guide['purchase'] = false;
            $guide['wishlist'] = false;
            $guide['review'] = false;
        }

        return response()->json([
            'status' => true,
            'data' => $guide,
        ], 200);
    }

    // guide searching
    public function adminGuideSearch(Request $request)
    {
        $guides = AudioGuide::where('title', 'LIKE', "%$request->search%")->with(['Category', 'AudioDescription', 'AudioDescription.AudioFaq', 'UserGuide', 'AudioContent'])->get();
        return response()->json([
            'status' => true,
            'data' => $guides,
        ], 200);
    }

    // guide searching
    public function guideSearch(Request $request)
    {
        $guides = AudioGuide::where('title', 'LIKE', "%$request->search%")->get()->toArray();

        $token = null;
        $user = null;
        if ($request->header('Authorization')) {
            $token = JWTAuth::verifyToken($request->header('Authorization'), false);
            $user = User::find($token->id);
        }
        $search = [];
        foreach ($guides as $guide) {
            if ($token) {
                $exist = UserSubscription::where('user_id', $token->id)->where('guide_id', $guide['id'])->where('ended_at', '>', date('Y-m-d'))->orderBy('id', 'desc')->first();
                $status = (new UserController)->subscriptionStatus($token->id);
                $wishlist = ProductWish::where('user_id', $token->id)->where('audio_guide_id', $guide['id'])->count();

                if (!empty($exist) && ($exist->status === 'paid' || $exist->status === 'complete' || $exist->status === 'active')) {
                    $guide['purchase'] = true;
                    $guide['whishlist'] = $wishlist > 0 ? true : false;
                } elseif ($user->demo == '1' || $user->role == 'admin') {
                    $guide['purchase'] = true;
                    $guide['whishlist'] = $wishlist > 0 ? true : false;
                } else {
                    $guide['purchase'] = $status !== null ? true : false;
                    $guide['whishlist'] = $wishlist > 0 ? true : false;
                }
            } else {
                $guide['purchase'] = false;
                $guide['whishlist'] = false;
            }
            $search[] = $guide;
        }

        return response()->json([
            'status' => true,
            'data' => $search,
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

    public function homepage(Request $request)
    {

        $purchase = UserSubscription::where('user_id', $request->header('id'))->where('ended_at', '>', date('Y-m-d'))->pluck('guide_id')->toArray();
        $wishlist = ProductWish::where('user_id', $request->header('id'))->pluck('audio_guide_id')->toArray();

        $guides = AudioGuide::all()->toArray();

        $products = [];
        $token = null;
        $status = null;
        $user = null;
        if ($request->header('Authorization')) {
            $token = JWTAuth::verifyToken($request->header('Authorization'), false);
            $status = (new UserController)->subscriptionStatus($token->id);
            $user = User::find($token->id);
        }

        foreach ($guides as $item) {
            if (in_array($item['id'], $purchase)) {
                $item['purchase'] = true;
            } elseif ($user->demo == '1' || $user->role == 'admin') {
                $item['purchase'] = true;
            } else {
                if ($status === 'autorenew' || $status === 'lifetime') {
                    $item['purchase'] = true;
                }else{
                    $item['purchase'] = false;
                }
            }
            if (in_array($item['id'], $wishlist)) {
                $item['wishList'] = true;
            }

            $products[] = $item;
        }

        return response()->json($products);
    }

    /***
     * Top selling guides
     */
    public function topSell(){
        $total = UserGuide::where('audio_guide_id','!=', Null)->orderBy('id','desc')
        ->limit(100)
        ->get()
        ->groupBy('audio_guide_id');

        $total_sell = [];
        foreach($total as $key=>$value){
            $total_sell[] = AudioGuide::where('id',$key)->first()->toArray();
        }
        return $total_sell;
    }
}
