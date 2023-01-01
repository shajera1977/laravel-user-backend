<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller

{
     function register(Request $req)

    {
        $user = new User;
        $user->name=$req->input('name');
        $user->email=$req->input('email');
        $user->password=Hash::make($req->input('password'));
        $user->remember_token = Str::random(14);
        $user->save();

        if(! $user->save()) {
            return response()->json('error');
        }
        else {
            $details = [
                'title' => 'Congratulation !',                                 
                'body' => 'Copy and Paste This Code On Validation Page : ',
                'Token' =>  $user->remember_token              
            ];
           
            Mail::to('rajeshbveer@gmail.com')->send(new \App\Mail\MyTestMail($details));

            return response()->json("success");
        }

    }

    function emailvaladation(Request $req)

    {
        
        $token=$req->input('token');
        
        $validtoken = User::where('remember_token', '=',$token)->first();

        if ($validtoken) {
            User::where('remember_token', $token)->update(array('email_verified_at' => '1'));            
            return response()->json("valid");
        } else {
            return response()->json("invalid");
        }
        
    }

    function login(Request $request)

    {
                 
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return response()->json('invalidlogin');
        }
        if (!Hash::check($password, $user->password)) {
            return response()->json('invalidlogin');
        }

        return response()->json('login');
           

    }

}
