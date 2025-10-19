<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\Location;
use App\Models\Setting\storageType;
use Illuminate\Container\Attributes\Storage;
// use App\Models\Mds\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// use Illuminate\Support\Facades\Redirect;

class StorageTypeController extends Controller
{
    //
    public function index()
    {
        $storageTypes = storageType::all();
        return view('setting.storage-type.list', compact('storageTypes'));
    }

    public function get($id)
    {
        $storageType = storageType::findOrFail($id);
        return response()->json(['storageType' => $storageType]);
    }

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $ops = storageType::orderBy($sort, $order);

        if ($search) {
            $ops = $ops->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('short_name', 'like', '%' . $search . '%')
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        $total = $ops->count();
        $ops = $ops->paginate(request("limit"))->through(function ($op) {
            $div_action = '<div class="font-sans-serif btn-reveal-trigger position-static">';

            $update_action =
                '<a href="javascript:void(0)" class="btn btn-sm" id="editLocation" data-id=' . $op->id .
                ' data-table="location_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>';
            $delete_action =
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="location_table" data-id="' .
                $op->id .
                '" id="deleteLocation" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="fa-solid fa-trash text-danger"></i></a></div></div>';

            return  [
                'id' => $op->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$op->id. '</div>',
                'title' => '<div class="align-middle white-space-wrap fs-9">' . $op->title . '</div>',
                'actions' => $div_action . $update_action . $delete_action,
                'created_at' => format_date($op->created_at,  'H:i:s'),
                'updated_at' => format_date($op->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $ops->items(),
            "total" => $total,
        ]);
    }

    public function store(Request $request)
    {
        //
        // dd($request);
        $user_id = Auth::user()->id;
        $storageType = new storageType();

        $rules = [
            'title' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info($validator->errors());
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
        } else {

            $error = false;
            $message = 'Storage Type created succesfully.' . $storageType->id;

            $storageType->title = $request->title;
            $storageType->created_by = $user_id;
            $storageType->updated_by = $user_id;
            $storageType->active_flag = 1;

            $storageType->save();
        }

        $notification = array(
            'message'       => 'Storage Type created successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    }

    public function update(Request $request)
    {
        $formFields = $request->validate([
            'id' => ['required'],
            'title' => ['required'],
        ]);

        $storageType = storageType::findOrFail($request->id);

        // dd($location);

        if ($storageType->update($formFields)) {
            return response()->json(['error' => false, 'message' => 'Storage Type updated successfully.', 'id' => $storageType->id]);
        } else {
            return response()->json(['error' => true, 'message' => 'Storage Type couldn\'t updated.']);
        }
    }

    public function delete($id)
    {
        $ws = StorageType::findOrFail($id);
        $ws->delete();

        $error = false;
        $message = 'Storage Type deleted succesfully.';

        $notification = array(
            'message'       => 'Storage Type deleted successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
    } // delete

}
