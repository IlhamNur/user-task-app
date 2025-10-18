@extends('layouts.app')
@section('content')
<div class="container">
    <button id="btn-add">Buat Task</button>
    <table id="tasks-table" class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>User</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<div id="task-modal" style="display:none;">
    <form id="task-form">
        @csrf
        <input type="hidden" name="task_id" id="task_id">
        <input name="title" id="title" required>
        <textarea name="description" id="description"></textarea>
        <select name="status" id="status">
            <option value="to-do">to-do</option>
            <option value="in-progress">in-progress</option>
            <option value="done">done</option>
        </select>
        <button type="submit">Simpan</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(function(){
  var table = $('#tasks-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{!! route("tasks.index") !!}',
    columns: [
      {data:'id'},
      {data:'title'},
      {data:'status'},
      {data:'user.name', name:'user.name'},
      {data:'action', orderable:false, searchable:false}
    ]
  });

  $('#btn-add').on('click', function(){
    $('#task-form')[0].reset();
    $('#task_id').val('');
    $('#task-modal').show();
  });

  $('#task-form').on('submit', function(e){
    e.preventDefault();
    var id = $('#task_id').val();
    var url = id ? '/tasks/'+id : '/tasks';
    var method = id ? 'PUT' : 'POST';

    $.ajax({
      url: url,
      method: method,
      data: $(this).serialize(),
      success: function(res){
        $('#task-modal').hide();
        table.ajax.reload(null, false);
      },
      error: function(xhr){
        alert('Terjadi error: '+xhr.responseJSON?.message ?? 'validation error');
      }
    });
  });

  $('#tasks-table').on('click', '.btn-edit', function(){
    var id = $(this).data('id');
    $.get('/tasks/'+id+'/edit', function(data){
      $('#task_id').val(data.id);
      $('#title').val(data.title);
      $('#description').val(data.description);
      $('#status').val(data.status);
      $('#task-modal').show();
    });
  });

  $('#tasks-table').on('click', '.btn-delete', function(){
    if (!confirm('Hapus task?')) return;
    var id = $(this).data('id');
    $.ajax({
      url: '/tasks/'+id,
      method: 'DELETE',
      data: {_token: '{{ csrf_token() }}'},
      success: function(){ table.ajax.reload(null,false); }
    });
  });
});
</script>
@endpush
