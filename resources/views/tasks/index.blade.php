<x-app-layout>
    <div class="container py-3">
        <h2 class="mb-4">Manajemen Task</h2>

        <button id="btn-add" class="btn btn-primary mb-3">+ Tambah Task</button>

        <table id="tasks-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>User</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="task-form">
                    @csrf
                    <input type="hidden" id="task_id" name="task_id">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Judul</label>
                            <input type="text" id="title" name="title" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Deskripsi</label>
                            <textarea id="description" name="description" class="form-control"></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="to-do">To Do</option>
                                <option value="in-progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <span class="spinner-border spinner-border-sm me-1 d-none" id="btn-loader"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" />

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
            ]
        });

        $('#btn-add').on('click', function(){
            $('#task_id').val('');
            $('#task-form')[0].reset();
            $('#modal-title').text('Tambah Task');

            let modal = new bootstrap.Modal(document.getElementById('taskModal'));
            modal.show();
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
                    $('#task-form button[type=submit]').prop('disabled', true);
                },
                complete: function(){
                    $('#btn-loader').addClass('d-none');
                    $('#task-form button[type=submit]').prop('disabled', false);
                },
                success: function(){
                    let modalEl = document.getElementById('taskModal');
                    let modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: id ? 'Task diperbarui!' : 'Task ditambahkan!',
                        showConfirmButton: false,
                        timer: 1300
                    });

                    table.ajax.reload(null, false);
                },
                error: function(xhr){
                    if(xhr.status === 422){
                        let errors = xhr.responseJSON.errors;
                        let messages = Object.values(errors).flat().join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi gagal',
                            html: messages,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan server.'
                        });
                    }
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

                let modal = new bootstrap.Modal(document.getElementById('taskModal'));
                modal.show();
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
            }).then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        url: '/tasks/' + id,
                        type: 'DELETE',
                        data: {_token: '{{ csrf_token() }}'},
                        success: function(){
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil dihapus!',
                                showConfirmButton: false,
                                timer: 1000
                            });
                            table.ajax.reload(null, false);
                        },
                        error: function(){
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal menghapus task'
                            });
                        }
                    });
                }
            });
        });
    });
    </script>
    @endpush
</x-app-layout>