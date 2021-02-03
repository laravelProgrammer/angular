<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',

        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()->all()],422);
        }else{
            $user =  new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json(['message' => 'User registered successfully'],200);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required',

        ]);
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()->all()]);
        }else{
            $user =  User::where('email', $request->email)->where('password', $request->password)->first();
          //  $password = decrypt($user->password);
            if($user){
                return response()->json(['user' => $user],200);
            }
                return response()->json(['message' => 'Wrong email or password'],200);
        }
    }
}
