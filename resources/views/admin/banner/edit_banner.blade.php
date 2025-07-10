@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{ url('/admin/all-banner') }}">
                    <img src="{{ asset('backend/images/back.png') }}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{ url('/admin/all-banner') }}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách Banners
                </a>
                Sửa Banner
            </header>
            <div class="panel-body">
                <div class="position-center">
                    <form id="updateBannerForm" class="form-validate" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="banner_name">Tên Banner</label>
                            <input type="text" name="banner_name" id="banner_name" class="form-control" placeholder="Tên Banner" required>
                        </div>
                        <div class="form-group">
                            <label for="banner_image">Hình ảnh</label>
                            <input type="file" name="banner_image" id="banner_image" class="form-control">
                            <div id="current_image">
                                <label>Ảnh hiện tại:</label>
                                <img id="banner_image_preview" src="" alt="Banner Image Preview" style="max-width: 100px; max-height: 100px;" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="banner_desc">Mô tả</label>
                            <textarea rows="5" name="banner_desc" id="banner_desc" class="form-control" placeholder="Mô tả Banner" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="banner_status">Hiển thị</label>
                            <select name="banner_status" id="banner_status" class="form-control" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Cập nhật Banner</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    const adminTokenRaw = localStorage.getItem("admin_token");
    const adminToken = atob(adminTokenRaw);
    document.addEventListener("DOMContentLoaded", function() {
        const bannerId = "{{ $banner_id }}";

        if (!bannerId) {
            alert("Không tìm thấy ID banner.");
            window.location.href = "/admin/all-banner";
            return;
        }

        fetchBannerData(bannerId);

        document.getElementById("updateBannerForm").addEventListener("submit", function(event) {
            event.preventDefault();
            updateBanner(bannerId);
        });
    });

    function fetchBannerData(bannerId) {
        fetch(`/api/banners/${bannerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const bannerData = data.data;
                    populateBannerData(bannerData);
                } else {
                    alert("Không tìm thấy banner.");
                    window.location.href = "/admin/all-banner";
                }
            })
            .catch(error => {
                console.error("Lỗi khi lấy dữ liệu banner:", error);
            });
    }

    function populateBannerData(bannerData) {
        document.getElementById('banner_name').value = bannerData.banner_name;
        document.getElementById('banner_desc').value = bannerData.banner_desc || "";
        document.getElementById('banner_status').value = bannerData.banner_status;

        const bannerImagePreview = document.getElementById('banner_image_preview');
        if (bannerData.banner_image) {
            bannerImagePreview.src = `/uploads/banner/${bannerData.banner_image}`;
        }
    }

    function updateBanner(bannerId) {


        if (!adminToken) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        let formData = new FormData(document.getElementById("updateBannerForm"));
        formData.append("_method", "PATCH");

        const headers = new Headers();
        headers.append("Authorization", `Bearer ${adminToken}`);

        fetch(`/api/banners/${bannerId}`, {
                method: "POST",
                headers: headers,
                body: formData
            })
            .then(response => handleUnauthorizedError(response))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = "/admin/all-banner";
                } else {
                    alert("Cập nhật thất bại: " + data.message);
                }
            })
            .catch(error => {
                console.error("Lỗi cập nhật banner:", error);
                alert("Đã có lỗi xảy ra, vui lòng thử lại!");
            });
    }

    function handleUnauthorizedError(response) {
        if (response.status === 401) {
            alert("Chưa đăng nhập, vui lòng đăng nhập!");
            window.location.href = "{{ url('admin-login') }}";
            throw new Error("Unauthorized access");
        }
        return response;
    }
</script>

@endsection