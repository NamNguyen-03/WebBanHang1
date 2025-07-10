@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ url('/admin/all-users') }}">
                    <img src="{{ asset('backend/images/back.png') }}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{ url('/admin/all-users') }}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách Users
                </a>
                Sửa Users
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="updateAdminForm" class="form-validate">
                        <div class="form-group">
                            <label for="user_name">Tên User</label>
                            <input type="text" name="user_name" id="user_name" class="form-control" placeholder="Tên Admin" required>
                        </div>
                        <div class="form-group">
                            <label for="user_email">Email</label>
                            <input type="text" name="user_email" id="user_email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="user_phone">Phone</label>
                            <input type="text" name="user_phone" id="user_phone" class="form-control" placeholder="Phone" required>
                        </div>
                        <button type="submit" class="btn btn-info">Cập nhật</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
@section('scripts')
<script>
    const userId = "{{$id}}";
    // const adminTokenRaw = localStorage.getItem("admin_token");
    // const adminToken = atob(adminTokenRaw);

    document.addEventListener("DOMContentLoaded", function() {
        fetchUserInfo();

        const form = document.getElementById('updateAdminForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            updateUserInfo();
        });
    });

    function fetchUserInfo() {
        fetch(`/api/users/${userId}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('user_name').value = res.data.name;
                    document.getElementById('user_email').value = res.data.email;
                    document.getElementById('user_phone').value = res.data.phone;
                } else {
                    alert('Lỗi khi lấy thông tin người dùng');
                }
            })
            .catch(error => {
                console.error("Lỗi API: " + error);
            });
    }

    function updateAdminInfo() {
        const formData = {
            name: document.getElementById('user_name').value,
            email: document.getElementById('user_email').value,
            phone: document.getElementById('user_phone').value
        };
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }
        fetch(`/api/admins/${adminId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + adminToken
                },
                body: JSON.stringify(formData)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    alert("Cập nhật thành công!");
                    window.location.href = "/admin/all-admin";
                } else {
                    let errorText = res.message || "Cập nhật thất bại.";
                    if (res.errors) {
                        for (let field in res.errors) {
                            errorText += "\n" + res.errors[field].join("\n");
                        }
                    }
                    alert(errorText);
                }
            })
            .catch(error => {
                console.error("Lỗi khi cập nhật admin:", error);
                alert("Đã xảy ra lỗi. Vui lòng thử lại.");
            });
    }
</script>

@endsection