@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <!-- <a href="{{url('/admin/add-brand')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm thương hiệu
            </a> -->
            Danh sách admin
        </div>

        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <!-- <button id="showAllBtn">Hiện tất cả thương hiệu</button> -->

            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <!-- <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchBrands()">Search</button>
                    </span>
                </div> -->
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="adminTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên admin</th>
                        <th>Email admin</th>
                        <th>Chức vụ</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div id="pagination" class="text-center" style="margin-top: 20px;"></div>
    </div>
</div>
<!-- Modal đổi mật khẩu -->
<div class="modal fade" id="updatePasswordModal" tabindex="-1" role="dialog" aria-labelledby="updatePasswordLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="updatePasswordForm">
                <div class="modal-header">
                    <h4 class="modal-title" id="updatePasswordLabel">Đổi mật khẩu admin</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="password_admin_id">

                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Nhập lại mật khẩu</label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <label for="super_password">Mật khẩu SuperAdmin</label>
                        <input type="password" class="form-control" id="super_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const adminTokenRaw = localStorage.getItem("admin_token");
    const adminToken = atob(adminTokenRaw);
    document.addEventListener("DOMContentLoaded", function() {
        fetchAdmin();
    });

    function fetchAdmin() {
        fetch(`/api/admins`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    renderAdmin(res.data);
                } else {
                    alert("Không thể lấy danh sách admin!");
                }
            })
            .catch(error => {
                console.error("Lỗi khi gọi API:", error);
            });
    }

    function renderAdmin(admins) {
        const tbody = document.querySelector("#adminTable tbody");
        tbody.innerHTML = "";

        admins.forEach((admin, index) => {
            const tr = document.createElement("tr");

            const roles = admin.roles.map(role => role.role_name).join(", ");

            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${admin.admin_name}</td>
                                <td>${admin.admin_email}</td>
                <td>${roles}</td>
                <td>${admin.created_at}</td>
                <td>
                   <a href="/admin/edit-admin/${admin.admin_id}" class="active">
                            <i class="fa fa-pencil-square-o text-success text-active"></i>
                        </a>
                        <a href="javascript:void(0)" class="active" onclick="deleteAdmin(${admin.admin_id})" style="margin-left:5px;">
                            <i class="fa fa-trash text"></i>
                        </a>
                        <a href="javascript:void(0)" class="active" onclick="updatePass(${admin.admin_id})" style="margin-left:5px;">
                            <i class="fa fa-key text"></i>
                        </a>
                </td>
            `;

            tbody.appendChild(tr);
        });
        if ($.fn.DataTable.isDataTable('#adminTable')) {
            $('#adminTable').DataTable().destroy();
        }

        // Khởi tạo lại
        $('#adminTable').DataTable({
            paging: true,
            searching: true,
            ordering: true
        });
    }

    function deleteAdmin(adminId) {
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }
        if (confirm("Bạn có chắc chắn muốn xóa admin này?")) {
            fetch(`/api/admins/${adminId}`, {
                    method: "DELETE",
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + adminToken
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        alert("Đã xóa thành công.");
                        fetchAdmin();
                    } else {
                        alert("Xóa thất bại: " + (res.message || ""));
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi xóa admin:", error);
                    alert("Đã xảy ra lỗi.");
                });
        }
    }

    function updatePass(adminId) {
        document.getElementById('password_admin_id').value = adminId;

        document.getElementById('new_password').value = '';
        document.getElementById('confirm_password').value = '';
        document.getElementById('super_password').value = '';
        $('#updatePasswordModal').modal('show');
    }

    document.getElementById('updatePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }
        const adminId = document.getElementById('password_admin_id').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const superPassword = document.getElementById('super_password').value;

        if (newPassword !== confirmPassword) {
            alert("Mật khẩu không khớp!");
            return;
        }

        fetch(`/api/admins/${adminId}/change-password`, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + adminToken

                },
                body: JSON.stringify({
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword,
                    super_password: superPassword
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    alert("Đổi mật khẩu thành công!");
                    $('#updatePasswordModal').modal('hide');
                } else {
                    alert("Lỗi: " + (res.message || "Không rõ nguyên nhân"));
                }
            })
            .catch(error => {
                console.error("Lỗi đổi mật khẩu:", error);
                alert("Có lỗi xảy ra.");
            });
    });
</script>

@endsection