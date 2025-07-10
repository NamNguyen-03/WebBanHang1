@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-post')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm bài viết
            </a>
            Liệt kê bài viết
        </div>

        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs"></div>
            <div class="col-sm-4"></div>
            <div class="col-sm-3">
                <!-- <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchPosts()">Search</button>
                    </span>
                </div> -->

            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="postTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tiêu đề</th>
                        <th>Hình ảnh</th>
                        <th>Slug</th>
                        <th>Mô tả</th>
                        <th>Nội dung</th>
                        <th>Danh mục bài viết</th>
                        <th>Hiển thị</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div id="pagination" style="margin-top: 20px;"></div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    let allPosts = [];
    let currentPage = 1;
    const perPage = 10;
    let searchQuery = "";
    // const adminTokenRaw = localStorage.getItem("admin_token");
    // const adminToken = atob(adminTokenRaw);
    document.addEventListener("DOMContentLoaded", function() {
        fetchPostsFromAPI();
    });

    function fetchPostsFromAPI() {
        const url = `{{ url('/api/posts') }}?search=${encodeURIComponent(searchQuery)}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allPosts = data.data;
                    renderPosts(currentPage);
                }
            })
            .catch(error => console.error("Lỗi khi lấy bài viết:", error));
    }


    function renderPosts(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const postsToDisplay = allPosts.slice(start, end);

        let tableBody = document.querySelector("#postTable tbody");
        tableBody.innerHTML = "";
        postsToDisplay.forEach((post, index) => {
            let categoryName = post.cate_post?.cate_post_name || "Không có";

            let row = `
            <tr>
                <td>${start + index + 1}</td>
                <td>${post.post_title.length > 100 ? post.post_title.substring(0, 100) + '...' : post.post_title}</td>
                <td><img src="{{asset('uploads/post/')}}/${post.post_image}" width="50"></td>
                <td>${post.post_slug}</td>
                <td>${post.post_desc.length > 100 ? post.post_desc.substring(0, 100) + '...' : post.post_desc}</td>
                <td>${post.post_content.length > 100 ? post.post_content.substring(0, 100) + '...' : post.post_content}</td>
                <td>${categoryName}</td>
                <td>
                    <a href="javascript:void(0)" class="toggle-status" data-id="${post.post_id}" data-status="${post.post_status}">
                        ${post.post_status == 1 ? 
                            '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' : 
                            '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>'}
                    </a>
                </td>
                <td>
                    <a href="/admin/edit-post/${post.post_slug}" class="active">
                        <i class="fa fa-pencil-square-o text-success text-active"></i>
                    </a>
                    <a onclick="deletePost('${post.post_slug}')" href="#" class="active">
                        <i class="fa fa-trash text"></i>
                    </a>
                </td>
            </tr>`;
            tableBody.innerHTML += row;
        });

        document.querySelectorAll(".toggle-status").forEach(link => {
            link.addEventListener("click", function() {
                let postId = this.getAttribute("data-id");
                let currentStatus = this.getAttribute("data-status");
                let newStatus = currentStatus == 1 ? 0 : 1;
                updatePostStatus(postId, newStatus, this);
            });
        });

        if ($.fn.DataTable.isDataTable('#postTable')) {
            $('#postTable').DataTable().destroy();
        }

        $('#postTable').DataTable({
            paging: true,
            searching: true,
            ordering: true
        });
    }

    function updatePagination() {
        const totalPages = Math.ceil(allPosts.length / perPage);
        const paginationDiv = document.getElementById("pagination");
        paginationDiv.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {
            const pageLink = document.createElement("a");
            pageLink.href = "#";
            pageLink.className = "page-link";
            pageLink.innerText = i;

            pageLink.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = i;
                renderPosts(currentPage);
                updatePagination();
            });

            if (i === currentPage) {
                pageLink.classList.add("active");
            }

            paginationDiv.appendChild(pageLink);
        }
    }

    function searchPosts() {

        searchQuery = document.getElementById('searchInput').value;
        currentPage = 1;
        fetchPostsFromAPI();
    }


    function updatePostStatus(postId, newStatus, element) {

        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch(`{{ url('/api/posts/') }}/${postId}/status`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken
                },
                body: JSON.stringify({
                    post_status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Cập nhật trạng thái thành công!");
                    element.setAttribute("data-status", newStatus);
                    element.innerHTML = newStatus == 1 ?
                        '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
                        '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>';
                } else {
                    alert("Lỗi từ server: " + (data.message || "Không thể cập nhật."));
                }
            })
            .catch(error => alert(error.message));
    }

    function deletePost(post_slug) {

        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa bài viết này không?")) {
            fetch(`{{ url('/api/posts/') }}/${post_slug}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": "Bearer " + adminToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Xóa bài viết thành công!");
                        fetchPostsFromAPI();
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa bài viết."));
                    }
                })
                .catch(error => alert(error.message));
        }
    }
</script>

<style>
    #pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .page-link {
        margin: 5px;
        padding: 10px 15px;
        text-decoration: none;
        color: #007bff;
        border: 1px solid #007bff;
        border-radius: 5px;
    }

    .page-link:hover {
        background-color: #007bff;
        color: white;
    }

    .page-link.active {
        background-color: #007bff;
        color: white;
        border-color: #0056b3;
    }
</style>

@endsection