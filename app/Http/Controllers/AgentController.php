<?php

namespace App\Http\Controllers;


use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    /**
     * Tampilkan daftar agent milik user yang sedang login.
     */
    public function index()
    {
        $agents = Auth::user()->agents; // Ambil semua agent milik user yang login
        return response()->json($agents);
    }
    public function AgentTransaction()
{
    $agents = Agent::with(['transaksi' => function ($query) {
        $query->with('detailTransaksi');
    }])->get();

    return response()->json([
        'status' => 'success',
        'data' => $agents,
    ]);
}


    /**
     * Simpan agent baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nomer_hp' => 'required|string|max:20',
            'email' => 'required|email|unique:agents,email',
        ]);

        $agent = Auth::user()->agents()->create([
            'nama' => $request->nama,
            'nomer_hp' => $request->nomer_hp,
            'email' => $request->email,
            'user_id' => Auth::id()
        ]);

        return response()->json(['message' => 'Agent berhasil ditambahkan', 'agent' => $agent], 201);
    }

    /**
     * Tampilkan detail agent.
     */
    public function show($id)
    {
        $agent = Auth::user()->agents()->with('transaksi.detailTransaksi')->find($id);

        if (!$agent) {
            return response()->json(['message' => 'Agent tidak ditemukan'], 404);
        }

        return response()->json($agent);
    }

    /**
     * Update data agent.
     */
    public function update(Request $request, $id)
    {
        $agent = Auth::user()->agents()->find($id);

        if (!$agent) {
            return response()->json(['message' => 'Agent tidak ditemukan'], 404);
        }

        $request->validate([
            'nama' => 'sometimes|string|max:100',
            'nomer_hp' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|unique:agents,email,' . $id,
        ]);

        $agent->update($request->all());

        return response()->json(['message' => 'Agent berhasil diperbarui', 'agent' => $agent]);
    }

    /**
     * Hapus agent.
     */
    public function destroy($id)
    {
        $agent = Auth::user()->agents()->find($id);

        if (!$agent) {
            return response()->json(['message' => 'Agent tidak ditemukan'], 404);
        }

        $agent->delete();
        return response()->json(['message' => 'Agent berhasil dihapus']);
    }
}
