<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Task') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ __('Daftar Task') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Kelola daftar tugas di bawah ini.') }}
                        </p>
                    </div>
                    <x-primary-button id="btn-add" class="bg-gray-800 hover:bg-gray-700">
                        + {{ __('Tambah Task') }}
                    </x-primary-button>
                </div>

                <div class="overflow-x-auto">

                    <table id="tasks-table"
                        class="min-w-full text-sm text-gray-700 border border-gray-200 rounded-lg text-center align-middle">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs border-b">
                            <tr>
                                <th class="px-3 py-2 border">ID</th>
                                <th class="px-3 py-2 border">Judul</th>
                                <th class="px-3 py-2 border">Status</th>
                                <th class="px-3 py-2 border">User</th>
                                <th class="px-3 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-semibold text-gray-800" id="modal-title">Tambah Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="task-form">
                    @csrf
                    <input type="hidden" id="task_id" name="task_id">
                    <div class="modal-body space-y-3">
                        <div>
                            <x-input-label for="title" :value="__('Judul')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="description" :value="__('Deskripsi')" />
                            <textarea id="description" name="description"
                                class="block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <div>
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="to-do">To Do</option>
                                <option value="in-progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <x-secondary-button type="button" data-bs-dismiss="modal">
                            {{ __('Batal') }}
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="btn-loader"></span>
                            {{ __('Simpan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" />

    <style>
        table.dataTable tbody tr:hover {
            background-color: #f9fafb !important;
        }

        table.dataTable td {
            vertical-align: middle;
        }

        table.dataTable td,
        table.dataTable th {
            text-align: center;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: none;
            border-radius: 0.375rem;
            padding: 0.4rem 0.8rem;
            background: transparent;
            color: #374151 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4f46e5 !important;
            /* indigo-600 */
            color: white !important;
            box-shadow: 0 0 3px rgba(79, 70, 229, 0.3);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #6366f1 !important;
            /* indigo-500 */
            color: white !important;
        }
    </style>

    <script>
        $(document).ready(function(){
            var table = $('#tasks-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('tasks.index') }}",
                columns: [
                    {data: 'id'},
                    {data: 'title'},
                    {data: 'status'},
                    {data: 'user', name: 'user.name'},
                    {data: 'action', orderable: false, searchable: false}
                ],
                language: {
                    paginate: {
                        previous: "&lt;",
                        next: "&gt;"
                    }
                },
                drawCallback: function() {
                    $('.dataTables_paginate .paginate_button').addClass('transition duration-150 ease-in-out');
                }
            });

            $('#btn-add').on('click', function(){
                $('#task_id').val('');
                $('#task-form')[0].reset();
                $('#modal-title').text('Tambah Task');
                new bootstrap.Modal(document.getElementById('taskModal')).show();
            });

            $('#task-form').on('submit', function(e){
                e.preventDefault();
                let id = $('#task_id').val();
                let method = id ? 'PUT' : 'POST';
                let url = id ? '/tasks/' + id : '/tasks';

                $.ajax({
                    url: url,
                    type: method,
                    data: $(this).serialize(),
                    beforeSend: function(){
                        $('#btn-loader').removeClass('d-none');
                    },
                    complete: function(){
                        $('#btn-loader').addClass('d-none');
                    },
                    success: function(){
                        bootstrap.Modal.getInstance(document.getElementById('taskModal')).hide();
                        Swal.fire({icon:'success',title:id?'Task diperbarui!':'Task ditambahkan!',timer:1300,showConfirmButton:false});
                        table.ajax.reload(null,false);
                    },
                    error: function(xhr){
                        Swal.fire({icon:'error',title:'Error',text:xhr.responseJSON?.message || 'Terjadi kesalahan server.'});
                    }
                });
            });

            $('#tasks-table').on('click', '.btn-edit', function(){
                let id = $(this).data('id');
                $.get('/tasks/' + id + '/edit', function(data){
                    $('#task_id').val(data.id);
                    $('#title').val(data.title);
                    $('#description').val(data.description);
                    $('#status').val(data.status);
                    $('#modal-title').text('Edit Task');
                    new bootstrap.Modal(document.getElementById('taskModal')).show();
                });
            });

            $(document).on('click', '.btn-delete', function(){
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus task ini?',
                    text: 'Data yang dihapus tidak bisa dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if(result.isConfirmed){
                        $.ajax({
                            url: '/tasks/' + id,
                            type: 'DELETE',
                            data: {_token: '{{ csrf_token() }}'},
                            success: function(){
                                Swal.fire({icon:'success',title:'Berhasil dihapus!',timer:1000,showConfirmButton:false});
                                table.ajax.reload(null,false);
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>