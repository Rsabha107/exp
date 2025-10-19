<?php

namespace App\Http\Controllers\Sps\User;

use App\Http\Controllers\Controller;
use App\DataTables\UsersDataTable;
use App\Models\Department;
use App\Models\Event;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\UtilController;
use App\Mail\SendUserCreationLink;
use App\Services\SignedUserLinkGenerator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    //

    public function profile()
    {
        $user = User::find(Auth::user()->id);
        $file = $user->file_attach;

        return view('sps.users.profile', compact('user', 'file'));
    }

    public function showForm()
    {
        return view('sps.users.invite-user');
    }

    public function sendInvite(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email',
        // ]);

        $link = SignedUserLinkGenerator::generate($request->name, $request->email);
        Mail::to($request->email)->send(new SendUserCreationLink($link, $request->name));

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
        return redirect()->route('auth.signup');
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
