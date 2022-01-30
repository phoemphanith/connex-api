<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/l', function () {
    return view('welcome');
});

Route::get('/', function () {
    $query = http_build_query([
        'client_id' => '3',
        'redirect_uri' => 'http://127.0.0.1:8001/callback',
        'response_type' => 'code',
        'scope' => 'access-patient-status'
    ]);

    return redirect('http://127.0.0.1/oauth/authorize?' . $query);
});

Route::get('/callback', function (Request $request) {
    $response = (new GuzzleHttp\Client)->post('http://127.0.0.1/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => '3',
            'client_secret' => 'SXvlEoahPIFq39gxnkFN909KlnX8O1wB6xb6zXHD',
            'redirect_uri' => 'http://127.0.0.1:8001/callback',
            'code' => $request->code,
        ]
    ]);

    session()->put('token', json_decode((string) $response->getBody(), true));

    return redirect('/user');
});

Route::get('/user', function () {
    if (!session()->has('token')) {
        return redirect('/');
    }

    $response = (new GuzzleHttp\Client)->get('http://127.0.0.1/api/user', [
        'headers' => [
            'Authorization' => 'Bearer ' . Session::get('token.access_token')
        ]
    ]);

    return json_decode((string) $response->getBody(), true);
});
