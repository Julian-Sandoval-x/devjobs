<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Vacante;
use App\Notifications\NuevoCandidato;

new class extends Component
{
    use WithFileUploads;
    public $cv;
    public $vacante;

    protected $rules = [
        'cv' => 'required|mimes:pdf|max:2048',
    ];

    public function mount(Vacante $vacante) {
        $this->vacante = $vacante;
    }

    public function postularme() {
        $datos = $this->validate();

        // Almacenar el CV en el almacenamiento
        $cv = $this->cv->store('cv', 'public');
        $datos['cv'] = $this->cv->hashName() . '.pdf';

        // Crear el candidato a la vacante
        $this->vacante->candidatos()->create([
            'user_id' => auth()->user()->id,
            'cv' => $datos['cv'],
        ]);        

        // Crear notificacion y enviar el email
        $this->vacante->reclutador->notify(
            new NuevoCandidato(
                $this->vacante->id,
                $this->vacante->titulo,
                auth()->user()->id)
            );

        // Mostrar el usuario un mensaje de éxito
        session()->flash('mensaje', '¡Postulación realizada correctamente!');

        return redirect()->back();

    }
};
?>

<div class="bg-gray-100 p-5 mt-10 flex flex-col justify-center items-center">
    <h3 class="text-center text-2xl font-bold my-4">Postularme a esta vacante</h3>

    @if (session()->has('mensaje'))
        <p class="uppercase border border-green-600 bg-green-100 text-green-600 font-bold my-5 p-2 text-sm rounded-lg">
            {{ session('mensaje') }}
        </p>
    @else
        <form action="" class="w-96 mt-5" wire:submit.prevent='postularme'>

            <div class="mb-4">
                <x-input-label for="cv" :value="__('Currículum (PDF)')" />
                <x-text-input id="cv" type="file" accept=".pdf" wire:model='cv' class="block mt-1 w-full" />
            </div>

            @error('cv')

                <livewire:mostrar-alerta :message="$message" />

            @enderror

            <x-primary-button class="w-full justify-center">
                Postularme
            </x-primary-button>

        </form>
    @endif
</div>