<?php

use Livewire\Component;
use App\Models\Salario;
use App\Models\Categoria;
use App\Models\Vacante;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;

new class extends Component
{
    public $vacante_id;
    public $salarios;
    public $categorias;
    public $titulo;
    public $salario;
    public $categoria;
    public $empresa;
    public $ultimo_dia;
    public $descripcion;
    public $imagen;
    public $imagen_nueva;

    protected $rules = [
        'titulo' => 'required|string',
        'salario' => 'required',
        'categoria' => 'required',
        'empresa' => 'required|string',
        'ultimo_dia' => 'required|date',
        'descripcion' => 'required|string',
        'imagen_nueva' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
    ];

    use WithFileUploads;

    public function mount(Vacante $vacante)
    {
        $this->vacante_id = $vacante->id;
        $this->salarios = Salario::all();
        $this->categorias = Categoria::all();
        $this->titulo = $vacante->titulo;
        $this->salario = $vacante->salario_id;
        $this->categoria = $vacante->categoria_id;
        $this->empresa = $vacante->empresa;
        $this->ultimo_dia = Carbon::parse($vacante->ultimo_dia)->format('Y-m-d');
        $this->descripcion = $vacante->descripcion;
        $this->imagen = $vacante->imagen;
        $this->imagen_nueva = null;

    }

    public function editarVacante() {
        $datos = $this->validate();

        // Si hay una nueva imagen
        if($this->imagen_nueva) {
            $imagen = $this->imagen_nueva->store('vacantes', 'public');
            $datos['imagen'] = $this->imagen_nueva->hashName() . '.' . $this->imagen_nueva->getClientOriginalExtension();
        }

        // Encontrar la vacante a editar
        $vacante = Vacante::find($this->vacante_id);

        // Asignar los nuevos valores
        $vacante->titulo = $datos['titulo'];
        $vacante->salario_id = $datos['salario'];
        $vacante->categoria_id = $datos['categoria'];
        $vacante->empresa = $datos['empresa'];
        $vacante->ultimo_dia = $datos['ultimo_dia'];
        $vacante->descripcion = $datos['descripcion'];
        $vacante->imagen = $datos['imagen'] ?? $vacante->imagen; // Si no hay nueva imagen, mantener la actual

        // Guardar los cambios
        $vacante->save();

        // Reedireccionar
        session()->flash('mensaje', 'La vacante se actualizo correctamente');
        return redirect()->route('vacantes.index');

    }
};
?>

<form action="" class="md:w-1/2 space-y-5" wire:submit.prevent='editarVacante'>
    <div>
        <x-input-label for="titulo" :value="__('Titulo Vacante')" />
        <x-text-input id="titulo" class="block mt-1 w-full" type="text" wire:model.live="titulo" :value="old('titulo')"
            placeholder="Titulo Vacante" />

        @error('titulo')
        <livewire:mostrar-alerta :message="$message" />
        @enderror
    </div>

    <div>
        <x-input-label for="salario" :value="__('Salario Mensual')" />

        <select id="salario" wire:model.live="salario"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
            <option value="">-- Seleccione --</option>
            @foreach ($salarios as $salario)
            <option value="{{ $salario->id }}">{{ $salario->salario }}</option>
            @endforeach
        </select>

        @error('salario')
        <livewire:mostrar-alerta :message="$message" />
        @enderror
    </div>

    <div>
        <x-input-label for="categoria" :value="__('Categoria')" />

        <select id="categoria" wire:model.live="categoria"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
            <option value="">-- Seleccione --</option>
            @foreach ($categorias as $categoria)
            <option value="{{ $categoria->id }}">{{ $categoria->categoria }}</option>
            @endforeach
        </select>
        @error('categoria')
        <livewire:mostrar-alerta :message="$message" />
        @enderror
    </div>

    <div>
        <x-input-label for="empresa" :value="__('Empresa')" />
        <x-text-input id="empresa" class="block mt-1 w-full" type="text" wire:model.live="empresa"
            :value="old('empresa')" placeholder="Nombre de la Empresa" />
        @error('empresa')
        <livewire:mostrar-alerta :message="$message" />
        @enderror
    </div>

    <div>
        <x-input-label for="ultimo_dia" :value="__('Ultimo día para postularse')" />
        <x-text-input id="ultimo_dia" class="block mt-1 w-full" type="date" wire:model.live="ultimo_dia"
            :value="old('ultimo_dia')" />
        @error('ultimo_dia')
        <livewire:mostrar-alerta :message="$message" />
        @enderror
    </div>

    <div>
        <x-input-label for="descripcion" :value="__('Descripción Puesto')" />
        <textarea wire:model.live="descripcion" placeholder="Descripción general del puesto, experiencia"
            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full h-72"></textarea>
        @error('descripcion')
        <livewire:mostrar-alerta :message="$message" />
        @enderror
    </div>

    <div>
        <x-input-label for="imagen" :value="__('Imagen')" />
        <input id="imagen"
            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
            type="file" 
            accept="image/*" 
            wire:model="imagen_nueva"
        />
        @error('imagen_nueva')
        <livewire:mostrar-alerta :message="$message" />
        @enderror

        <div class="my-5 w-80">
            <x-input-label :value="__('Imagen Actual')" />

            <img src="{{ asset('storage/vacantes/' . $imagen) }}" alt="{{ 'Imagen vacante ' . $titulo }}">
        </div>

        <div class="my-5 w-80">
            @if ($imagen_nueva)
                <x-input-label :value="__('Imagen Nueva')" />
                <img src=" {{ $imagen_nueva->temporaryUrl() }}">
            @endif
        </div>

    </div>

    <x-primary-button class="w-full justify-center">
        Guardar Cambios
    </x-primary-button>

</form>