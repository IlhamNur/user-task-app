<x-app-layout>
    <div class="container py-3">
        <h2 class="mb-4">Manajemen Pengguna</h2>

        <button id="btn-add" class="btn btn-primary mb-3">+ Tambah Pengguna</button>

        <table id="users-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $user->id }}">Edit</button>
                        @if(auth()->id() !== $user->id)
                        <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $user->id }}">Hapus</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="user-form">
                    @csrf
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Nama</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-2 password-field">
                            <label class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Role</label>
                            <select id="role" name="role" class="form-select">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
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

    <script>
        $(function(){
        $('#btn-add').on('click', function(){
            $('#user-form')[0].reset();
            $('#user_id').val('');
            $('.password-field').show();
            $('#modal-title').text('Tambah Pengguna');
            let modal = new bootstrap.Modal(document.getElementById('userModal'));
            modal.show();
        });

        $('#user-form').on('submit', function(e){
            e.preventDefault();
            let id = $('#user_id').val();
            let method = id ? 'PUT' : 'POST';
            let url = id ? '/users/' + id : '/users';

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                beforeSend: function(){
                    $('#btn-loader').removeClass('d-none');
                    $('#user-form button[type=submit]').prop('disabled', true);
                },
                complete: function(){
                    $('#btn-loader').addClass('d-none');
                    $('#user-form button[type=submit]').prop('disabled', false);
                },
                success: function(){
                    location.reload();
                },
                error: function(xhr){
                    alert('Error: ' + xhr.responseJSON?.message);
                }
            });
        });

        $(document).on('click', '.btn-edit', function(){
            let id = $(this).data('id');
            $.get('/users/' + id + '/edit', function(data){
                $('#user_id').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email);
                $('#role').val(data.role);
                $('.password-field').hide();
                $('#modal-title').text('Edit Pengguna');
                let modal = new bootstrap.Modal(document.getElementById('userModal'));
                modal.show();
            });
        });

        $(document).on('click', '.btn-delete', function(){
            let id = $(this).data('id');

            Swal.fire({
                title: 'Hapus pengguna ini?',
                text: 'Data tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if(result.isConfirmed){
                    $.ajax({
                        url: '/users/' + id,
                        type: 'DELETE',
                        data: {_token: '{{ csrf_token() }}'},
                        success: function(){
                            Swal.fire({
                                icon: 'success',
                                title: 'Pengguna dihapus',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            setTimeout(()=>location.reload(), 1300);
                        },
                        error: function(){
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal menghapus user'
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