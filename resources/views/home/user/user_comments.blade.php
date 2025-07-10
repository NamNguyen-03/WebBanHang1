@extends('home.user_layout')
@section('mini_content')


<section class="orders-section py-5">
    <div class="container" style="width:100%">
        <table id="userCommentsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="width: 50px;">STT</th>
                    <th style="width: 150px;">Sản phẩm</th> <!-- Thu ngắn -->
                    <th style="width: 80px;">Hình ảnh</th>
                    <th style="width: 120px;">Đánh giá</th>
                    <th style="width: 400px;">Bình luận</th> <!-- Mở rộng -->
                    <th style="width: 120px;">Ngày bình luận</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</section>

<script>
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');

    document.addEventListener('DOMContentLoaded', function() {
        if (!userId) {
            swal({
                title: "Cảnh báo",
                text: "<span style='color:red;'>Vui lòng đăng nhập trước!</span>",
                type: "warning",
                html: true
            });
            window.location.href = "/login";
            return;
        }

        fetch(`/api/users/${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data.comments)) {
                    renderComments(data.data.comments);
                }
            })
            .catch(error => console.error('Lỗi khi fetch comment:', error));
    });

    function renderComments(comments) {
        const tbody = document.querySelector("#userCommentsTable tbody");
        tbody.innerHTML = '';

        comments.forEach((comment, index) => {
            const product = comment.product;
            const productSlug = product.product_slug;
            const productLink = `/product-details/${productSlug}`;
            const productImage = product.product_image;
            const productName = product.product_name.length > 40 ? product.product_name.substring(0, 40) + "..." : product.product_name;

            let stars = '';
            if (comment.rating && comment.rating.rating != null) {
                const ratingValue = Math.round(comment.rating.rating * 2) / 2;
                for (let i = 1; i <= 5; i++) {
                    if (ratingValue >= i) {
                        stars += '<i class="fas fa-star" style="color:gold;"></i>';
                    } else if (ratingValue + 0.5 === i) {
                        stars += '<i class="fas fa-star-half-alt" style="color:gold;"></i>';
                    } else {
                        stars += '<i class="far fa-star" style="color:gold;"></i>';
                    }
                }
            }

            const rowHTML = `
            <tr>
                <td>${index + 1}</td>
                <td><a href="${productLink}">${productName}</a></td>
                <td>
                    <img src="/uploads/product/${productImage}" alt="${productName}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                </td>
                <td>${stars}</td>
                <td>${comment.comment}</td>
                <td>${comment.comment_date}</td>
            </tr>
        `;
            tbody.innerHTML += rowHTML;
        });


        // Khởi tạo DataTable
        $('#userCommentsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
            }
        });
    }
</script>

<style>
    td img {
        border: 1px solid #ddd;
        padding: 3px;
        background-color: #fff;
    }
</style>


@endsection