<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('app.login');
    }

    public function login(Request $request)
    {
        // Validate username and password
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Add this line
            try {
                Log::info('Attempting external API authentication with fixed credentials using multipart/form-data in Guzzle...');

                $client = new Client(['verify' => false]);

                $response = $client->post('https://cis-dev.del.ac.id/api/jwt-api/do-auth', [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'multipart' => [
                        [
                            'name' => 'username',
                            'contents' => 'johannes',
                        ],
                        [
                            'name' => 'password',
                            'contents' => 'Del@2022',
                        ],
                    ],
                ]);

                $body = $response->getBody()->getContents();
                $data = json_decode($body, true);

                Log::info('External API response:', ['response' => $data]);

                if ($data && isset($data['result']) && $data['result'] === true) {
                    // Store API token and user data in session
                    session(['api_token' => $data['token']]);
                    session(['user_api' => $data['user']]);
                    Log::info('External API authentication successful.');
                } else {
                    // Handle API authentication failure
                    $errorMessage = $data['error'] ?? 'Failed to authenticate with external API.';
                    Log::warning('External API authentication failed:', ['error' => $errorMessage]);
                    // Optionally, inform the user
                    session()->flash('warning', 'Logged in, but failed to authenticate with external API.');
                }
            } catch (\Exception $e) {
                Log::error('External API authentication error:', ['message' => $e->getMessage()]);
                session()->flash('warning', 'Logged in, but an error occurred while contacting external API.');
            }

            return redirect()->intended('/home');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }


    public function logout()
    {
        Auth::logout();
        session()->flush();
        session()->regenerate();
        return redirect('/login');
    }
}
