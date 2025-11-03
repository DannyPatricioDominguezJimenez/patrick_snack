<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Calendario de Eventos y Tareas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Vista Mensual (Placeholder)</h3>
                    
                    {{-- Botón para agregar un nuevo evento --}}
                    <div class="mb-6 flex justify-end">
                        <a href="#" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                            Añadir Evento
                        </a>
                    </div>

                    {{-- Estructura Simple del Calendario (Placeholder) --}}
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <div class="flex justify-between items-center mb-4">
                            <button class="text-gray-700 hover:text-gray-900 font-bold">&lt; Anterior</button>
                            <h4 class="text-xl font-semibold">Octubre 2025</h4>
                            <button class="text-gray-700 hover:text-gray-900 font-bold">Siguiente &gt;</button>
                        </div>
                        
                        <div class="grid grid-cols-7 gap-1 text-center font-bold text-gray-600 mb-2">
                            <span>Dom</span>
                            <span>Lun</span>
                            <span>Mar</span>
                            <span>Mié</span>
                            <span>Jue</span>
                            <span>Vie</span>
                            <span>Sáb</span>
                        </div>

                        <div class="grid grid-cols-7 gap-1 text-center">
                            {{-- Días de la semana (ejemplo) --}}
                            @for ($i = 1; $i <= 31; $i++)
                                <div class="p-2 h-16 border rounded hover:bg-indigo-100 cursor-pointer 
                                    @if ($i == 21) bg-blue-500 text-white font-bold @endif
                                    @if ($i == 25) bg-green-200 text-green-800 @endif
                                ">
                                    {{ $i }}
                                    @if ($i == 21)
                                        <p class="text-xs">Entrega</p>
                                    @endif
                                    @if ($i == 25)
                                        <p class="text-xs text-green-800">Cierre</p>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>