@extends('admin.admin_layout')
@section('admin_content')
<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>

            Lịch sử nhập
        </div>
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">

                <!-- <button class="btn btn-sm btn-default">Nhập sản phẩm</button> -->
            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <!-- <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchImports()">Search</button>
                    </span>
                </div> -->
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="importTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng nhập</th>
                        <th>Giá nhập</th>
                        <th>Thời gian nhập</th>
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
    // const adminTokenRaw = localStorage.getItem("admin_token");
    // const adminToken = atob(adminTokenRaw);
    let allImports = [];
    let perPage = 10;
    let currentPage = 1;
    let searchQuery = "";

    function fetchImports() {
        fetch(`/api/import-products?search=${encodeURIComponent(searchQuery)}`, {
                headers: {
                    "Authorization": "Bearer " + adminToken,
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    allImports = data.data;
                    currentPage = 1;
                    renderImports(currentPage);
                    // renderPagination();
                } else {
                    alert("Không thể tải dữ liệu nhập hàng");
                }
            })
            .catch(error => {
                console.error("Lỗi khi gọi API nhập hàng:", error);
            });
    }

    function renderImports(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;
        const importsToShow = allImports.slice(start, end);
        const tbody = document.querySelector("#importTable tbody");
        tbody.innerHTML = "";

        importsToShow.forEach((imp, index) => {
            let product = imp.products || {};
            let row = `
        <tr>
            <td>${start + index + 1}</td>
            <td>
                <img src="/uploads/product/${product.product_image}" width="40" style="margin-right: 5px;">
               ${product.product_name.length > 100 ? product.product_name.substring(0, 100) + '...' : product.product_name || '---'}
            </td>
            <td>${imp.quantity_in}</td>
            <td>${Number(imp.price_in).toLocaleString()} đ</td>
            <td>${new Date(imp.created_at).toLocaleString()}</td>
            <td>        
                <a href="javascript:void(0)" class="active" onclick="deleteImport('${imp.import_id}')">
                    <i class="fa fa-trash text"></i>
                </a>
            </td>
        </tr>`;
            tbody.innerHTML += row;
        });
        if ($.fn.DataTable.isDataTable('#importTable')) {
            $('#importTable').DataTable().destroy();
        }

        $('#importTable').DataTable({
            paging: true,
            searching: true,
            ordering: true
        });

    }

    // function renderPagination() {
    //     let totalPages = Math.ceil(allImports.length / perPage);
    //     let container = document.getElementById("pagination");
    //     container.innerHTML = "";

    //     for (let i = 1; i <= totalPages; i++) {
    //         let btn = document.createElement("button");
    //         btn.innerText = i;
    //         btn.className = (i === currentPage) ? "btn btn-primary btn-sm" : "btn btn-default btn-sm";
    //         btn.addEventListener("click", function() {
    //             currentPage = i;
    //             renderImports(currentPage);
    //             renderPagination();
    //         });
    //         container.appendChild(btn);
    //     }
    // }

    // function searchImports() {
    //     searchQuery = document.getElementById('searchInput').value;
    //     currentPage = 1;
    //     fetchImports();
    // }

    document.addEventListener("DOMContentLoaded", function() {
        fetchImports();
    });
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
</style>
@endsection