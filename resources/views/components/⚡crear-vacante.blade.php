<?php

use App\Models\Salario;
use App\Models\Categoria;
use App\Models\Vacante;
use Livewire\WithFileUploads;
use Livewire\Component;

new class extends Component
{
    // Consultar base de datos
    public $salarios;
    public $categorias;

    // Propiedades del form
    public $titulo;
    public $salario;
    public $categoria;
    public $empresa;
    public $ultimo_dia;
    public $descripcion;
    public $imagen;

    use WithFileUploads;

    protected $rules = [
        'titulo' => 'required|string',
        'salario' => 'required',
        'categoria' => 'required',
        'empresa' => 'required|string',
        'ultimo_dia' => 'required|date',
        'descripcion' => 'required|string',
        'imagen' => 'required|image|mimes:jpg,png,jpeg|max:2048',
    ];


    public function mount()
    {
        $this->salarios = Salario::all();
        $this->categorias = Categoria::all();
    }

    public function crearVacante() {
       $datos = $this->validate();

        // Almacenar la imagen
        $imagen = $this->imagen->store('vacantes', 'public');
        $datos['imagen'] = $this->imagen->hashName();
        
        // Crear la vacante
        Vacante::create([
            'titulo' => $datos['titulo'],
            'salario_id' => $datos['salario'],
            'categoria_id' => $datos['categoria'],
            'empresa' => $datos['empresa'],
            'ultimo_dia' => $datos['ultimo_dia'],
            'descripcion' => $datos['descripcion'],
            'imagen' => $datos['imagen'],
            'user_id' => auth()->user()->id,
        ]);

        // Mostrar mensaje de éxito
        session()->flash('mensaje', 'La vacante se publico correctamente');

        // Redireccionar
        return redirect()->route('vacantes.index');

    }

};
?>

<form action="" class="md:w-1/2 space-y-5" wire:submit.prevent='crearVacante'>
    <div>
        <x-input-label for="titulo" :value="__('Titulo Vacante')" />
        <x-text-input 
            id="titulo" 
            class="block mt-1 w-full" 
            type="text" 
            wire:model.live="titulo" 
            :value="old('titulo')"
            placeholder="Titulo Vacante"/>
        
        @error('titulo')
            <livewire:mostrar-alerta :message="$message"/>
        @enderror
    </div>

    <div>
        <x-input-label for="salario" :value="__('Salario Mensual')" />
        
        <select
                id="salario"
                wire:model.live="salario"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
            >
            <option value="">-- Seleccione --</option>
            @foreach ($salarios as $salario)
                <option value="{{ $salario->id }}">{{ $salario->salario }}</option>
            @endforeach
        </select>

        @error('salario')
            <livewire:mostrar-alerta :message="$message"/>
        @enderror
    </div>

    <div>
        <x-input-label for="categoria" :value="__('Categoria')" />
        
        <select
                id="categoria"
                wire:model.live="categoria"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
            >
            <option value="">-- Seleccione --</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->categoria }}</option>
            @endforeach
        </select>
        @error('categoria')
            <livewire:mostrar-alerta :message="$message"/>
        @enderror
    </div>

    <div>
        <x-input-label for="empresa" :value="__('Empresa')" />
        <x-text-input 
            id="empresa" 
            class="block mt-1 w-full" 
            type="text" 
            wire:model.live="empresa"
            :value="old('empresa')"
            placeholder="Nombre de la Empresa"/>
        @error('empresa')
            <livewire:mostrar-alerta :message="$message"/>
        @enderror
    </div>

    <div>
        <x-input-label for="ultimo_dia" :value="__('Ultimo día para postularse')" />
        <x-text-input 
            id="ultimo_dia" 
            class="block mt-1 w-full" 
            type="date" 
            wire:model.live="ultimo_dia"
            :value="old('ultimo_dia')"/>
        @error('ultimo_dia')
            <livewire:mostrar-alerta :message="$message"/>
        @enderror
    </div>

    <div>
        <x-input-label for="descripcion" :value="__('Descripción Puesto')" />
        <textarea 
            wire:model.live="descripcion"
            placeholder="Descripción general del puesto, experiencia"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full h-72"
            ></textarea>
        @error('descripcion')
            <livewire:mostrar-alerta :message="$message"/>
        @enderror
    </div>

    <div>
        <x-input-label for="imagen" :value="__('Imagen')" />
        <input
            id="imagen" 
            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
            type="file" 
            accept="image/*"
            wire:model="imagen" />

        <div class="my-5 w-80">
            @if($imagen)
                Imagen:
                <img src="{{ $imagen->temporaryUrl() }}">
            @endif
        </div>


        @error('imagen')
            <livewire:mostrar-alerta :message="$message"/>
        @enderror
    </div>

    <x-primary-button class="w-full justify-center">
        Crear Vacante
    </x-primary-button>

</form>