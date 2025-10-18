<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-medium">{{ __('Daftar Pengguna') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Kelola daftar pengguna di bawah ini.') }}</p>
                    </div>
                    <x-primary-button id="btn-add" class="bg-gray-800 hover:bg-gray-700">
                        + {{ __('Tambah Pengguna') }}
                    </x-primary-button>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-center align-middle">
                        <thead class="bg-gray-100 text-gray-700 border-b">
                            <tr>
                                <th class="px-3 py-2">ID</th>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">Email</th>
                                <th class="px-3 py-2">Role</th>
                                <th class="px-3 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2">{{ $user->id }}</td>
                                <td class="py-2">{{ $user->name }}</td>
                                <td class="py-2">{{ $user->email }}</td>
                                <td class="py-2">
                                    <span
                                        class="px-2 py-1 text-sm rounded
                                            {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    <button class="btn btn-warning btn-sm btn-edit"
                                        data-id="{{ $user->id }}">Edit</button>
                                    @if(auth()->id() !== $user->id)
                                    <button class="btn btn-danger btn-sm btn-delete"
                                        data-id="{{ $user->id }}">Hapus</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-3 text-gray-500">Tidak ada data pengguna.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg rounded-2xl">
                <div class="modal-header bg-gray-100 border-b">
                    <h5 class="modal-title font-semibold" id="modal-title">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="user-form">
                    @csrf
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="modal-body space-y-3">
                        <div>
                            <x-input-label for="name" :value="__('Nama')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                        </div>

                        <div class="password-field">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                        </div>

                        <div>
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
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
    <script>
        $(function(){
            $('#btn-add').on('click', function(){
                $('#user-form')[0].reset();
                $('#user_id').val('');
                $('.password-field').show();
                $('#modal-title').text('Tambah Pengguna');
                new bootstrap.Modal('#userModal').show();
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
                        $('#user-form button[type=submit]').prop('disabled', true);
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
                    new bootstrap.Modal('#userModal').show();
                });
            });

            $(document).on('click', '.btn-delete', function(){
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus pengguna ini?',
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
                                    title: 'Berhasil dihapus!',
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