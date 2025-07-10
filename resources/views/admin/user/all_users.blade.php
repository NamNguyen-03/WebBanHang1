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
            Danh sách người dùng
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
            <table class="table table-striped b-t b-light" id="userTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên người dùng</th>
                        <th>Email người dùng</th>
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
                    <h4 class="modal-title" id="updatePasswordLabel">Đổi mật khẩu người dùng</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="password_user_id">

                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Nhập lại mật khẩu</label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Mật khẩu admin</label>
                        <input type="password" class="form-control" id="admin_password" required>
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
@endsection
@section('scripts')
<script>
    // const adminTokenRaw = localStorage.getItem("admin_token");
    // const adminToken = atob(adminTokenRaw);

    document.addEventListener("DOMContentLoaded", function() {
        fetchUser();
    });

    function fetchUser() {
        fetch(`/api/users`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    renderUser(res.data);
                } else {
                    alert("Không thể lấy danh sách người dùng!");
                }
            })
            .catch(error => {
                console.error("Lỗi khi gọi API:", error);
            });
    }

    function renderUser(users) {
        const tbody = document.querySelector("#userTable tbody");
        tbody.innerHTML = "";

        users.forEach((user, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${user.created_at}</td>
                <td>
                    <a href="/admin/edit-user/${user.id}" class="active">
                        <i class="fa fa-pencil-square-o text-success text-active"></i>
                    </a>
                    <a href="javascript:void(0)" class="active" onclick="deleteUser(${user.id})" style="margin-left:10px;">
                        <i class="fa fa-trash text"></i>
                    </a>
                    <a href="javascript:void(0)" class="active" onclick="updatePass(${user.id})" style="margin-left:10px;">
                        <i class="fa fa-key fa-2x text"></i>
                    </a>
                    <a href="/admin/user-orders/${user.id}" class="active" style="margin-left:10px;font-size:12px">
                        <i class="fa fa-shopping-bag fa-2x text-active"></i>
                    </a>
                </td>
            `;

            tbody.appendChild(tr);
        });
        if ($.fn.DataTable.isDataTable('#userTable')) {
            $('#userTable').DataTable().destroy();
        }

        $('#userTable').DataTable({
            paging: true,
            searching: true,
            ordering: true
        });
    }

    function deleteUser(userId) {
        if (confirm("Bạn có chắc chắn muốn xóa người dùng này?")) {
            fetch(`/api/users/${userId}`, {
                    method: "DELETE",
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + atob(localStorage.getItem("admin_token"))
                    }
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        alert("Đã xóa thành công.");
                        fetchUser();
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

    function updatePass(userId) {
        document.getElementById('password_user_id').value = userId;

        document.getElementById('new_password').value = '';
        document.getElementById('confirm_password').value = '';
        document.getElementById('admin_password').value = '';

        $('#updatePasswordModal').modal('show');
    }

    document.getElementById('updatePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const userId = document.getElementById('password_user_id').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const adminPassword = document.getElementById('admin_password').value;

        if (newPassword !== confirmPassword) {
            alert("Mật khẩu không khớp!");
            return;
        }
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }
        fetch(`/api/users/${userId}/change-password`, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + adminToken

                },
                body: JSON.stringify({
                    new_password: newPassword,
                    new_password_confirmation: confirmPassword,
                    admin_password: adminPassword
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