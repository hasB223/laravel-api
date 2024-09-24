<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SemakNoInsolvensiController extends Controller
{

    public function showSemakInsolvensi()
    {
        return view('semakan');
    }
    public function semakInsolvensi(Request $request)
    {
        // Validate user input
        $request->validate([
            'noInsolvensi' => 'required|string|max:13',
        ]);

        // Prepare the API URL and headers
        $apiUrl = 'https://api.mdi.gov.my/apix2/eiservices/ansuran';
        $clientId = env('MDI_CLIENT_ID');
        $token = Session::get('mdi-token');
        $eiSession = Session::get('ei-session');

        // Prepare the API request body
        $body = [
            'searchno' => $request->input('noInsolvensi')
        ];

        try {
            // Make the API call
            $response = Http::withHeaders([
                'mdi-clientid' => $clientId,
                'mdi-token' => $token,
                'ei-session' => $eiSession,
            ])->post($apiUrl, $body);

            $data = $response->json();

            // Check for a valid JSON response
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from the API');
            }

            // Handle good and bad responses
            if ($data['status'] === 1) {
                // Good response - pass data to the view
                return redirect()->back()->with([
                    'success' => true,
                    'insolvencyno' => $data['insolvencyno'],
                    'name' => $data['name'],
                    'idno' => $data['idno'],
                    'amount' => $data['amount'],
                ]);
            } else {
                // Bad response - show error message
                return redirect()->back()->with([
                    'error' => true,
                    'insolvencyno' => $data['insolvencyno'],
                    'message' => $data['message'],
                ]);
            }
        } catch (\Exception $e) {
            // Handle exceptions
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
