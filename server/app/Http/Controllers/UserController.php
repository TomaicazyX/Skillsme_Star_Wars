<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    //
    public function signin(Request $request){
        $email = $request->email;
        $password = $request->password;
        $user = User::where('email','=',$email)->first();
        if($user == null){
            return response([
                'success' => false,
                'message' => 'No such user exists'
            ],401);
        }else{
            if(Hash::check($password,$user->password)){    
                session()->put('email',$user->email);
                session()->put('userId',$user->id);
                return response([
                    'success' => true,
                    'message' => 'User Logged in successfully',
                    'email'   => $user->email,
                    'userId'  => $user->id
                ],200);
            }else{
                return response([
                    'success' => false,
                    'message' => 'User password mismatched'
                ],401);
                
            }
        }
    }
    public function signup(Request $request){
        $email = $request->email;
        $password = $request->password;
        $confirmPassword = $request->confirmPassword;
        $ipAddress = $_SERVER["REMOTE_ADDR"];
        if($password != $confirmPassword){
            return response([
                'success' => false,
                'message' => 'Passwords do not match'
            ],401);
        }
        DB::beginTransaction();
        try{
            $user = User::create([
                'email' => $email,
                'password' => Hash::make($password),
                'ipaddress' => $ipAddress
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response([
                'success' => false,
                'message' => 'User not created successfully <br>' . $e->getMessage()
            ],401);
        }
        DB::commit();
        session()->put('email',$user->email);
        session()->put('userId',$user->id);
        return response([
            'success' => true,
            'message' => 'User created successfully',
            'email'   => $user->email,
            'userId'  => $user->id
        ],200);
    }
}
