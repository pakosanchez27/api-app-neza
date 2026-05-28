@extends('layouts.app')
@section('title', 'Categorias eventos')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        .admin-shell {
            border: 1px solid rgba(97, 16, 42, 0.08);
            border-radius: 26px;
            background: #fff;
            box-shadow: 0 18px 40px rgba(97, 18, 50, 0.07);
        }

        .admin-table table.dataTable thead th {
            color: #6b5560;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .admin-table .dataTables_wrapper .dataTables_filter input,
        .admin-table .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e7d8dc;
            border-radius: 999px;
            padding: 0.45rem 0.85rem;
            background: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="admin-shell mb-5 overflow-hidden">
        <div class="bg-[linear-gradient(135deg,#2f1821,#61102a)] px-6 py-6 text-white">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#ffd175]">Catalogos</p>
                    <h2 class="mt-3 text-2xl font-semibold">Categorias eventos</h2>
                    <p class="mt-2 text-sm leading-7 text-white/78">Administra los registros de la tabla event_categories.</p>
                </div>
                <a href="{{ route('admin.catalogos.categorias-eventos.create') }}"
                    class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-[#63102a] shadow-[0_10px_24px_rgba(0,0,0,0.14)] transition hover:bg-[#fff2f5]">
                    Crear categoria
                </a>
            </div>
        </div>
    </div>

    <div class="admin-shell admin-table p-5">
        <div class="mb-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#8d6b10]">Listado</p>
            <h3 class="mt-2 text-lg font-semibold text-[#201815]">Categorias registradas</h3>
        </div>

        <div class="overflow-x-auto">
            <table id="tabla-categorias-eventos" class="display stripe hover w-full text-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Descripcion</th>
                        <th>Eventos relacionados</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorias as $categoria)
                        <tr>
                            <td>{{ $categoria->id }}</td>
                            <td>{{ $categoria->nombre ?: 'Sin nombre' }}</td>
                            <td>{{ $categoria->slug ?: 'Sin slug' }}</td>
                            <td>{{ $categoria->descripcion ?: 'Sin descripcion' }}</td>
                            <td>{{ number_format($categoria->eventos_count) }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.catalogos.categorias-eventos.edit', $categoria) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-sky-100 text-sky-700 transition hover:bg-sky-200"
                                        title="Editar" aria-label="Editar categoria">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L8.25 19.462 4.5 20.5l1.038-3.75L16.862 4.487Z" />
                                        </svg>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('admin.catalogos.categorias-eventos.destroy', $categoria) }}"
                                        class="form-delete-catalogo inline-flex"
                                        data-message="Se eliminara la categoria {{ $categoria->nombre ?: 'seleccionada' }}.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-700 transition hover:bg-rose-200"
                                            title="Eliminar" aria-label="Eliminar categoria">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 7.5h12m-9.75 0V6a1.5 1.5 0 0 1 1.5-1.5h4.5A1.5 1.5 0 0 1 15.75 6v1.5m-7.5 0v10.125A1.875 1.875 0 0 0 10.125 19.5h3.75a1.875 1.875 0 0 0 1.875-1.875V7.5M10.5 10.5v6m3-6v6" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            const successMessage = @json(session('success'));
            const errorMessage = @json(session('error'));

            $('#tabla-categorias-eventos').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[1, 'asc']]
            });

            if (successMessage) {
                Swal.fire({ icon: 'success', title: 'Operacion completada', text: successMessage, confirmButtonColor: '#63102a' });
            }

            if (errorMessage) {
                Swal.fire({ icon: 'error', title: 'No fue posible completar la accion', text: errorMessage, confirmButtonColor: '#63102a' });
            }

            $('.form-delete-catalogo').on('submit', function(event) {
                event.preventDefault();
                const form = this;

                Swal.fire({
                    icon: 'warning',
                    title: 'Eliminar registro',
                    text: form.dataset.message || 'Se eliminara este registro.',
                    showCancelButton: true,
                    confirmButtonText: 'Si, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#be123c',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
