<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Productos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Lista de Productos en Inventario</h3>
                    
                    {{-- Botón para agregar un nuevo producto --}}
                    <div class="mb-4 flex justify-end">
                        <a href="#" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                            Nuevo Producto
                        </a>
                    </div>

                    {{-- Tabla de Productos --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre del Producto
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- Filas de ejemplo --}}
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">101</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Laptop UltraBook X1</td>
                                    <td class="px-6 py-4 whitespace-nowrap">$1250.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap">25</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                        <a href="#" class="text-red-600 hover:text-red-900">Eliminar</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">102</td>
                                    <td class="px-6 py-4 whitespace-nowrap">Monitor Curvo 27"</td>
                                    <td class="px-6 py-4 whitespace-nowrap">$350.50</td>
                                    <td class="px-6 py-4 whitespace-nowrap">150</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                        <a href="#" class="text-red-600 hover:text-red-900">Eliminar</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>