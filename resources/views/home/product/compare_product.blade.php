@extends('home.home_layout')
@section('content')
<div class="container py-4">
    <h2>So sánh sản phẩm</h2>
    <div id="compare-container">Đang tải dữ liệu...

    </div>
</div>

<script>
    const userId = localStorage.getItem("user_id") || sessionStorage.getItem('user_id');

    document.addEventListener("DOMContentLoaded", function() {
        fetchCompare();
    });

    function fetchCompare() {
        const container = document.getElementById("compare-container");
        const compareKey = `compare_${userId}`;
        const compareData = sessionStorage.getItem(compareKey);

        if (!compareData) {
            container.innerHTML = "<p>Chưa có sản phẩm nào được chọn để so sánh.</p>";
            return;
        }

        const compareList = JSON.parse(compareData);
        if (compareList.length === 0) {
            container.innerHTML = "<p>Chưa có sản phẩm nào để so sánh.</p>";
            return;
        }

        const ids = compareList.map(item => item.product_id);

        fetch('/api/get-products?' + ids.map(id => 'ids[]=' + id).join('&'))
            .then(res => res.json())
            .then(function(productDetails) {
                if (!Array.isArray(productDetails)) {
                    container.innerHTML = "<p>Dữ liệu trả về không hợp lệ.</p>";
                    return;
                }

                const colCount = productDetails.length;

                const table = document.createElement("table");
                table.className = "table table-bordered text-center";
                table.style.tableLayout = "fixed";
                table.style.width = "100%";

                const headers = ["Tên sản phẩm", "Hình ảnh", "Giá", "Mô tả", "Nội dung"];

                headers.forEach(function(header) {
                    const row = document.createElement("tr");

                    const th = document.createElement("th");
                    th.textContent = header;
                    th.style.background = "#f8f9fa";
                    th.style.verticalAlign = "middle";
                    th.style.width = "15%";
                    th.style.textAlign = "center";
                    row.appendChild(th);

                    productDetails.forEach(function(product) {
                        const td = document.createElement("td");
                        td.style.verticalAlign = "middle";
                        td.style.border = "1px solid #dee2e6";
                        td.style.whiteSpace = "normal"; // Cho xuống dòng nếu dài
                        td.style.wordWrap = "break-word"; // Tự ngắt từ khi dài
                        td.style.padding = "10px"; // Thêm khoảng cách nếu cần
                        td.style.width = `${(85 / colCount).toFixed(2)}%`;

                        if (header === "Tên sản phẩm") {
                            td.textContent = product.product_name;
                        } else if (header === "Hình ảnh") {
                            const img = document.createElement("img");
                            img.src = "/uploads/product/" + product.product_image;
                            img.alt = product.product_name;
                            img.width = 100;
                            td.appendChild(img);
                        } else if (header === "Giá") {
                            td.textContent = new Intl.NumberFormat('vi-VN').format(product.product_price) + " đ";
                        } else if (header === "Mô tả") {
                            td.innerHTML = product.product_desc || "Không có mô tả";
                        } else if (header === "Nội dung") {
                            td.innerHTML = product.product_content || "Không có nội dung";
                        }

                        row.appendChild(td);
                    });

                    table.appendChild(row);
                });

                container.innerHTML = "";
                container.appendChild(table);
            })


            .catch(function(error) {
                container.innerHTML = "<p>Không thể tải dữ liệu sản phẩm.</p>";
                console.error(error);
            });
    }
</script>

@endsection