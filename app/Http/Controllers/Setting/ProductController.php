<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Cms\ItemCategory;
use App\Models\Cms\ItemUnitType;
use App\Models\Cms\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $unit_types = ItemUnitType::all();
        $categories = ItemCategory::all();
        // $sub_categories = ItemSubcategory::all();

        return view('setting.products.list', [
            'products' => $products,
            'unit_types' => $unit_types,
            'categories' => $categories,
            // 'sub_categories' => $sub_categories,
        ]);
    } // End index

    public function list()
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "asc";
        $op = Product::orderBy($sort, $order);

        // dd($op);
        if ($search) {
            $op = $op->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        }
        $total = $op->count();

        $op = $op->paginate(request("limit"))->through(function ($op) {

            $actions =
                '<div class="font-sans-serif btn-reveal-trigger position-static">' .
                '<a href="javascript:void(0)" class="btn btn-sm" id="edit_product_offcanv"  data-id=' .
                $op->id .
                ' data-table="products_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>' .
                '<a href="javascript:void(0)" class="btn btn-sm" data-table="products_table" data-id="' .
                $op->id .
                '" id="delete_product" data-table="products_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="bx bx-trash text-danger"></i></a></div></div>';

            return [
                'id' => $op->id,
                'id1' => '<div class="ms-3">' . $op->id . '</div>',
                'image' => $op->image,
                'product_name' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-3">' . $op->product_name . '</div>',
                'product_price' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-1">' . $op->product_price . '</div>',
                'quantity_on_hand' => '<div class="align-middle white-space-wrap unit_type_idfw-bold fs-9 ms-1">' . $op->quantity_on_hand . '</div>',
                'unit_type' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-1">' . $op->unit_type?->title . '</div>',
                'description' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-1">' . $op->product_description . '</div>',
                // 'chicken_quantity' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-1">' . $op->chicken_quantity . '</div>',
                // 'meat_quantity' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-1">' . $op->meat_quantity . '</div>',
                // 'vegetarian_quantity' => '<div class="align-middle white-space-wrap fw-bold fs-9 ms-1">' . $op->vegetarian_quantity . '</div>',
                'active_flag' => '<span class="badge badge-phoenix badge-phoenix-' . $op->active_status?->color . '">' . $op->active_status?->name . '</span>',
                'actions' => $actions,
                'created_at' => format_date($op->created_at,  'H:i:s'),
                'updated_at' => format_date($op->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $op->items(),
            "total" => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd('createEvent');
        $user_id = Auth::user()->id;

        $rules = [
            'product_price' => ['required'],
            'product_name' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
        } else {

            $op = new Product();
            if ($request->hasFile('file_name')) {

                $file = $request->file('file_name');
                $fileNameWithExt = $request->file('file_name')->getClientOriginalName();
                // get file name
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                // get extension
                $extension = $request->file('file_name')->getClientOriginalExtension();

                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $fileNameToStore = rand() . date('ymdHis') . $file->getClientOriginalName();  // use this

                // Log::info($fileNameWithExt);
                // Log::info($filename);
                // Log::info($extension);
                // Log::info($fileNameToStore);

                // $path = $request->file('file_name')->storeAs('private/mds/event/logo', $fileNameToStore);
                Storage::disk('public')->putFileAs('products', $file, $fileNameToStore);

                // $path = $file->move('upload/profile_images/', $fileNameToStore);
                // Log::info($path);


            } else {
                $fileNameToStore = 'default.png';
            }

            $op->image = $fileNameToStore;
            $op->product_name = $request->product_name;
            $op->product_price = $request->product_price;
            $op->unit_type_id = intval($request->unit_type_id);
            $op->product_description = $request->product_description;
            $op->active_flag_id = 1;
            $op->created_by = $user_id;
            $op->updated_by = $user_id;

            $op->save();

            $notification = array(
                'message'       => 'Event created successfully',
                'alert-type'    => 'success'
            );

            $error = false;
            $message = 'Product ' . $op->name . ' successfully created';
        }
        // return response()->json([
        //     'error' => false,
        //     'message' => 'Project ' . $project->name . ' created successfully ',
        // ]);
        // Toastr::success('Has been add successfully :)','Success');
        return response()->json([
            'error' => $error,
            'message' => $message,
        ]);

        // return redirect()->route('projects.admin.project')->with($notification);
    } // store

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // dd('createEvent');
        $user_id = Auth::user()->id;

        $rules = [
            'product_price' => ['required'],
            'product_name' => ['required'],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
        } else {
            $op = Product::findOrFail($request->id);

            if ($request->hasFile('file_name')) {
                $file = $request->file('file_name');
                $fileNameWithExt = $request->file('file_name')->getClientOriginalName();
                // get file name
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                // get extension
                $extension = $request->file('file_name')->getClientOriginalExtension();

                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $fileNameToStore = rand() . date('ymdHis') . $file->getClientOriginalName();  // use this

                // Log::info($fileNameWithExt);
                // Log::info($filename);
                // Log::info($extension);
                // Log::info($fileNameToStore);

                Storage::disk('public')->delete('products/' . $op->image);
                // $path = $request->file('file_name')->storeAs('private/mds/event/logo', $fileNameToStore);
                Storage::disk('public')->putFileAs('products', $file, $fileNameToStore);

                // $path = $file->move('upload/profile_images/', $fileNameToStore);
                // Log::info($path);
                $op->image = $fileNameToStore;
            }

            $op->product_name = $request->product_name;
            $op->product_price = $request->product_price;
            $op->unit_type_id = intval($request->unit_type_id);
            $op->product_description = $request->product_description;
            $op->updated_by = $user_id;

            $op->save();

            $notification = array(
                'message'       => 'Product ' . $op->name . ' successfully updated',
                'alert-type'    => 'success'
            );

            $error = false;
            $message = 'Product ' . $op->name . ' successfully updated';
        }
        // return response()->json([
        //     'error' => false,
        //     'message' => 'Project ' . $project->name . ' created successfully ',
        // ]);
        // Toastr::success('Has been add successfully :)','Success');
        return response()->json([
            'error' => $error,
            'message' => $message,
        ]);

        // return redirect()->route('projects.admin.project')->with($notification);
    } // store

    /**
     * Show the form for editing the specified resource.
     */
    public function get($id)
    {
        $product = Product::findOrFail($id);


        return response()->json(['product' => $product]);
    }  // End function get

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {

        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'error' => true,
                'message' => 'Product not found',
            ]);
        }

        if ($product->image && Storage::disk('public')->exists('products/' . $product->image)) {
            Storage::disk('public')->delete('products/' . $product->image);
        }
        $product->delete();
        return response()->json([
            'error' => false,
            'message' => 'Product deleted successfully',
        ]);
    } // delete
}
