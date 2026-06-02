<?php

namespace App\Http\Controllers;

use App\Models\Vacante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VacanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = Gate::inspect('viewAny', Vacante::class);
        if($response->denied()) {
            abort(403, $response->message());
        }
        return view('vacantes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(Gate::denies('create', Vacante::class)) {
            abort(403, 'No tienes permisos para crear vacantes');
        }
        return view('vacantes.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vacante $vacante)
    {
        return view('vacantes.show', compact('vacante'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacante $vacante)
    {
        $response = Gate::inspect('update', $vacante);
        if ($response->denied()) {
            abort(403, $response->message());
        }

        return view('vacantes.edit', compact('vacante'));
    }

    
}
