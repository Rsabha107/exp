<?php

namespace App\Http\Controllers\Chl\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendAccessConfirmEmailJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Mail\SendUserCreationLink;
use App\Mail\SendUserCreationLinkMail;
use App\Models\Setting\Event;
use App\Services\SignedUserLinkGenerator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    //

    public function profile()
    {
        $user = User::find(Auth::user()->id);
        $file = $user->file_attach;

        return view('mds/admin/users/profile', compact('user', 'file'));
    }

    public function msStore(Request $request)
    {

        Log::info('UserController@msStore - Request: ' . json_encode($request->all()));
        try {
            $rules = [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|max:15',

                'venue_id' => 'required',
                'event_id' => 'required',
                'roles' => 'required|array|min:1',
            ];

            $message = '
            [
                "name.required" => "Name is required",
                "email.required" => "Email is required",
                "email.email" => "Provide a valid email",
                "email.unique" => "Email already exists",
                "phone.required" => "Phone is required",
                "venue_id.required" => "Venue selection is required",
            ]';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors($validator);
            }

            // @unlink(public_path('upload/instructor_images/' . $data->photo));
            // $id = Auth::user()->id;
            $user = new User();

            $generated_password = generateSecurePassword();
            $hashed_password = Hash::make($generated_password);
            $user->password = $hashed_password;
            $user->employee_id = 0;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            // $user->password = Hash::make($request->password);
            $user->status = 1;
            $user->usertype = 'user';
            $user->is_admin = 0;
            $user->first_login_flag = 0;
            $user->role = 'user';
            // $user->address = 'doha';
            $user->save();

            $roles = $request->roles;

            $intRoles = collect($roles)->map(function ($role) {
                return (int)$role;
            });
            if ($request->roles) {
                $user->assignRole($intRoles);
            }

            if ($request->event_id) {
                foreach ($request->event_id as $key => $data) {
                    Log::info('Event ID: ' . $data);
                    $user->events()->attach($request->event_id[$key]);
                }
            }

            if ($request->venue_id) {
                foreach ($request->venue_id as $key => $data) {
                    $user->venues()->attach($request->venue_id[$key]);
                }
            }

            Log::info('Assigning roles: ' . json_encode($intRoles));

            $notification = array(
                'message'       => 'User created successfully',
                'alert-type'    => 'success'
            );

            if (config('settings.send_notifications')) {
                $eventNames = $user->events()->exists()
                    ? $user->events->pluck('name')->implode(', ')
                    : 'N/A';
                $venueNames = $user->venues()->exists()
                    ? $user->venues->pluck('name')->implode(', ')
                    : 'N/A';
                $userRoless = $user->getRoleNames()->implode(', ') ?? 'N/A';
                Log::info('User Roles: ' . $userRoless);
                $details = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'event' => $eventNames,
                    'venue' => $venueNames,
                    // 'role' => $userRoless,
                ];
                // Send email notification
                SendAccessConfirmEmailJob::dispatch($details);
            }

            return Redirect::route('login')->with($notification);
        } catch (\Exception $e) {
            Log::error('Validation error in UserController@store: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }

        // Toastr::success('Has been add successfully :)','Success');
        // return redirect()->back()->with($notification);
        //mainProfileStore

    }

    public function showForm()
    {
        $events = Event::all();
        return view('mds.admin.users.invite-user', compact('events'));
    }

    public function sendInvite(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'event_id' => 'required|exists:mds_events,id',
        ];

        $messages = [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'event_id.required' => 'Please select an event.',
            'event_id.exists' => 'The selected event is invalid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Log::info($validator->errors());
            $error = true;
            $type = 'success';
            $message = $validator->messages();
            return redirect()->back()->withErrors($message)->withInput();
        }

        $link = SignedUserLinkGenerator::generate($request->name, $request->email, $request->event_id, 30); // valid for 30 mins
        Mail::to($request->email)->send(new SendUserCreationLinkMail($link, $request->name));

        return redirect()->back()->with('success', 'Invitation sent successfully to ' . $request->email);
    }

    public function createViaLink(Request $request)
    {
        // Optional auto-creation if params are passed
        Log::info('Creating user via signed link. createViaLink');
        Log::info($request->all());
        // if ($request->filled(['name', 'email']) ) {
        //     $user = User::firstOrCreate(
        //         ['email' => $request->email],
        //         [
        //             'name' => $request->name,
        //             'password' => bcrypt(Str::random(10)) // You can also send a password param
        //         ]
        //     );

        //     return response()->json([
        //         'message' => 'User created or already exists.',
        //         'user' => $user,
        //     ]);
        // }

        // Otherwise, show a creation form
        // Log::info('No name or email provided, redirecting to signup form.');
        return redirect()->route('mds.auth.signup', [
            'name' => $request->query('name'),
            'email' => $request->query('email'),
            'event_id' => $request->query('event_id'),
        ]);
    }

    public function sendCreationLink()
    {
        $name = 'John Doe';
        $email = 'john@example.com';

        $signedUrl = SignedUserLinkGenerator::generate($name, $email, 60); // valid for 60 mins

        Mail::to($email)->send(new SendUserCreationLink($signedUrl, $name));

        return response()->json(['message' => 'User creation link sent to ' . $email]);
    }

    public function update(Request $request)
    {

        $rules = [
            'file_name' => 'nullable|image|mimes:jpeg,png|max:2048', // max 2MB
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $notification = array(
                'message' => $validator->errors()->first(),
                'alert-type' => 'error'
            );

            return redirect()->back()
                ->withInput()
                ->with($notification);
        }

        $id = Auth::user()->id;
        $user = User::find($id);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;

        Log::info($request->all());
        if ($request->hasFile('file_name')) {

            $file = $request->file('file_name');
            $fileNameWithExt = $request->file('file_name')->getClientOriginalName();
            // get file name
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            // get extension
            $extension = $request->file('file_name')->getClientOriginalExtension();

            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            Log::info($fileNameWithExt);
            Log::info($filename);
            Log::info($extension);
            Log::info($fileNameToStore);

            // upload
            if ($user->photo != 'default.png') {
                Storage::delete('public/upload/profile_images/' . $user->photo);
            }

            $path = $request->file('file_name')->storeAs('public/upload/profile_images', $fileNameToStore);
            // $path = $file->move('upload/profile_images/', $fileNameToStore);
            Log::info($path);
        } else {
            $fileNameToStore = 'noimage.jpg';
        }

        $user->photo = $fileNameToStore;

        $user->save();

        $notification = array(
            'message' => 'Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function updatePassword(Request $request)
    {
        $id = Auth::user()->id;
        $user = User::find($id);

        $rules = [
            'password' => 'required|confirmed|min:8|max:16',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $notification = array(
                'message' => $validator->errors()->first(),
                'alert-type' => 'error'
            );

            return redirect()->back()
                ->withInput()
                ->with($notification);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            $notification = array(
                'message' => 'Old Password is incorrect',
                'alert-type' => 'error'
            );

            // Toastr::error('Old Password is incorrect','Error');
            return redirect()->back()->with($notification);
        }

        // $user->password = Hash::make($request->password);
        // $user->save();

        // $notification = array(
        //     'message' => 'Password Updated Successfully',
        //     'alert-type' => 'success'
        // );

        // return redirect()->back()->with($notification);
    }
}
