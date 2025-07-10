@extends('admin.admin_layout')
@section('admin_content')

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <a href="{{url('/admin/dashboard') }}">
                    <img src="{{asset('backend/images/back.png')}}" alt="Back" style=" float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
                </a>
                <a href="{{url('/admin/all-category')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                    Danh sách danh mục
                </a>
                Thêm danh mục sản phẩm
            </header>
            <div class="panel-body">

                <div class="position-center">
                    <form id="addCategoryForm" class="form-validate">
                        <div class="form-group">
                            <label for="category_name">Tên danh mục</label>
                            <input type="text" name="category_name" onkeyup="ChangeToSlug();" class="form-control" id="slug" placeholder="Tên danh mục" required>
                        </div>
                        <div class="form-group">
                            <label for="category_slug">Slug</label>
                            <input type="text" name="category_slug" id="convert_slug" class="form-control" placeholder="Slug" required>
                        </div>
                        <div class="form-group">
                            <label for="category_desc">Mô tả</label>
                            <textarea style="resize: none" rows="5" class="form-control" name="category_desc" id="category_desc" placeholder="Mô tả danh mục" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="category_parent">Thuộc danh mục</label>
                            <select name="category_parent" id="category_parent" class="form-control input-sm m-bot15" required>
                                <option value="0">Chọn</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category_status">Hiển thị</label>
                            <select name="category_status" class="form-control input-sm m-bot15" required>
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info">Thêm danh mục</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
@section('scripts')
<script>
    // const adminTokenRaw = localStorage.getItem("admin_token");
    // const adminToken = atob(adminTokenRaw);
    document.addEventListener("DOMContentLoaded", function() {
        fetch("{{ url('/api/categories') }}")
            .then(response => response.json())
            .then(data => {
                console.log("Dữ liệu nhận được từ API:", data);

                if (data && data.success && Array.isArray(data.data)) {
                    let categoryParentSelect = document.querySelector("#category_parent");

                    let parentCategories = data.data.filter(category => category.category_parent == 0);

                    parentCategories.forEach(category => {
                        let option = document.createElement("option");
                        option.value = category.category_id;
                        option.textContent = category.category_name;
                        categoryParentSelect.appendChild(option);
                    });
                } else {
                    console.error("Dữ liệu không hợp lệ hoặc không có danh mục.");
                }
            })
            .catch(error => console.error("Lỗi khi lấy danh mục:", error));
    });


    document.querySelector("#addCategoryForm").addEventListener("submit", function(event) {
        event.preventDefault();


        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            return;
        }

        let formData = new FormData(this);

        fetch("{{ url('/api/categories') }}", {
                method: "POST",
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Thêm danh mục thành công");
                    window.location.href = "{{ url('/admin/all-category') }}";
                } else {
                    alert(data.message || "Có lỗi xảy ra");
                }
            })
            .catch(error => console.error("Lỗi khi thêm danh mục:", error));
    });
</script>

@endsection