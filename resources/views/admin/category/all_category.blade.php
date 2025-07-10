@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/dashboard') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <a href="{{url('/admin/add-category')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm danh mục
            </a>
            Liệt kê danh mục sản phẩm
        </div>
        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <button id="showAllBtn">Hiện tất cả danh mục</button>
            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchCategories()">Go</button>
                    </span>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="categoryTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên danh mục</th>
                        <th>Danh mục cha</th>
                        <th>Slug</th>
                        <th>Mô tả danh mục</th>
                        <th>Hiển thị</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="sortableParents">
                </tbody>
            </table>
        </div>

        <div id="pagination" class="text-center" style="margin-top: 20px;"></div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    let allCategories = [];
    let currentPage = 1;
    let perPage = 3;
    let searchQuery = "";
    // const adminTokenRaw = localStorage.getItem("admin_token");
    // const adminToken = atob(adminTokenRaw);
    let showAll = false;

    function searchCategories() {
        searchQuery = document.getElementById('searchInput').value;
        currentPage = 1;
        fetchCategories();
    }

    function fetchCategories() {
        const url = `{{ url('/api/categories') }}?search=${encodeURIComponent(searchQuery)}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allCategories = data.data;
                    renderCategories(currentPage);

                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error("Lỗi khi lấy danh mục:", error));
    }


    // function renderCategories(page) {
    //     const start = (page - 1) * perPage;
    //     const end = start + perPage;

    //     const parents = allCategories
    //         .filter(category => category.category_parent == 0)
    //         .sort((a, b) => a.category_order - b.category_order);

    //     const categoriesToDisplay = parents.slice(start, end);

    //     let tableBody = document.querySelector("#categoryTable tbody");
    //     tableBody.innerHTML = "";

    //     categoriesToDisplay.forEach(parent => {
    //         const parentRow = generateCategoryRow(parent, false);
    //         tableBody.innerHTML += parentRow;

    //         const children = allCategories
    //             .filter(category => category.category_parent == parent.category_id)
    //             .sort((a, b) => a.category_order - b.category_order);

    //         children.forEach(child => {
    //             const childRow = generateCategoryRow(child, true);
    //             tableBody.innerHTML += childRow;
    //         });
    //     });

    //     enableDragAndDrop();
    //     updatePagination();
    //     document.querySelectorAll(".toggle-status").forEach(link => {
    //         link.addEventListener("click", function() {
    //             let categorySlug = this.getAttribute("data-slug");
    //             let currentStatus = this.getAttribute("data-status");
    //             let newStatus = currentStatus == 1 ? 0 : 1;
    //             updateCategoryStatus(categorySlug, newStatus, this);
    //         });
    //     });
    // }
    function renderCategories(page) {
        const start = (page - 1) * perPage;
        const end = start + perPage;

        const parents = allCategories
            .filter(category => category.category_parent == 0)
            .sort((a, b) => a.category_order - b.category_order);

        const categoriesToDisplay = parents.slice(start, end);

        let tableBody = document.querySelector("#categoryTable tbody");
        tableBody.innerHTML = "";

        categoriesToDisplay.forEach(parent => {
            const parentIndex = parents.findIndex(p => p.category_id === parent.category_id) + 1;
            const parentRow = generateCategoryRow(parent, false, parentIndex);
            tableBody.innerHTML += parentRow;
            const children = allCategories
                .filter(category => category.category_parent == parent.category_id)
                .sort((a, b) => a.category_order - b.category_order);

            let childIndex = 1;
            children.forEach(child => {
                const childRow = generateCategoryRow(child, true, childIndex++);
                tableBody.innerHTML += childRow;
            });
        });

        enableDragAndDrop();
        updatePagination();

        document.querySelectorAll(".toggle-status").forEach(link => {
            link.addEventListener("click", function() {
                let categorySlug = this.getAttribute("data-slug");
                let currentStatus = this.getAttribute("data-status");
                let newStatus = currentStatus == 1 ? 0 : 1;
                updateCategoryStatus(categorySlug, newStatus, this);
            });
        });
    }



    function generateCategoryRow(category, isChild = false, displayOrder = '') {
        const indentStyle = isChild ? 'background-color:rgb(224, 252, 253);' : '';
        const parentName = category.parent ? category.parent.category_name : "Không có";
        const rowClass = isChild ? "group-child" : "";
        const parentAttr = isChild ? `data-parent-id="${category.category_parent}"` : "";

        return `
        <tr data-id="${category.category_id}" ${parentAttr} style="${indentStyle}" class="${rowClass}">
            <td style="color: ${isChild ? 'inherit' : 'red'}; font-weight: ${isChild ? 'normal' : 'bold'}; font-size: ${isChild ? '12px' : '18px'}">
                ${displayOrder}
            </td>
            <td>${category.category_name}</td>
            <td>${parentName}</td>
            <td>${category.category_slug}</td>
            <td>${category.category_desc.length > 150 ? category.category_desc.substr(0, 150) : category.category_desc}</td>
            <td>
                <a href="javascript:void(0)" class="toggle-status" data-slug="${category.category_slug}" data-status="${category.category_status}">
                    ${category.category_status == 1 ?
                        '<i class="fa-solid fa-eye fa-2x" style="color: green;"></i>' :
                        '<i class="fa-solid fa-eye-slash fa-2x" style="color: red;"></i>'}
                </a>
            </td>
            <td>
                <a href="/admin/edit-category/${category.category_slug}" class="active">
                    <i class="fa fa-pencil-square-o text-success text-active"></i>
                </a>
                <a onclick="deleteCategory('${category.category_slug}')" href="javascript:void(0)" class="active">
                    <i class="fa fa-trash text"></i>
                </a>
            </td>
        </tr>`;
    }








    document.addEventListener("DOMContentLoaded", function() {
        fetchCategories();
    });



    function updatePagination() {

        const parents = allCategories.filter(c => c.category_parent == 0);
        const totalPages = Math.ceil(parents.length / perPage);
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
                renderCategories(currentPage);
                updatePagination();
            });

            if (i === currentPage) {
                pageLink.classList.add("active");
            }

            paginationDiv.appendChild(pageLink);
        }
    }
    document.getElementById("showAllBtn").addEventListener("click", () => {
        showAll = !showAll;
        document.getElementById("showAllBtn").innerText = showAll ? "Phân trang lại" : "Hiện tất cả danh mục";
        if (showAll) {
            perPage = 1000;
        } else {
            perPage = 3;
        }
        renderCategories(1);

    });
</script>
<script>
    function enableDragAndDrop() {
        $("#categoryTable tbody").sortable({
            items: "> tr",
            placeholder: "ui-state-highlight",

            start: function(event, ui) {
                const currentRow = ui.item;
                originalIndex = currentRow.index();
                draggedChildren = [];

                if (currentRow.hasClass("group-child")) {
                    draggedType = 'child';
                    draggedChildParentId = currentRow.data("parent-id");
                } else {
                    draggedType = 'parent';
                    const currentId = currentRow.data("id");

                    currentRow.nextAll().each(function() {
                        const row = $(this);
                        if (row.hasClass("group-child") && row.data("parent-id") == currentId) {
                            draggedChildren.push(row);
                        } else {
                            return false;
                        }
                    });

                    draggedChildren.forEach(row => row.hide());
                }
            },

            stop: function(event, ui) {
                const currentRow = ui.item;

                if (draggedType === 'parent') {
                    draggedChildren.reverse().forEach(child => {
                        child.insertAfter(currentRow);
                        child.show();
                    });
                    draggedChildren = [];
                }

                draggedType = null;
                draggedChildParentId = null;
            },

            update: function(event, ui) {
                const currentRow = ui.item;

                if (draggedType === 'parent') {
                    const next = currentRow.next();

                    if (next.length && next.hasClass("group-child")) {
                        const nextParentId = next.data("parent-id");
                        const currentId = currentRow.data("id");

                        if (nextParentId !== currentId) {
                            $("#categoryTable tbody").sortable("cancel");
                            draggedChildren.forEach(row => row.show());
                            draggedChildren = [];
                            alert("Không thể thả danh mục cha vào giữa các danh mục con.");
                            return;
                        }
                    }
                }

                if (draggedType === 'child') {
                    let valid = false;

                    $("#categoryTable tbody > tr").each(function() {
                        const row = $(this);

                        if (!row.hasClass("group-child")) {
                            const parentId = row.data("id");

                            if (parentId === draggedChildParentId) {
                                valid = true;
                            } else if (valid) {
                                valid = false;
                            }
                        }

                        if (row[0] === currentRow[0]) {
                            if (!valid) {
                                $("#categoryTable tbody").sortable("cancel");
                                alert("Không thể kéo danh mục con ra ngoài phạm vi cha của nó.");
                                return false;
                            }
                        }
                    });
                }

                const parentOrder = [];
                const childOrderMap = {};
                let currentParentId = null;
                const offset = perPage * (currentPage - 1);
                let parentIndex = 0;

                $("#categoryTable tbody > tr").each(function() {
                    const row = $(this);
                    const rowId = row.data("id");

                    if (!row.hasClass("group-child")) {
                        parentOrder.push({
                            category_id: rowId,
                            category_order: offset + parentIndex + 1
                        });
                        currentParentId = rowId;
                        childOrderMap[currentParentId] = [];
                        parentIndex++;
                    } else {
                        if (currentParentId) {
                            childOrderMap[currentParentId].push(rowId);
                        }
                    }
                });
                if (!adminTokenRaw) {
                    alert("Bạn cần đăng nhập để thực hiện thao tác này!");
                    window.location.href = "{{ url('admin-login') }}";
                    return;
                }
                fetch('/api/categories/update-order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            "Authorization": "Bearer " + adminToken,
                        },
                        body: JSON.stringify({
                            parents: parentOrder,
                            children: childOrderMap
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message || "Cập nhật thứ tự thành công!");
                        fetchCategories();
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Đã xảy ra lỗi khi cập nhật thứ tự!");
                    });
            }
        });
    }


    // let draggedChildren = [];
    // let originalIndex = null;
    // let draggedType = null; // 'parent' or 'child'
    // let draggedChildParentId = null;

    // function enableDragAndDrop() {
    //     $("#categoryTable tbody").sortable({
    //         items: "> tr", // Cho kéo tất cả các dòng
    //         placeholder: "ui-state-highlight",

    //         start: function(event, ui) {
    //             const currentRow = ui.item;
    //             originalIndex = currentRow.index();

    //             draggedChildren = [];

    //             if (currentRow.hasClass("group-child")) {
    //                 // Kéo dòng con
    //                 draggedType = 'child';
    //                 draggedChildParentId = currentRow.data("parent-id");
    //             } else {
    //                 // Kéo dòng cha
    //                 draggedType = 'parent';
    //                 const currentId = currentRow.data("id");

    //                 currentRow.nextAll().each(function() {
    //                     const row = $(this);
    //                     if (row.hasClass("group-child") && row.data("parent-id") == currentId) {
    //                         draggedChildren.push(row);
    //                     } else {
    //                         return false;
    //                     }
    //                 });

    //                 draggedChildren.forEach(row => row.hide());
    //             }
    //         },

    //         stop: function(event, ui) {
    //             const currentRow = ui.item;

    //             if (draggedType === 'parent') {
    //                 draggedChildren.reverse().forEach(child => {
    //                     child.insertAfter(currentRow);
    //                     child.show();
    //                 });
    //                 draggedChildren = [];
    //             }

    //             draggedType = null;
    //             draggedChildParentId = null;
    //         },

    //         update: function(event, ui) {
    //             const currentRow = ui.item;

    //             if (draggedType === 'parent') {
    //                 const prev = currentRow.prev();
    //                 if (prev.length && prev.hasClass("group-child")) {
    //                     $("#categoryTable tbody").sortable("cancel");
    //                     draggedChildren.forEach(row => row.show());
    //                     draggedChildren = [];
    //                     alert("Không thể thả danh mục cha vào giữa các danh mục con.");
    //                     return;
    //                 }
    //             }

    //             if (draggedType === 'child') {
    //                 let valid = false;

    //                 $("#categoryTable tbody > tr").each(function() {
    //                     const row = $(this);

    //                     if (!row.hasClass("group-child")) {
    //                         const parentId = row.data("id");

    //                         if (parentId === draggedChildParentId) {
    //                             valid = true;
    //                         } else if (valid) {
    //                             valid = false;
    //                         }
    //                     }

    //                     if (row[0] === currentRow[0]) {
    //                         if (!valid) {
    //                             $("#categoryTable tbody").sortable("cancel");
    //                             alert("Không thể kéo danh mục con ra ngoài phạm vi cha của nó.");
    //                             return false;
    //                         }
    //                     }
    //                 });
    //             }


    //             const parentOrder = [];
    //             const childOrderMap = {};
    //             let currentParentId = null;
    //             const offset = perPage * (currentPage - 1);
    //             let parentIndex = 0;

    //             $("#categoryTable tbody > tr").each(function() {
    //                 const row = $(this);
    //                 const rowId = row.data("id");

    //                 if (!row.hasClass("group-child")) {
    //                     parentOrder.push({
    //                         category_id: rowId,
    //                         category_order: offset + parentIndex + 1
    //                     });
    //                     currentParentId = rowId;
    //                     childOrderMap[currentParentId] = [];
    //                     parentIndex++;
    //                 } else {
    //                     if (currentParentId) {
    //                         childOrderMap[currentParentId].push(rowId);
    //                     }
    //                 }
    //             });

    //             console.log("parents:", parentOrder);
    //             console.log("children:", childOrderMap);

    //             fetch('/api/categories/update-order', {
    //                     method: 'POST',
    //                     headers: {
    //                         'Content-Type': 'application/json',
    //                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //                         "Authorization": "Bearer " + adminToken,
    //                     },
    //                     body: JSON.stringify({
    //                         parents: parentOrder,
    //                         children: childOrderMap
    //                     })
    //                 })
    //                 .then(res => res.json())
    //                 .then(data => {
    //                     alert(data.message || "Cập nhật thứ tự thành công!");
    //                     fetchCategories();
    //                 })
    //                 .catch(err => {
    //                     console.error(err);
    //                     alert("Đã xảy ra lỗi khi cập nhật thứ tự!");
    //                 });
    //         }
    //     });
    // }
</script>

<script>
    function updateCategoryStatus(categorySlug, newStatus, element) {
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            return;
        }
        fetch(`{{ url('/api/categories/') }}/${categorySlug}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Bearer " + adminToken,

                },
                body: JSON.stringify({
                    category_status: newStatus
                })
            })
            .then(response => {
                if (response.status === 401) {
                    alert("Token không hợp lệ. Bạn cần đăng nhập lại.");
                    window.location.href = "/admin-login";
                    return;
                }
                return response.json();
            })
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

    function deleteCategory(categoryId) {
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa danh mục này không?")) {
            fetch(`{{ url('/api/categories/') }}/${categoryId}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": "Bearer " + adminToken,
                    }
                })
                .then(response => {
                    if (response.status === 401) {
                        alert("Token không hợp lệ. Bạn cần đăng nhập lại.");
                        window.location.href = "/admin-login";
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Xóa danh mục thành công!");
                        fetchCategories();
                    } else {
                        alert("Lỗi từ server: " + (data.message || "Không thể xóa danh mục."));
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
        border: 1px solid #0056b3;
    }

    .ui-state-highlight {
        background-color: #e0e0e0;
        height: 40px;
    }
</style>
@endsection