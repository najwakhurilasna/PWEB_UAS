<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getWeather()
    {
        try {
            $banyuwangi = $this->fetchWeather('Banyuwangi');
            $bali = $this->fetchWeather('Bali');

            return response()->json([
                'success' => true,
                'banyuwangi' => $banyuwangi,
                'bali' => $bali,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function fetchWeather($city)
    {
        $url = "https://wttr.in/{$city}?format=j1";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            $current = $data['current_condition'][0];
            return [
                'city' => $city,
                'temperature' => $current['temp_C'],
                'description' => $current['weatherDesc'][0]['value'],
            ];
        }

        return ['city' => $city, 'temperature' => 'N/A', 'description' => 'Tidak tersedia'];
    }
}
