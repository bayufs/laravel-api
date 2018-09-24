<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        if($user->save()) {
            $user->signin = [
                'href' => 'api/v1/user/signin',
                'method' => 'POST',
                'params' => 'email, password'
            ];

            // Response dalam bentuk array jika berhasil created data user
            $response = [
                'massage' => 'User Created',
                'user' => $user,
            ];

            return response()->json($response, 201);

        }
        // Response dalam bentuk array jika TIDAK berhasil created data user
        $response = [
            'massage' => 'An error occured',
        ];
        return response()->json($response, 404);



       
    }

    public function signin(Requet $request)
    {
        return 'It works';
    }
}
