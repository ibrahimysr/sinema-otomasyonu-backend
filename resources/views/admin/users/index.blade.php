@extends('admin.layouts.app')

@section('title', 'Kullanıcı Yönetimi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kullanıcı Listesi</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ad</th>
                                    <th>E-posta</th>
                                    <th>Rol</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changeRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kullanıcı Rolünü Değiştir</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="changeRoleForm">
                    <input type="hidden" id="userId" name="user_id">
                    <div class="form-group">
                        <label for="roleSelect">Yeni Rol</label>
                        <select class="form-control" id="roleSelect" name="role_id">
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="saveRoleButton">Kaydet</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#usersTable').DataTable({
        ajax: {
            url: '/api/users/user-list',
            dataSrc: 'data',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'role.name' },
            { data: 'created_at' },
            {
                data: null,
                render: function(data, type, row) {
                    return '<button class="btn btn-primary btn-sm changeRole" data-id="' + row.id + '">Rol Değiştir</button>';
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json'
        }
    });

    function loadRoles() {
        $.ajax({
            url: '/api/role/roles',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            },
            success: function(response) {
                var roles = response.data;
                var select = $('#roleSelect');
                select.empty();
                roles.forEach(function(role) {
                    select.append(new Option(role.name, role.id));
                });
            }
        });
    }

    $('#usersTable').on('click', '.changeRole', function() {
        var userId = $(this).data('id');
        $('#userId').val(userId);
        loadRoles();
        $('#changeRoleModal').modal('show');
    });

    $('#saveRoleButton').click(function() {
        var userId = $('#userId').val();
        var roleId = $('#roleSelect').val();

        $.ajax({
            url: '/api/role/change-user-role',
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            },
            data: {
                user_id: userId,
                role_id: roleId
            },
            success: function(response) {
                $('#changeRoleModal').modal('hide');
                table.ajax.reload();
                toastr.success('Kullanıcı rolü başarıyla güncellendi');
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Bir hata oluştu');
            }
        });
    });
});
</script>
@endsection 