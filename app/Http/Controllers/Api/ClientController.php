<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index()
    {
        return Client::all();
    }

    public function show($id)
    {
        return Client::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'auth_user_id' => 'nullable|string',
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email|max:320',
        ]);

        $data['id'] = Str::uuid()->toString();

        $client = Client::create($data);

        return response()->json($client, 201);
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $data = $request->validate([
            'auth_user_id' => 'nullable|string',
            'name'         => 'sometimes|required|string|max:255',
            'email'        => 'nullable|email|max:320',
        ]);

        $client->update($data);

        return $client;
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->noContent();
    }
}
