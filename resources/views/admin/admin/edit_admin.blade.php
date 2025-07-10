@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ url('/admin/all-admin') }}">
                    <img src="{{ asset('backend/images/back.png') }}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{ url('/admin/all-admin') }}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách Admin
                </a>
                Sửa Admin
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="updateAdminForm" class="form-validate">
                        <div class="form-group">
                            <label for="admin_name">Tên Admin</label>
                            <input type="text" name="admin_name" id="admin_name" class="form-control" placeholder="Tên Admin" required>
                        </div>
                        <div class="form-group">
                            <label for="admin_email">Email</label>
                            <input type="text" name="admin_email" id="admin_email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="admin_phone">Phone</label>
                            <input type="text" name="admin_phone" id="admin_phone" class="form-control" placeholder="Phone" required>
                        </div>
                        <button type="submit" class="btn btn-info">Cập nhật</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    const adminId = "{{$admin_id}}";
    const adminTokenRaw = localStorage.getItem("admin_token");
    const adminToken = atob(adminTokenRaw);
    document.addEventListener("DOMContentLoaded", function() {
        fetchAdminInfo();

        const form = document.getElementById('updateAdminForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            updateAdminInfo();
        });
    });

    function fetchAdminInfo() {
        fetch(`/api/admins/${adminId}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('admin_name').value = res.data.admin_name;
                    document.getElementById('admin_email').value = res.data.admin_email;
                    document.getElementById('admin_phone').value = res.data.admin_phone;
                } else {
                    alert('Lỗi khi lấy thông tin admin');
                }
            })
            .catch(error => {
                console.error("Lỗi API: " + error);
            });
    }

    function updateAdminInfo() {
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }
        const formData = {
            admin_name: document.getElementById('admin_name').value,
            admin_email: document.getElementById('admin_email').value,
            admin_phone: document.getElementById('admin_phone').value
        };

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