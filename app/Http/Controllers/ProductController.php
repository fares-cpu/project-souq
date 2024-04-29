<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\ProductUploadRequest;
use Exception;
use Knightu\Helpers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Buy;
use App\Models\Rate;
use App\Models\Report;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Comment;
use App\Models\UserFollow;
use App\Models\CateFollow;

class ProductController extends Controller
{
    
    public function store(ProductUploadRequest $request){
        //validation in form request
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->category = $request->category;
        $product->price = $request->price;
        $product->instock = $request->instock;
        $product->mainimage = $request->file('mainimage')->store('productsImages');
        $product->image2 = $request->file('image2')->store('productsImages');
        $product->image3 = $request->file('image3')->store('productsImages');
        $product->image4 = $request->file('image4')->store('productsImages');
        $product->user_id = Auth::user()->id;
        try{
            $product->save();
            return ApiResponse::success('successfully uploaded the file');
        }catch(Exception $e){
            return ApiResponse::error();
        }
    }

    public function all(){
        $all = Product::select(['name', 'mainimage', 'price', 'rate'])->where('instock', '>', 0)->get();
        $data = "ProductResource::collection($all)";
        return ApiResponse::success($data, 'successfully retrieved all products');
    }

    public function show(string $product_id){
        $product = Product::findOr(id: $product_id, callback: function(){
            return ApiResponse::error(
                message: "Product not found",
                status: 404
            );
        })->first()->get();
        $data = '//new ProductResource($product)';
        return ApiResponse::success($data, "successfully retrieved the product");
    }

    public function buy(Request $request, string $product){
        //validation
        $validator = Validator::make($request->all(), [
            'amount' => 'numeric|gt:0',
            'order'  => 'boolean|required'
        ]);
        if($validator->fails()){
            return ApiResponse::validationError($validator->errors()->toArray());
        }
        if($request->has('amount')) $amount = $request->amount;
        else $amount = 1;
        $product_really = Product::findOr(id: $product, callback: function(){
            return ApiResponse::error(
                ['error' => 'NOT FOUND'],
                'not found',
                404
            );
        })->first();

        $customer = Auth::user()->id;
        $seller = $product_really->value('user_id');

        if($customer == $seller) return ApiResponse::error(
            ['error' => 'you can\'t buy your own products'],
            'UNAUTHORIZED',
            401
        );

        $buy = new Buy;
        $buy->product_id = $product;
        $buy->customer_id = Auth::user()->id;
        $buy->seller_id = $product_really->value('user_id');
        $buy->order = $request->order;
        $buy->done = false;

        try{
            $product_really->decrement('instock', $amount);
            $product_really->save();
            $buy->save();
            return ApiResponse::success(
                message: 'successfully bought the product.'
            );
        }catch (Exception $e){
            return ApiResponse::error(
                ['error' => $e->getMessage()],
                'Server Error',
                500
            );
        }
    }

    public function update(ProductUpdateRequest $request, string $product_id){
        $product = Product::findOr($product_id, function (){
            return ApiResponse::notFound(['notFound'=>'product not found']);
        });
        if($request->user()->cannot('update', $product)){
            return ApiResponse::unauthorized([
                "auth" => "cannot update a product that belongs to another user"
            ]);
        }
        foreach($request->all() as $key => $value){
            $product->$key = $value;
        }
        try{
            $product->save();
        }catch(Exception $e){
            return ApiResponse::error(
                ['error' => $e->__toString()],
                'try again',
                500
            );
        }
    }
    public function rate(Request $request, string $product_id){
        $product = Product::findOr($product_id, function(){
            return ApiResponse::notFound(['error' => 'product was not found. please try again']);
        });
        if($request->user()->cannot('rate', $product))
            return ApiResponse::Unauthorized([
                'auth' => 'cannot rate a product if you have not bought it yet'
            ]);
        //validation
        $validator = Validator::make($request->all(), [
            'rate' => 'required|numeric|min:1|max:5'
        ]);
        if($validator->fails())
            return ApiResponse::validationError($validator->errors()->toArray());
        //there's gotta be a rates table in this case
        $rate = new Rate;
        $rate->user_id = $request->user()->id;
        $rate->product_id = $product_id;
        $rate->value = $request->rate;
        try{
            $rate->save();
            return ApiResponse::success(message: 'successfully rated this prduct');
        }catch(QueryException $e){
            return ApiResponse::error(
                ['error'=>$e->__toString()],
                'unable to rate this product due to server error',
                500
            );
        }
    }
    public function report(Request $request, string $product_id){
        //validation
        $validator = Validator::make($request->all(),[
            'title' => 'required|string' ,
            'body' => 'required|string'
        ]);
        if($validator->fails())
            return ApiResponse::ValidationError($validator->errors()->toArray());
        $product = Product::FindOr($product_id, function (){
            return ApiResponse::notFound(['error'=> 'product was not found']);
        });
        if($request->user()->cannot('report', $product))
            return ApiResponse::Unauthorized(['auth' => 'you cannot rate a product you have not bought yet']);
        $report = new Report;
        $report->user_id = $request->user()->id;
        $report->product_id = $product_id;
        $report->title = $request->title;
        $report->body = $request->body;
        try{
            $report->save();
            return ApiResponse::success(message: 'reported the product successfully');
        }catch(QueryException $e){
            return ApiResponse::error(
                ['error'=>$e->__toString()],
                'unable to report this product due to a server error',
                500
            );
        }
    }
    public function like(Request $request, string $product_id){
        $like = new Like;
        $like->user_id = $request->user()->id;
        $like->product_id = $product_id;
        try{
            $like->save();
            return ApiResponse::success(message: 'liked the product');
        }catch(QueryException $e){
            return ApiResponse::error(
                ['error' => $e->__toString()],
                'unable to like this product due to a server error',
                500
            );
        }
    }
    public function comment(Request $request, string $product_id){
        //validation
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string'
        ]);
        if($validator->fails())
            return ApiResponse::ValidationError($validator->errors()->toArray());
        $comment = new Comment;
        $comment->user_id = $request->user()->id;
        $comment->product_id = $product_id;
        $comment->body = $request->comment;
        try{
            $comment->save();
            return ApiResponse::success(message: 'successfully commented');
        }catch(QueryException $e){
            return ApiResponse::error(
                ['error' => $e->__toString()],
                'unable to comment on this product due to a server error',
                500
            );
        }
    }

    public function followUser(Request $request, string $user_id){
        $follow = new UserFollow;
        $follow->user1_id = $request->user()->id;
        $follow->user2_id = $user_id;
        try{
            $follow->save();
            return ApiResponse::success(message: "successfully followed the user");
        }catch(QueryException $e){
            return ApiResponse::error(
                ['error' => $e->__toString()],
                'unable to follow due to a server error',
                500
            );
        }
    }

    public function followCategory(Request $request, string $category){
        $follow = new CateFollow;
        $follow->user_id = $request->user()->id;
        $follow->category = $category;
        try{
            $follow->save();
            return ApiResponse::success(message: 'successfully followed the category');
        }catch(QueryException $e){
            return ApiResponse::error(
                ['error' => $e->__toString()],
                'unable to follow due to a server error',
                500
            );
        }
    }
    
}
