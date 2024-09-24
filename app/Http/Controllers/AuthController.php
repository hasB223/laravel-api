<?php

namespace App\Http\Controllers;

use App\Models\Lookup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use PhpParser\Node\Stmt\TryCatch;

class AuthController extends Controller
{
    // Method to generate MDI token
    public function generateMdiToken()
    {
        $apiUrl = 'https://api.mdi.gov.my/apix2/auth/login';
        $clientId = env('MDI_CLIENT_ID');
        $secretKey = env('MDI_SECRET_KEY');

        try {
            // First API request to get the auth token
            $response = Http::withHeaders([
                'mdi-clientid' => $clientId,
                'mdi-secretkey' => $secretKey
            ])->post($apiUrl);

            $data = $response->json();

            // Check if the JSON response is valid
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from API');
            }

            // Check if the API authentication is successful
            if ($data['status'] === 1) {
                // Save the token to the session
                $mdiToken = $data['token'];
                Session::put('mdi-token', $mdiToken);
                return $mdiToken;
            } else {
                throw new \Exception('Failed to authenticate with the API');
            }
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'error' => 'Error during API request',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Method to generate EI session using the MDI token
    public function generateEiSession()
    {
        // Retrieve the MDI token from session
        $mdiToken = Session::get('mdi-token');
        if (!$mdiToken) {
            // If the MDI token doesn't exist, generate it
            $mdiToken = $this->generateMdiToken();
        }

        $loginUrl = 'https://api.mdi.gov.my/apix2/eiservices/login';
        $clientId = env('MDI_CLIENT_ID');
        $username = env('MDI_USERNAME');
        $password = env('MDI_PASSWORD');

        $bodyData = [
            'username' => $username,
            'password' => $password
        ];

        try {
            // Second API request for login using the MDI token
            $response = Http::withHeaders([
                'mdi-clientid' => $clientId,
                'mdi-token' => $mdiToken
            ])->post($loginUrl, $bodyData);

            $loginData = $response->json();

            // Check if the login is successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from login API');
            }

            if ($loginData['status'] === 1) {
                // Save the session information from login response
                Session::put('ei-session', $loginData['session']);
                return $loginData['session'];
            } else {
                throw new \Exception('Login failed');
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error during login API call',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function showSession()
    {
        // Retrieve all session data as an associative array
        $sessionData = Session::all();

        // Decode nested JSON strings
        foreach ($sessionData as $key => $value) {
            if (is_string($value) && $this->isJson($value)) {
                $sessionData[$key] = json_decode($value, true);
            }
        }

        // Return the JSON response with pretty print formatting
        return response()->json($sessionData, 200, [], JSON_PRETTY_PRINT);
    }

    // Helper function to check if a string is a valid JSON
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
