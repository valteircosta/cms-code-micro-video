<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    private $rules = [
        "name" => "required|max:255",
        "is_active" => 'boolean'
    ];

    public function index()
    {
        return Genre::all();
    }

    public function store(Request $request)
    {
        /** Faz validaçã */
        $this->validate($request, $this->rules);
        /** Deve liberar inclusão em massa */
        $genre = Genre::create($request->all());
        $genre->refresh();
        return $genre;
    }

    public function show(Genre $genre)
    {
        return $genre;
    }

    public function update(Request $request, Genre $genre)
    {
        $this->validate($request, $this->rules);
        $genre->update($request->all());
        return $genre;
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return response()->noContent(); //204
    }
}
