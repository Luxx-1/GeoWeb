<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeoController extends Controller
{
    public function index()
    {
        // URL API BMKG untuk mengambil data gempa dirasakan terkini
        $url = 'https://data.bmkg.go.id/DataMKG/TEWS/gempadirasakan.json';

        try {
            // Mengambil data dari API BMKG
            $response = Http::get($url);

            // Memeriksa apakah respons berhasil
            if ($response->successful()) {
                // Mengambil data gempa dalam bentuk array
                $dataGempa = $response->json();

                // Mengambil data gempa dari key 'gempa'
                $gempaData = $dataGempa['Infogempa']['gempa'] ?? [];

                // Menampilkan data gempa di view main.index
                return view('main.index', compact('gempaData'));
            } else {
                // Jika gagal mengambil data, tampilkan pesan error
                return view('main.index')->with('error', 'Tidak dapat mengambil data gempa');
            }
        } catch (\Exception $e) {
            // Menangani jika ada kesalahan dalam mengambil data
            return view('main.index')->with('error', 'Terjadi kesalahan dalam mengambil data gempa');
        }
    }
}
