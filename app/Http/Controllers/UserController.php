<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;
use Knightu\Helpers\ApiResponse;
use App\Models\AuthImage;
use Illuminate\Support\Facades\Validator;
use App\Models\UserFollow;
use Illuminate\Database\QueryException;
use App\Models\CateFollow;

class UserController extends Controller
{
    /**
     * obviously, sign up
     */
    public function store(SignUpRequest $request){
        //validating in the SignUpRequest.php
        //Storing the user
        $user = new User;
        $user->fullname = $request->fullname;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->otherphone = $request->otherphone;
        $user->address = $request->address;
        $user->bio = $request->bio;
        $user->birthdate = $request->birthdate;
        $user->username = $request->username;
        $user->publicphone = $request->publicphone;
        //storing the files:
        $profileImage = $request->file('profileimage');
        $idimage1 = $request->file('idimage1');
        $idimage2 = $request->file('idimage2');
        $auths = new AuthImage;

        try{
            $user->profileImage = $profileImage->store('profileImages');
            $user->save();
            $id1path = $idimage1->store('authImages');
            $id2path = $idimage2->store('authImages');
            $idauthpath = $request->file('authimage')->store('authImages');
            $auths->user_id = $user->id;
            $auths->id1path = $id1path;
            $auths->id2path = $id2path;
            $auths->idauthpath = $idauthpath;
            $auths->save();

           
            return ApiResponse::success(message: 'successfully created user instance. please wait for the verifying email (about three days.)');
        }catch(Exception $e){
            return ApiResponse::error([$e->__toString()], 'Unexpected Error', 500);
        }
    }

    /**
     * this function returns info about sign-up 
     */
    public function store_params(){
        return ApiResponse::success([
            'url' => 'http://127.0.0.1:8000/api/signup',
            'method' => 'POST',
            'params' => [
                'fullname' => 'required|three words (english or arabic)',
                'email' => 'required|email',
                'phone' => 'required|regex:/[0-9]+',
                'otherphone' => 'regex:/[0-9]+',
                'idimage1' => 'required|image',
                'idimage2' => 'required|image',
                'address' => 'required|string',
                'bio' => 'required|string',
                'authimage' => 'required|image',
                'birthdate' => 'required|date',
                'username' => 'required|string',
                'publicphone' => 'required|regex:/[0-9]+',
                'profileimage' => 'required|image',
                'password' => 'required|string'   
            ]
        ]);
    }

    /**
     * just the login. nothing special...
     */
    public function login(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        if($validator->fails()) return ApiResponse::validationError([$validator->errors()]);
        //authenticating
        $user = User::where('email', $request->email)->firstOr(callback: function(){
            return ApiResponse::error(message: 'email not found', status:404);
        });
        if(empty($user->email_verified_at)) return ApiResponse::error(message: 'your email is not verified', status:403);
        if(Hash::check($request->password, $user->password)) return ApiResponse::error(message: 'wrong password', status:403);

        
        return ApiResponse::success(
            data: ['bearer token' => $user->createToken($user->username)->plainTextToken],
            message: 'successfully retrieved the user. this is the api token for you.'
        );
    }

    /**
     * the name explains everything
     */
    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        return ApiResponse::success(message: "successfully logged-out");
    }

    public function followUser(Request $request, string $user_id){
        $follow = new UserFollow;
        $follow->user1_id = $request->user()->id;
        $follow->user2_id = $user_id;
        try{
            $follow->save();
            return ApiResponse::success(message: "successfully followed the account");
        }catch(QueryException $e){
            return ApiResponse::error(
                ['error' => $e->__toString()],
                "unable to follow the account due to a server error",
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
            return ApiResponse::success(message: "successfully followed the category");
        }catch(QueryException $e){
            return ApiResponse::error(
                ['error'=> $e->__toString()],
                'unable to follow this category due to a server error',
                500
            );
        }
    }
}
