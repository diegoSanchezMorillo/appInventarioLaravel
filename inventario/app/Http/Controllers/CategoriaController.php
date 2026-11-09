<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Requests\CategoriaRequest;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('productos')
            ->orderBy('nombre')
            ->paginate(15);

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create', ['categoria' => new Categoria()]);
    }

    public function store(CategoriaRequest $request)
    {
        Categoria::create($request->validated());

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(CategoriaRequest $request, Categoria $categoria)
    {
        $datos = $request->validated();

        // Si ya tiene productos, el prefijo no se toca aunque lo hayan manipulado en el formulario
        if ($categoria->tieneProductos()) {
            unset($datos['prefijo']);
        }

        $categoria->update($datos);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->tieneProductos()) {
            return back()->with('error', 'No se puede eliminar una categoría que tiene productos.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada.');
    }
}
