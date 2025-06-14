<?php

namespace App\Http\Controllers;

use App\Models\HotspotUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotspotUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hotspotUser = Auth::user()->hotspotUser;
        return response($hotspotUser);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $hotspotUsers = $request->all(); // Ambil data dari front-end

    foreach ($hotspotUsers as $user) {
        HotspotUser::updateOrCreate(
            ['mac' => $user['mac']], // Gunakan MAC Address sebagai unik
            [
                'server' => $user['server'],
                'user' => $user['user'],
                'address' => $user['address'],
                'uptime' => $user['uptime'],
                'bytes_in' => $user['bytes_in'],
                'bytes_out' => $user['bytes_out'],
                'time_left' => $user['time_left'],
                'login_by' => $user['login_by'],
                'comment' => $user['comment'],
            ]
        );
    }

    return response()->json(['message' => 'Data hotspot berhasil disimpan'], 200);
}

    /**
     * Display the specified resource.
     */
    public function show(HotspotUser $hotspotUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HotspotUser $hotspotUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HotspotUser $hotspotUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HotspotUser $hotspotUser)
    {
        //
    }
}
