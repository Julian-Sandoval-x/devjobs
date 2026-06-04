<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vacante;

new class extends Component
{
    use WithPagination;
    protected $listeners = ['eliminarVacante'];

    public function with() {
        $vacantes = Vacante::where('user_id', auth()->id())->paginate(10);
        return compact('vacantes');
    }

    
    public function eliminarVacante(Vacante $vacante) {
        $vacante->delete();
    }
};
?>

<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

        @forelse($vacantes as $vacante)
            <div class="p-6 text-gray-900 border-b border-gray-200 md:flex md:justify-between md:items-center">

                <div class="leading-10">
                    <a href="{{ route('vacantes.show', $vacante->id) }}" class="font-bold text-xl">
                        {{ $vacante->titulo }}
                    </a>

                    <p class="text-sm text-gray-600 font-bold">
                        {{ $vacante->empresa }}
                    </p>

                    <p class="text-sm text-gray-500">
                        Último día: {{ $vacante->ultimo_dia->format('d/m/Y') }}
                    </p>
                </div>

                <div class="flex flex-col md:flex-row gap-3 items-stretch mt-5 md:mt-0">
                    <a 
                        href="{{ route('candidatos.index', $vacante) }}"
                        class="bg-slate-800 py-2 px-4 rounded-lg text-white text-xs font-bold uppercase text-center"
                    >
                      {{ $vacante->candidatos->count() }}  Candidatos
                    </a>

                    <a 
                        href="{{ route('vacantes.edit', $vacante->id) }}"
                        class="bg-blue-800 py-2 px-4 rounded-lg text-white text-xs font-bold uppercase text-center"
                    >
                        Editar
                    </a>

                    <button
                        wire:click="$dispatch('mostrarAlerta', [{{ $vacante->id }}])"
                        class="bg-red-600 py-2 px-4 rounded-lg text-white text-xs font-bold uppercase text-center"
                    >
                        Eliminar
                    </button>
                </div>

            </div>
        @empty
            <p class="p-3 text-sm text-center text-gray-600">
                No hay vacantes para mostrar
            </p>
        @endforelse

    </div>

    <div class="mt-10">
        {{ $vacantes->links() }}
    </div>
</div>


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

        Livewire.on('mostrarAlerta', vacante_id => {
           Swal.fire({
            title: "¿Eliminar Vacante?",
            text: "Una vacante eliminada no se puede recuperar",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, ¡Eliminar!",
            cancelButtonText: "No, ¡Cancelar!"
            }).then((result) => {
                // Eliminar la vacante
                Livewire.dispatch('eliminarVacante', vacante_id);

                if (result.isConfirmed) Swal.fire({
                    title: "¡Vacante Eliminada!",
                    text: "La vacante ha sido eliminada.",
                    icon: "success"
                });
            }); 
        });
    </script>
@endpush