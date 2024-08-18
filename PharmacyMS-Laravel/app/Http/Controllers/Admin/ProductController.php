<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use QCod\AppSettings\Setting\AppSettings;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = 'products';
        if ($request->ajax()) {
            $products = Product::latest()->get();
            return DataTables::of($products)
                ->addColumn('product', function ($product) {
                    $image = '';
                    $purchase = $product->purchase;
                    if (!empty($purchase)) {
                        if (!empty($purchase->image)) {
                            $image = '<span class="avatar avatar-sm mr-2">
                            <img class="avatar-img" src="'.asset("storage/purchases/".$purchase->image).'" alt="image">
                            </span>';
                        }
                        return $purchase->product. ' ' . $image;
                    }
                    return $image;
                })
                ->addColumn('category', function ($product) {
                    $category = null;
                    $purchase = $product->purchase;
                    if (!empty($purchase) && !empty($purchase->category)) {
                        $category = $purchase->category->name;
                    }
                    return $category;
                })
                ->addColumn('price', function ($product) {
                    return settings('app_currency','$').' '. $product->price;
                })
                ->addColumn('quantity', function ($product) {
                    $purchase = $product->purchase;
                    if (!empty($purchase)) {
                        return $purchase->quantity;
                    }
                    return 0;
                })
                ->addColumn('expiry_date', function ($product) {
                    $purchase = $product->purchase;
                    if (!empty($purchase)) {
                        return date_format(date_create($purchase->expiry_date), 'd M, Y');
                    }
                    return 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $editbtn = '<a href="'.route("products.edit", $row->id).'" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                    $deletebtn = '<a data-id="'.$row->id.'" data-route="'.route('products.destroy', $row->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
                    if (!auth()->user()->hasPermissionTo('edit-product')) {
                        $editbtn = '';
                    }
                    if (!auth()->user()->hasPermissionTo('destroy-purchase')) {
                        $deletebtn = '';
                    }
                    return $editbtn.' '.$deletebtn;
                })
                ->rawColumns(['product', 'action'])
                ->make(true);
        }

        return view('admin.products.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'add product';
        $purchases = Purchase::all();
        return view('admin.products.create', compact('title', 'purchases'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'product' => 'required|max:200',
            'price' => 'required|min:1',
            'description' => 'nullable|max:255',
        ]);

        Product::create([
            'purchase_id' => $request->product,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        $notification = notify("Product has been added");
        return redirect()->route('products.index')->with($notification);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $title = 'edit product';
        $purchases = Purchase::all();
        return view('admin.products.edit', compact('title', 'product', 'purchases'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $this->validate($request, [
            'product' => 'required|max:200',
            'price' => 'required',
            'description' => 'nullable|max:255',
        ]);

        $product->update([
            'purchase_id' => $request->product,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        $notification = notify('Product has been updated');
        return redirect()->route('products.index')->with($notification);
    }

    /**
     * Display a listing of expired resources.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */



     
    public function expired(Request $request)
    {
        $title = "Expired Products";
        if ($request->ajax()) {
            $products = Purchase::whereDate('expiry_date', '<', Carbon::now())->get();
            return DataTables::of($products)
                ->addColumn('product', function ($product) {
                    $image = '';
                    if (!empty($product->image)) {
                        $image = '<span class="avatar avatar-sm mr-2">
                            <img class="avatar-img" src="'.asset("storage/purchases/".$product->image).'" alt="image">
                            </span>';
                    }
                    return $product->product. ' ' . $image;
                })
                ->addColumn('category', function ($product) {
                    return $product->category->name ?? 'N/A';
                })
                ->addColumn('price', function ($product) {
                    return settings('app_currency','$').' '. $product->price;
                })
                ->addColumn('quantity', function ($product) {
                    return $product->quantity;
                })
                ->addColumn('expiry_date', function ($product) {
                    return date_format(date_create($product->expiry_date), 'd M, Y');
                })
                ->addColumn('action', function ($row) {
                    $editbtn = '<a href="'.route("products.edit", $row->id).'" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                    $deletebtn = '<a data-id="'.$row->id.'" data-route="'.route('products.destroy', $row->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
                    if (!auth()->user()->hasPermissionTo('edit-product')) {
                        $editbtn = '';
                    }
                    if (!auth()->user()->hasPermissionTo('destroy-purchase')) {
                        $deletebtn = '';
                    }
                    return $editbtn.' '.$deletebtn;
                })
                ->rawColumns(['product', 'action'])
                ->make(true);
        }

        return view('admin.products.expired', compact('title'));
    }




    /**
     * Display a listing of out of stock resources.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function outstock(Request $request)
    {
        $title = "Out of Stock Products";
        if ($request->ajax()) {
            $products = Purchase::where('quantity', '<=', 0)->get();
            return DataTables::of($products)
                ->addColumn('product', function ($product) {
                    $image = '';
                    if (!empty($product->image)) {
                        $image = '<span class="avatar avatar-sm mr-2">
                            <img class="avatar-img" src="'.asset("storage/purchases/".$product->image).'" alt="image">
                            </span>';
                    }
                    return $product->product. ' ' . $image;
                })
                ->addColumn('category', function ($product) {
                    return $product->category->name ?? 'N/A';
                })
                ->addColumn('price', function ($product) {
                    return settings('app_currency','$').' '. $product->price;
                })
                ->addColumn('quantity', function ($product) {
                    return $product->quantity;
                })
                ->addColumn('expiry_date', function ($product) {
                    return date_format(date_create($product->expiry_date), 'd M, Y');
                })
                ->addColumn('action', function ($row) {
                    $editbtn = '<a href="'.route("products.edit", $row->id).'" class="editbtn"><button class="btn btn-info"><i class="fas fa-edit"></i></button></a>';
                    $deletebtn = '<a data-id="'.$row->id.'" data-route="'.route('products.destroy', $row->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
                    if (!auth()->user()->hasPermissionTo('edit-product')) {
                        $editbtn = '';
                    }
                    if (!auth()->user()->hasPermissionTo('destroy-purchase')) {
                        $deletebtn = '';
                    }
                    return $editbtn.' '.$deletebtn;
                })
                ->rawColumns(['product', 'action'])
                ->make(true);
        }

        return view('admin.products.outstock', compact('title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        return Product::findOrFail($request->id)->delete();
    }
}
