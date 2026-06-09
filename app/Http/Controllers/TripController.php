<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    // HAPUS __construct() - middleware sudah diatur di routes!

    public function index()
    {
        $trips = Trip::latest()->paginate(10);
        return view('admin.trips.index', compact('trips'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        return view('admin.trip-form', ['trip' => null]);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'nama' => 'required|min:3',
            'lokasi' => 'required|in:Banyuwangi,Bali',
            'deskripsi' => 'required',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $data = $request->all();
        if ($request->fasilitas) {
            $data['fasilitas'] = json_encode(explode(',', $request->fasilitas));
        }

        Trip::create($data);
        return redirect()->route('detail')->with('success', 'Trip berhasil ditambahkan!');
    }

    public function edit($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $trip = Trip::findOrFail($id);
        return view('admin.trip-form', compact('trip'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $trip = Trip::findOrFail($id);

        $request->validate([
            'nama' => 'required|min:3',
            'lokasi' => 'required|in:Banyuwangi,Bali',
            'deskripsi' => 'required',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'required|integer|min:1',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        $data = $request->all();
        if ($request->fasilitas) {
            $data['fasilitas'] = json_encode(explode(',', $request->fasilitas));
        }

        $trip->update($data);
        return redirect()->route('detail')->with('success', 'Trip berhasil diupdate!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $trip = Trip::findOrFail($id);
        $trip->delete();
        return redirect()->route('detail')->with('success', 'Trip berhasil dihapus!');
    }
}
