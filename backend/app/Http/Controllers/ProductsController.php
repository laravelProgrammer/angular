<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'category' => 'required',
            'brand' => 'required',
            'price' => 'required',
            'image' => 'required',
            'description' => 'required',

        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()->all()],422);
        }else{
            $product =  new Product();
            $product->name = $request->name;
            $product->category = $request->category;
            $product->brand = $request->brand;
            $product->price = $request->price;
            $product->image = $request->image;
            $product->description = $request->description;
            $product->save();
            $url = "http://127.0.0.1:8000/storage/";
            $file = $request->file('image');
            $extension = $file->extension();
            $path = $request->file('image')->storeAs('proImages', $product->id. '.'. $extension);
            $product->image = $path;
            $product->image_path = $url.$path;
            $product->save();
            return response()->json(['message' => 'Product addedd successfully', 'status' => 'true']);
        }
    }

    
    public function show(Request $request)
    {
        session(['keys' => $request->keys]);
        $products = Product::where(function($q){
            $q->where('products.id', session('keys'))
              ->orWhere('products.name', 'LIKE', '%'.session('keys'). '%')
              ->orWhere('products.price', 'LIKE', '%'.session('keys'). '%')
              ->orWhere('products.category', 'LIKE', '%'.session('keys'). '%')
              ->orWhere('products.brand', 'LIKE', '%'.session('keys'). '%');
        })->select('products.*')->get();
                return response()->json($products);

    }

    public function searchProducts(Request $request)
    {
        session(['keys' => $request->keys]);
        $products = Product::where(function($q){
            $q->where('products.id', session('keys'))
              ->orWhere('products.name', 'LIKE', '%'.session('keys'). '%')
              ->orWhere('products.price', 'LIKE', '%'.session('keys'). '%')
              ->orWhere('products.category', 'LIKE', '%'.session('keys'). '%')
              ->orWhere('products.brand', 'LIKE', '%'.session('keys'). '%');
        })->select('products.*')->get();
        return response()->json($products);

    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required',
            'name' => 'required',
            'category' => 'required',
            'brand' => 'required',
            'price' => 'required',
            'description' => 'required',

        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()->all()],422);
        }else{
            $product = Product::find($request->id);
            $product->name = $request->name;
            $product->category = $request->category;
            $product->brand = $request->brand;
            $product->price = $request->price;
            $product->image = $request->image;
            $product->description = $request->description;
            $product->save();
            $url = "http://127.0.0.1:8000/storage/";
            $file = $request->file('image');
            $extension = $file->extension();
            $path = $request->file('image')->storeAs('proImages', $product->id. '.'. $extension);
            $product->image = $path;
            $product->image_path = $url.$path;
            $product->save();
            return response()->json(['message' => 'Product updated successfully', 'status' => 'true']);
        }
    }

    public function destroy($id)
    {
        $yes = Product::find($id);
        if($yes){
            Storage::disk('public')->delete($yes->image);
            Product::find($id)->delete();
            return response()->json(['message' => 'Product deleted successfully', 'status' => 'true'],200);
        }
        return response()->json(['message' => 'Product not deleted', 'status' => 'false'],200);
        
    }
}
