<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;


class ProductsController extends Controller
{
    public function index(){
        try{
            $result = Product::all()->sortDesc();
            if($result->count() == 0){
                return response()->json(["No Entries" => "There are no products."], 404); 
            }else{
                $res = array();
            foreach($result as $row){
                $res[] = array("id" => $row->id,
                            "name" => $row->name,
                            "category" => $row->name,
                            "quantity" => $row->quantity);
            } 
            return response()->json($res);
            }          
        }catch(\Exception $e){
            return response()->json(["Error"=>"Server Error " . $e->getMessage()], 500);
        }
    }

    public function add(Request $request){
        try{
            $validator = Validator::make($request->all(), ['name'=>'required',
                                                        'quantity'=>'required|integer',
                                                        'categoryId' => 'required|integer',
                                                        'price' => 'required']);
            if($validator->fails()){
                return response()->json(["invalid entry" => $validator->errors()], 400);
            }else{
                $result = Product::create($request->all());
                $res = (object) array("id"=> $result->id,
                                        "name" => $result->name, 
                                        "category" => $request->categoryId,
                                        "quantity" => $request->quantity,
                                        "price" => $request->price);                    
                return response()->json($res, 201);
            }
        }catch(\Exception $e){
            return response()->json(["Error"=>"Server Error"], 500);
        }
}

public function getProduct(Request $request, $id){
    try{
        $result = Product::find($id);
        if($result == null){
            return response()->json(["Not Found"=>"No product found"], 404);
        }else{
            return response()->json((object) array("id" => $result->id,
                                                    "name" => $result->name,
                                                    "category" => $result->categoryId,
                                                    "quantity" => $result->quantity,
                                                    "price" => $result->price));
        }
    }catch(\Exception $e){
        return response()->json(["Error"=>"Server Error"], 500);
    }
}

public function editProduct(Request $request, $id){
    try{
       $result = Product::find($id);
       if($result == null){
        return response()->json(["Not Found"=>"No product found"], 404);
       }else{
        $validator = Validator::make($request->all(), ["name" => "required",
                                                        "quantity" => "required|integer",
                                                        'categoryId' => 'required|integer',
                                                        'price' => 'required'
                                                    ]);
        if($validator->fails()){
            return response()->json(["Invalid Entry" => $validator->errors()], 400);
        }else{
            $result->name = $request->name;
            $result->quantity = $request->quantity;
            $result->categoryId = $request->categoryId;
            $result->price = $request->price;
            $result->save();
            return response()->json((object) array("id" => $result->id,
                                                    "name" => $result->name,
                                                    "quantity"=> $result->quantity,
                                                    "category"=> $result->categoryId,
                                                    "price"=> $result->price), 201);
        }
       }
    }catch(\Exception $e){
        return response()->json(["Error"=>"Server Error "], 500);
    }
}

public function delete(Request $request, $id){
    try{
        $result = Product::find($id);
        if($result == null){
         return response()->json(["Not Found"=>"No product found"], 404);
        }else{
            $result->delete();
            return response()->json(["deleted" => "One product has been deleted"]);
        }
    }catch(\Exception $e){
        return response()->json(["Error"=>"Server Error"], 500);
    }
}

public function GetCategoryProducts(Request $request, $categoryId){
    try{
        $result = Product::where('categoryId', $categoryId)->get()->sortDesc();
        if($result->count() == 0){
           return response()->json(["No Entries" => "There are no products under this category."], 404); 
        }else{         
            $res = array();
            foreach($result as $row){
                $res[] = array("id" => $row->id,
                "name" => $row->name,
                "category" => $row->categoryId,
                "quantity" => $row->quantity);
            }
            return response()->json($res);
        }
    }catch(\Exception $e){
        return response()->json(["Error"=>"Server Error"], 500);
    }
}

}
