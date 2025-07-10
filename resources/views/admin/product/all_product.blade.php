@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-product')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm sản phẩm
            </a>
            Liệt kê sản phẩm
        </div>
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <!-- <select class="input-sm form-control w-sm inline v-middle">
                    <option value="0">Bulk action</option>
                    <option value="1">Delete selected</option>
                    <option value="2">Bulk edit</option>
                    <option value="3">Export</option>
                </select>
                <button class="btn btn-sm btn-default">Apply</button> -->
            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <!-- <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchProducts()">Search</button>
                    </span>
                </div> -->
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="productTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sản phẩm</th>
                        <th>SL kho</th>
                        <th>Giá sản phẩm</th>
                        <th>Hình ảnh</th>
                        <th>Thư viện ảnh</th>
                        <th>Danh mục</th>
                        <th>Thương hiệu</th>
                        <th>Mô tả</th>
                        <th>Nội dung</th>
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
<div id="productModal" class="modal">
    <div class="modal-content">
        <button id="modal-close" class="modal-close">&times;</button>
        <div class="modal-header">
            <img id="modal-image" src="" alt="Product Image">
            <h4 id="modal-name">Tên sản phẩm</h4>
        </div>
        <div class="modal-body">
            <label for="">Số lượng nhập</label>
            <input type="number" id="modal-quantity" placeholder="Số lượng" class="form-control" required />
            <label for="">Giá nhập</label>
            <input type="text" id="modal-price-in" class="form-control modal-price-in" placeholder="Giá nhập" required />
            <button id="modal-submit" class="btn-submit">Nhập</button>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    let allProducts = [];
    let currentPage = 1;
    const perPage = 1000000000;
    let searchQuery = "";
    // const adminTokenRaw = localStorage.getItem("admin_token");
    // console.log(adminTokenRaw);
    // const adminToken = atob(adminTokenRaw);
    let currentProductId = null;
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("productModal");
        const closeBtn = document.getElementById("modal-close");
        const submitBtn = document.getElementById("modal-submit");
        fetchProducts();
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
            resetModal();
        });

        submitBtn.addEventListener("click", () => {
            const quantity = document.getElementById("modal-quantity").value.trim();
            let priceIn = document.getElementById("modal-price-in").value.trim().replace(/,/g, "");

            if (!quantity || !priceIn) {
                alert("Vui lòng nhập đủ thông tin!");
                return;
            }
            if (!adminTokenRaw) {
                alert("Bạn cần đăng nhập để thực hiện thao tác này!");
                window.location.href = "{{ url('admin-login') }}";
                return;
            }
            fetch("/api/import-products", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + adminToken
                    },
                    body: JSON.stringify({
                        product_id: currentProductId,
                        price_in: priceIn,
                        quantity_in: quantity
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        // fetchProducts();
                        alert("Nhập sản phẩm thành công");
                        modal.style.display = "none";
                        resetModal();

                    } else {
                        alert("Lỗi từ server: " + (res.message || "Không thể nhập."));
                    }
                })
                .catch(err => console.error("Lỗi API:", err));
        });

        function resetModal() {
            document.getElementById("modal-quantity").value = '';
            document.getElementById("modal-price-in").value = '';
            currentProductId = null;
        }
    });

    function fetchProducts() {
        fetch(`{{ url('/api/products') }}?search=${encodeURIComponent(searchQuery)}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    allProducts = res.data;
                    renderProducts(currentPage);
                }
            })
            .catch(err => console.error("Lỗi khi lấy sản phẩm:", err));
    }

    function renderProducts(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const display = allProducts.slice(start, end);
        const tbody = document.querySelector("#productTable tbody");

        tbody.innerHTML = display.map((product, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${truncate(product.product_name)}</td>
                <td style="color: ${product.product_quantity < 10 ? 'red' : (product.product_quantity <= 30 ? 'orange' : 'green')}">
                ${product.product_quantity}
                </td>
                <td>${product.product_price.toLocaleString()} đ</td>
                <td><img src="{{ asset('uploads/product/') }}/${product.product_image}" width="50"></td>
                <td><a href="/admin/product-gallery/${product.product_id}">Xem</a></td>
                <td>${product.category.category_name}</td>
                <td>${product.brand.brand_name}</td>
                <td>${truncate(stripHtml(product.product_desc), 100)}</td>
                <td>${truncate(stripHtml(product.product_content), 100)}</td>

                <td>
                    <a href="javascript:void(0)" class="toggle-status" data-slug="${product.product_slug}" data-status="${product.product_status}">
                        ${renderStatusIcon(product.product_status)}
                    </a>
                    <a href="javascript:void(0)" class="open-modal" data-id="${product.product_id}" data-name="${product.product_name}" data-image="${product.product_image}" style="margin-left: 5px;">
                        <i class="fa-solid fa-plus fa-2x" style="color: blue;"></i>
                    </a>
                </td>
                <td>
                    <a href="/admin/edit-product/${product.product_slug}" class="active">
                        <i class="fa fa-pencil-square-o text-success text-active"></i>
                    </a>
                    <a href="javascript:void(0)" class="active" onclick="deleteProduct('${product.product_slug}')">
                        <i class="fa fa-trash text"></i>
                    </a>
                </td>
            </tr>
        `).join("");

        attachModalEvents();
        attachToggleEvents();
        if ($.fn.DataTable.isDataTable('#productTable')) {
            $('#productTable').DataTable().destroy();
        }

        $('#productTable').DataTable({
            paging: true,
            searching: true,
            ordering: true
        });

    }

    function stripHtml(html) {
        let div = document.createElement("div");
        div.innerHTML = html;
        return div.textContent || div.innerText || "";
    }

    function truncate(str, max = 100) {
        return str.length > max ? str.substring(0, max) + '...' : str;
    }

    function renderStatusIcon(status) {
        return status == 1 ?
            '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
            '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>';
    }

    function attachModalEvents() {
        document.querySelectorAll(".open-modal").forEach(btn => {
            btn.addEventListener("click", function() {
                const name = this.dataset.name;
                const image = this.dataset.image;
                const productId = this.dataset.id;

                document.getElementById("modal-name").innerText = name;
                document.getElementById("modal-image").src = "{{ asset('uploads/product/') }}/" + image;
                document.getElementById("productModal").style.display = "block";
                currentProductId = productId;
            });
        });
    }

    function attachToggleEvents() {
        document.querySelectorAll(".toggle-status").forEach(link => {
            link.addEventListener("click", function() {
                const slug = this.dataset.slug;
                const currentStatus = +this.dataset.status;
                const newStatus = currentStatus === 1 ? 0 : 1;
                updateProductStatus(slug, newStatus, this);
            });
        });
    }

    // function updatePagination() {
    //     const totalPages = Math.ceil(allProducts.length / perPage);
    //     const paginationDiv = document.getElementById("pagination");
    //     paginationDiv.innerHTML = "";

    //     for (let i = 1; i <= totalPages; i++) {
    //         const link = document.createElement("a");
    //         link.href = "#";
    //         link.className = "page-link";
    //         link.innerText = i;

    //         if (i === currentPage) link.classList.add("active");

    //         link.addEventListener("click", e => {
    //             e.preventDefault();
    //             currentPage = i;
    //             renderProducts(currentPage);
    //             updatePagination();
    //         });

    //         paginationDiv.appendChild(link);
    //     }
    // }

    // function searchProducts() {
    //     searchQuery = document.getElementById("searchInput").value;
    //     currentPage = 1;
    //     fetchProducts();
    // }

    function updateProductStatus(slug, newStatus, element) {
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        fetch(`{{ url('/api/products') }}/${slug}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + adminToken
                },
                body: JSON.stringify({
                    product_status: newStatus
                })
            })
            .then(res => {
                if (res.status === 401) {
                    alert("Chưa đăng nhập, vui lòng đăng nhập!");
                    window.location.href = "{{ url('admin-login') }}";
                    return;
                }
                return res.json();
            })
            .then(res => {
                if (res.success) {
                    alert("Cập nhật trạng thái thành công!");
                    element.setAttribute("data-status", newStatus);
                    element.innerHTML = renderStatusIcon(newStatus);
                } else {
                    alert("Lỗi từ server: " + (res.message || "Không thể cập nhật."));
                }
            })
            .catch(err => alert("Lỗi: " + err.message));
    }

    function deleteProduct(slug) {
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) {
            fetch(`{{ url('/api/products') }}/${slug}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + adminToken
                    }
                })
                .then(res => {
                    if (res.status === 401) {
                        alert("Chưa đăng nhập, vui lòng đăng nhập!");
                        window.location.href = "{{ url('admin-login') }}";
                        return;
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.success) {
                        alert("Xóa sản phẩm thành công!");
                        fetchProducts();
                    } else {
                        alert("Lỗi từ server: " + (res.message || "Không thể xóa sản phẩm."));
                    }
                })
                .catch(err => alert("Lỗi: " + err.message));
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
        border: 1px solid #0056b3;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    .modal-content {
        background: white;
        width: 420px;
        margin: 8% auto;
        padding: 20px;
        border-radius: 12px;
        position: relative;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .modal-close {
        position: absolute;
        top: 10px;
        right: 15px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
    }

    .modal-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .modal-header img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }

    .modal-header h4 {
        margin: 0;
        font-size: 20px;
        font-weight: bold;
    }

    .modal-body .form-control {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 16px;
    }

    .btn-submit {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        font-size: 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-submit:hover {
        background-color: #0056b3;
    }
</style>
@endsection