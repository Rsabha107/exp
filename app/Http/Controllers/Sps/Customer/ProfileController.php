<?php

namespace App\Http\Controllers\Sps\Customer;

use App\Http\Controllers\Controller;
use App\Mail\QrCodeMail;
use App\Models\Sps\Profile;
use App\Models\Sps\ProhibitedItem;
use App\Models\Sps\StoredItem;
use App\Services\tokenService;
use Carbon\Carbon;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        Log::info('ProfileController index called ****************************************');

        $data = $request->all();
        // Check for data; if missing, prevent redirect loop
        if (empty($data)) {
            return view('sps.customer.error')->with('message', 'Please scan the QR code.');
        }

        $prohibited_items = ProhibitedItem::all();

        // $data = $request->all();
        if (!session('confirmed')) {
            // return redirect()->route('sps.customer.visitor')->with([
            //     'prohibitedItems' => $prohibited_items,
            //     'data' => $data,
            // ]); // block direct/back access
            return view('sps.customer.visitor', [
                'prohibitedItems' => $prohibited_items,
                'data' => $data,
            ]);
        }

        // Remove the flag so they can’t refresh to resubmit
        session()->forget('confirmed');

        // Log::info('Request data from url: ' . json_encode($data));

        return view('sps.customer.visitor', [
            'prohibitedItems' => $prohibited_items,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $rule =  [
            'venue_id' => 'required|exists:venues,id',
            'location_id' => 'required|exists:locations,id',
            'storage_type_id' => 'required|exists:storage_types,id',
            'event_id' => 'required|exists:events,id',
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'phone'      => 'required|string|max:20',
            'email_address' => 'required|email|max:100',
            'prohibited_item_id' => 'required|array',
            'prohibited_item_id.*' => 'exists:prohibited_items,id',
            'file_name' => 'required|array',
            'file_name.*' => 'image|mimes:jpeg,jpg,png,gif,webp', // Max 5MB per image
        ];
        // $validator = Validator::make($request->all(), [
        //     'venue_id' => 'required|exists:venues,id',
        //     'location_id' => 'required|exists:locations,id',
        //     'storage_type_id' => 'required|exists:storage_types,id',
        //     'event_id' => 'required|exists:events,id',
        //     'first_name' => 'required|string|max:50',
        //     'last_name'  => 'required|string|max:50',
        //     'phone'      => 'required|string|max:20',
        //     'email_address' => 'required|email|max:100',
        //     'prohibited_item_id' => 'required|array',
        //     'prohibited_item_id.*' => 'exists:prohibited_items,id',
        //     'file_name' => 'required|array',
        //     'file_name.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120', // Max 5MB per image
        // ]);

        $message = [
            'file_name.required'   => 'Please upload at least one image.',
            'file_name.*.required' => 'Each image is required.',
            'file_name.*.image'    => 'Each file must be a valid image.',
            'file_name.*.mimes'    => 'Only JPEG, PNG, GIF, or WebP images are allowed.',
            'file_name.*.uploaded' => 'One of the images failed to upload. Please try again.',
        ];

        $validator = Validator::make($request->all(), $rule, $message);

        if ($validator->fails()) {
            return redirect()->back()->with('errors', $validator->errors())->withInput($request->all());
        }

        DB::beginTransaction();

        try {
            // Create Profile

            $submitted = getStatusIdByLabel('submitted');
            $checked_in = getStatusIdByLabel('checked-in');
            $returned = getStatusIdByLabel('returned');

            $visitor = new Profile();
            $visitor->first_name = $request->first_name;
            $visitor->last_name = $request->last_name;
            $visitor->phone = $request->phone;
            $visitor->email_address = $request->email_address;
            $visitor->item_status_id = $submitted; // Set the status to 'Submitted'
            $visitor->storage_type_id = $request->storage_type_id;
            $visitor->venue_id = $request->venue_id;
            $visitor->location_id = $request->location_id;
            $visitor->event_id = $request->event_id;

            $visitor->save();

            // Log::info('Created visitor: ' . $visitor->id);

            // Handle items
            foreach ($request->prohibited_item_id as $key => $itemId) {
                $fileNameToStore = 'noimage.jpg';

                if ($request->hasFile('file_name') && isset($request->file_name[$key])) {
                    try {
                        $file = $request->file_name[$key];
                        Log::info('Processing file: ' . $file->getClientOriginalName() . ' for item ID: ' . $itemId);
                        $fileNameToStore = rand() . date('ymdHis') . $file->getClientOriginalName();
                        Log::info('Generated filename: ' . $fileNameToStore);
                        $destinationPath = public_path('storage/items/img/');
                        Log::info('Destination path: ' . $destinationPath);
                        $image = Image::read($file);
                        // $image->resize(150, 150);
                        $image->resize(1024, 1024, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });

                        Log::info('Image resized for: ' . $fileNameToStore);
                        // $image->save($destinationPath . $fileNameToStore);
                        $image->toJpeg(75)->save($destinationPath . $fileNameToStore);
                        Log::info('Image saved: ' . $fileNameToStore);
                        if (filesize($destinationPath . $fileNameToStore) > 1024 * 1024) {
                            Log::warning('Image still larger than 1MB after compression: ' . $fileNameToStore);
                        }
                    } catch (\Throwable $fileError) {
                        Log::error('Image processing failed: ' . $fileError->getMessage());
                        // Optionally skip the item or fail the whole request
                        // continue;
                        throw $fileError;
                    }
                }

                $storedItem = new StoredItem();
                $storedItem->item_image = $fileNameToStore;
                $storedItem->item_image_path = 'restricted/img/' . $fileNameToStore;
                $storedItem->profile_id = $visitor->id;
                $storedItem->item_id = $itemId;
                $storedItem->item_description = $request->item_description[$key] ?? null;
                $storedItem->event_id = $request->event_id;
                $storedItem->venue_id = $request->venue_id;
                $storedItem->location_id = $request->location_id;
                $storedItem->item_status_id = $submitted; // Set the status to 'Submitted'
                $storedItem->save();

                Log::info('Stored item: ' . json_encode($storedItem));
            }

            DB::commit();

            $payload = Crypt::encrypt($visitor->id);
            // $profile = base64_encode($payload);
            session(['confirmed' => true]); // mark as confirmed

            return redirect()->route('sps.customer.confirmation', ['token' => $payload])
                ->with('message', 'Visitor Information created!')
                ->with('alert-type', 'success')
                ->with('status', 'success');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Visitor creation failed: ' . $e->getMessage());
            Log::debug($e);


            return redirect()->back()
                ->with('message', 'Visitor creation failed: ' . $e->getMessage())
                ->with('alert-type', 'error')
                ->with('status', 'error')
                ->withInput();
        }
    }


    public function confirmation($token)
    {
        Log::info('ProfileController confirmation called');
        if (!session()->has('status')) {
            return redirect()->route('index')->with('error', 'No visitor data found.');
        }
        $id = Crypt::decrypt($token);
        Log::info('Decrypted profile: ' . json_encode($id));

        $profile = Profile::find($id);
        $result = Builder::create()
            ->writer(new PngWriter())           // Ensure PNG format
            ->data($profile->ref_number)                       // QR code content
            ->size(300)                         // Image size (px)
            ->margin(10)                        // Margin (quiet zone)
            ->backgroundColor(new Color(255, 255, 255)) // Background color
            ->build();

        $filename = 'qrcodes/profile-' . $profile->id . '-' . Str::random(6) . '.png';
        Storage::put('public/' . $filename, $result->getString());
        $filePath = storage_path('app/public/' . $filename);

        //This QR is for the Operators who scans the spectator by using mobile//

        $resultMobile = Builder::create()
            ->writer(new PngWriter())           // Ensure PNG format
            ->data(route('sps.operator.find.mobile', $profile->ref_number))                       // QR code content
            ->size(300)                         // Image size (px)
            ->margin(10)                        // Margin (quiet zone)
            ->backgroundColor(new Color(173, 216, 230)) // Light blue background
            ->build();

        $filename = 'qrcodes/profile-mobile' . $profile->id . '-' . Str::random(6) . '.png';

        Storage::put('public/' . $filename, $resultMobile->getString());
        $filePathMobile = storage_path('app/public/' . $filename);

        // $filePath = public_path('qrcodes/sps-visitor-' . $profile->id . '-' . Str::random(6) . '.png');
        // $result->saveToFile($filePath);

        Mail::to($profile->email_address)->send(new QrCodeMail($profile, $filePath, $filePathMobile));

        return view('sps.customer.confirmation', ['profile' => $profile, 'qrBase64' => base64_encode($result->getString())]);

        return response($result->getString(), 200)
            ->header('Content-Type', 'image/png');
    }

    public function save($text = 'https://example.com')
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($text)
            ->size(300)
            ->margin(10)
            ->build();

        $filePath = public_path('qrcodes/qr.png');
        $result->saveToFile($filePath);

        return "QR code saved to: /qrcodes/qr.png";
    }
}
