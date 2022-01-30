<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        $image = Upload::saveFile('/user', $request->file('image'), null);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'username' => Hash::make($request->name),
            'image' => url('uploads/user/' . $image),
            'major' => $request->major,
            'interests' => $request->interests,
            'dob' => $request->dob,
            'bio' => request('bio', "")
        ]);

        $access_token_example = $user->createToken('PassportExample@Section.io')->accessToken;
        //return the access token we generated in the above step
        return response()->json([
            'user' => $user,
            'token' => $access_token_example
        ], 200);
    }

    /**
     * login user to our application
     */
    public function login(Request $request)
    {
        $login_credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (!auth()->attempt($login_credentials)) {
            return response()->json(['error' => 'UnAuthorised Access'], 401);
        }

        $user = User::find(Auth::user()->id);
        $token = $user->createToken('PassportExample@Section.io')->accessToken;

        return response()->json([
            "user" => $user,
            "token" => $token
        ]);
    }

    /**
     * This method returns authenticated user details
     */
    public function update(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $image = Upload::saveFile('/user', $request->file('image'), $user->image);
        $user->update([
            'name' => request('name', $user->name),
            'image' => $image,
            'major' => request('major', $user->major),
            'interests' => request('interests', $user->interests),
            'dob' => request('dob', $user->dob),
            'bio' => request('bio', $user->bio)
        ]);
        return response()->json(['user' => $user], 200);
    }
}
