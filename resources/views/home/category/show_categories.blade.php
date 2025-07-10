@extends('home.home_layout')
@section('sidebar_content')
@include('home.home_sidebar')
@endsection
@section('content')
<div class="col-sm-9 padding-right">
    <div class="features_items" style="min-height: 300px;">
        <h2 class="title text-center" id="category-parent-title"></h2>
        <!-- Hiển thị danh mục con -->
        <div class="category-parent-slider-container">
            <div id="category_child" class="category_child-slider">

            </div>
        </div>


        <br>

        <!-- Slider sản phẩm của từng danh mục con -->
        <div id="product_slider_container">
            <!-- JS sẽ render nhiều slider sản phẩm tương ứng ở đây -->
        </div>
        <div class="modal fade" id="quickview" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered custom-modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi tiết sản phẩm</h5>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-5">
                                <img id="product_main_image" src="" width="100%" />
                                <div id="product_quickview_gallery" class="gallery-container"></div>
                            </div>
                            <form action="">
                                @csrf
                                <div id="product_quickview_value"></div>
                                <div class="col-md-7">
                                    <h3><span id="product_quickview_name"></span></h3>
                                    <p>ID: <span id="product_quickview_id"></span></p>
                                    <h4 style="color:blue;">Giá: <span id="product_quickview_price"></span></h4>

                                    <br>
                                    <label>Mô tả:</label>
                                    <p><span id="product_quickview_desc"></span></p>
                                    <span id="more" class="toggle-link" style="display: none; cursor: pointer;"> <i class="fa fa-chevron-down"></i> Xem thêm </span>

                                    <div id="product_quickview_add"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='/cart'">Đi đến giỏ hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .custom-modal {
        max-width: 50%;
        width: 50%;
    }
</style>
<script>
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

    const categorySlug = `{{$category_slug}}`;
    document.addEventListener("DOMContentLoaded", function() {

        fetch(`/api/get-category-parent/${categorySlug}`)
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    const data = response.data;
                    console.log(data);

                    const categoryChildContainer = document.getElementById('category_child');
                    const productSliderContainer = document.getElementById('product_slider_container');
                    document.getElementById('category-parent-title').innerHTML = data[0].category_name;

                    data.forEach(parent => {
                        // Lọc và sắp xếp category con
                        const visibleChildren = (parent.children || [])
                            .filter(child => child.category_status === 1)
                            .sort((a, b) => a.category_order - b.category_order);

                        visibleChildren.forEach(child => {
                            // 1. Render tên danh mục con
                            const childDiv = document.createElement('div');
                            childDiv.className = 'category-child_name';
                            childDiv.textContent = child.category_name;
                            childDiv.addEventListener('click', () => {
                                window.location.href = `/category/${child.category_slug}`;
                            });
                            categoryChildContainer.appendChild(childDiv);

                            // 2. Lọc sản phẩm có product_status == 1
                            const visibleProducts = (child.products || []).filter(p => p.product_status === 1);

                            // 3. Render slider sản phẩm
                            const sliderSection = document.createElement('div');
                            sliderSection.innerHTML = `
                        <div class="product_slider_header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0;">
                            <h2 style="margin: 0; font-size: 30px;">${child.category_name}</h2>
                            <a href="/category/${child.category_slug}" style="color: #1a73e8; text-decoration: none;">Xem tất cả</a>
                        </div>
                        <div class="swiper product_swiper">
                            <div class="swiper-wrapper">
                                ${visibleProducts.map(product => `
                                    <div class="swiper-slide">
                                        <div class="product-image-wrapper" style="height:470px">
                                            <div class="single-products">
                                                <div class="productinfo text-center">
                                                    <form>
                                                        <input type="hidden" value="${product.product_id}" class="cart_product_id_${product.product_id}">
                                                        <input type="hidden" value="${product.product_name}" class="cart_product_name_${product.product_id}">
                                                        <input type="hidden" value="${product.product_slug}" class="cart_product_slug_${product.product_id}">
                                                        <input type="hidden" value="${product.product_image}" class="cart_product_image_${product.product_id}">
                                                        <input type="hidden" value="${product.product_quantity}" class="cart_product_quantity_${product.product_id}">
                                                        <input type="hidden" value="${product.product_price}" class="cart_product_price_${product.product_id}">
                                                        <input type="hidden" value="1" class="cart_product_qty_${product.product_id}">

                                                        <a href="/product-details/${product.product_slug}">
                                                            <img src="/uploads/product/${product.product_image}" alt="${product.product_name}" />
                                                            <h2>${new Intl.NumberFormat('vi-VN').format(product.product_price)}đ</h2>
                                                            <p>${product.product_name.length > 50 ? product.product_name.substr(0, 50) + '...' : product.product_name}</p>
                                                        </a>
                                                        <button data-id_product="${product.product_id}" type="button" class="btn btn-default add-to-cart" >Thêm giỏ hàng</button>
                                                        <button
                                                            type="button"
                                                            class="btn btn-default quick-view"
                                                            data-toggle="modal"
                                                            data-target="#quickview"
                                                            onclick="loadQuickViewProduct('${encodeURIComponent(product.product_slug)}')">
                                                            <i class="fa fa-eye"></i>
                                                        </button>                            
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="choose">
                                                <ul class="nav nav-pills nav-justified">
                                                    <li><a href="#" class="add-to-wishlist" data-product_id="${product.product_id}"><i class="fa fa-heart"></i></a></li>
                                                    <li><a href="#" class="add-to-compare" data-product_id="${product.product_id}" data-product_image="${product.product_image}" data-product_name="${product.product_name}"><i class="fa fa-plus-square"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    `;

                            productSliderContainer.appendChild(sliderSection);

                            // Khởi tạo slider cho mỗi danh mục con (sử dụng selector riêng biệt nếu cần)
                            new Swiper('.product_swiper', {
                                slidesPerView: 4,
                                spaceBetween: 20,
                                navigation: {
                                    nextEl: '.swiper-button-next',
                                    prevEl: '.swiper-button-prev',
                                },
                                breakpoints: {
                                    0: {
                                        slidesPerView: 1
                                    },
                                    768: {
                                        slidesPerView: 2
                                    },
                                    1024: {
                                        slidesPerView: 4
                                    },
                                }
                            });
                        });
                    });
                }
            })
            .catch(error => console.error('Lỗi khi gọi API:', error));

        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("add-to-cart")) {
                addToCart(event.target.dataset.id_product);
            }
        });
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-to-wishlist')) {
                e.preventDefault();
                let productId = e.target.closest('.add-to-wishlist').getAttribute('data-product_id');

                if (!userId) {
                    alert("Vui lòng đăng nhập để thêm vào yêu thích.");
                    return;
                }

                fetch('/api/wishlist', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer ' + atob(token)
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            customer_id: userId
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success === true) {
                            swal("Thành công", "Đã thêm sản phẩm vào yêu thích!", "success");
                        } else if (data.success === false) {
                            swal("Thông báo", data.message || "Sản phẩm đã có trong yêu thích!", "info");
                        } else {
                            swal("Lỗi", "Đã có trong yêu thích!", "error");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        swal({
                            title: "Cảnh báo",
                            text: "<span style='color:red;'>Có lỗi xảy ra!</span>",
                            type: "warning",
                            html: true
                        });
                    });
            }
        });
    });

    function addToCart(productId) {
        productId = String(productId);
        if (!userId) {
            showAlert('Eror', 'Vui lòng đăng nhập trước!', 'error', 'red');
            window.location.href = "/home-login";
            return;
        }
        let productQty = document.querySelector(`.cart_product_quantity_${productId}`).value;
        if (productQty < 1) {
            swal({
                title: "Cảnh báo",
                text: "<span style='color:red;'>Số lượng trong kho không đủ!</span>",
                type: "warning",
                html: true
            });
            return;
        }
        let cartKey = `cart_${userId}`;
        let cart = JSON.parse(localStorage.getItem(cartKey)) || [];

        let productElement = document.querySelector(`.cart_product_id_${productId}`);
        let product = {
            product_id: productId,
            product_name: document.querySelector(`.cart_product_name_${productId}`).value,
            product_slug: document.querySelector(`.cart_product_slug_${productId}`).value,
            product_image: document.querySelector(`.cart_product_image_${productId}`).value,
            product_quantity: document.querySelector(`.cart_product_quantity_${productId}`).value,
            product_price: document.querySelector(`.cart_product_price_${productId}`).value,
            quantity: 1
        };

        let existingProduct = cart.find(item => item.product_id === productId);
        if (existingProduct) {
            if (existingProduct.quantity + 1 > product.product_quantity) {
                showAlert('Eror', 'Không đủ sản phẩm trong kho!', 'error', 'red');
                return;
            } else {
                existingProduct.quantity += 1;
            }
        } else {
            cart.push(product);
        }

        localStorage.setItem(cartKey, JSON.stringify(cart));
        swal("Success", "Đã thêm sản phẩm vào giỏ hàng!", "success");
        document.getElementById('cart-count').textContent = cart.length;

    }

    function loadQuickViewProduct(slug) {
        fetch(`/api/products/${slug}`)
            .then(res => res.json())
            .then(json => {
                if (!json.success) return;

                const product = json.data;

                // Gán thông tin chính
                document.getElementById("product_main_image").src = `/uploads/product/${product.product_image}`;
                document.getElementById("product_quickview_name").textContent = product.product_name;
                document.getElementById("product_quickview_id").textContent = product.product_id;
                document.getElementById("product_quickview_price").textContent = parseInt(product.product_price).toLocaleString("vi-VN") + " đ";

                // Mô tả sản phẩm
                const fullDescription = product.product_desc;
                const descriptionElement = document.getElementById("product_quickview_desc");
                const maxLength = 250; // Giới hạn chiều dài mô tả

                // Kiểm tra độ dài mô tả
                if (fullDescription.length <= maxLength) {
                    descriptionElement.innerHTML = fullDescription; // Hiển thị luôn nếu mô tả không quá 250 chữ
                    document.getElementById("more").style.display = "none"; // Ẩn nút "Xem thêm"
                } else {
                    const shortDescription = fullDescription.substring(0, maxLength) + "...";
                    descriptionElement.innerHTML = shortDescription; // Hiển thị mô tả rút gọn

                    // Hiển thị nút "Xem thêm" nếu mô tả dài
                    const moreButton = document.getElementById("more");
                    moreButton.style.display = "inline"; // Hiển thị nút "Xem thêm"
                    moreButton.setAttribute('data-expanded', 'false'); // Mặc định là chưa mở rộng
                    moreButton.onclick = function() {
                        if (moreButton.getAttribute('data-expanded') === 'false') {
                            descriptionElement.innerHTML = fullDescription; // Hiển thị mô tả đầy đủ
                            moreButton.innerHTML = " <i class='fa fa-chevron-up'></i> Rút gọn"; // Đổi tên nút thành "Rút gọn"
                            moreButton.setAttribute('data-expanded', 'true'); // Đánh dấu là mở rộng
                        } else {
                            descriptionElement.innerHTML = shortDescription; // Rút gọn mô tả lại
                            moreButton.innerHTML = " <i class='fa fa-chevron-down'></i> Xem thêm"; // Đổi tên nút lại
                            moreButton.setAttribute('data-expanded', 'false'); // Đánh dấu là chưa mở rộng
                        }
                    };
                }

                // Gallery
                const galleryContainer = document.getElementById("product_quickview_gallery");
                galleryContainer.innerHTML = "";
                product.galleries.forEach(img => {
                    const imgElement = document.createElement("img");
                    imgElement.src = `/uploads/gallery/${img.gallery_image}`;
                    imgElement.onclick = () => {
                        document.getElementById("product_main_image").src = `/uploads/gallery/${img.gallery_image}`;
                    };
                    galleryContainer.appendChild(imgElement);
                });

                // Form thêm vào giỏ + hidden input
                document.getElementById("product_quickview_add").innerHTML = `
                <input type="hidden" class="cart_product_name_${product.product_id}" value="${product.product_name}">
                <input type="hidden" class="cart_product_slug_${product.product_id}" value="${product.product_slug}">
                <input type="hidden" class="cart_product_image_${product.product_id}" value="${product.product_image}">
                <input type="hidden" class="cart_product_quantity_${product.product_id}" value="${product.product_quantity}">
                <input type="hidden" class="cart_product_price_${product.product_id}" value="${product.product_price}">
                <button type="button" class="btn btn-primary mt-2" onclick="addToCart(${product.product_id})">Thêm vào giỏ</button>
            `;
            })
            .catch(err => {
                console.error("Lỗi khi lấy dữ liệu sản phẩm:", err);
            });
    }
</script>
<style>
    .category-parent-slider-container {
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .category_child-slider {
        display: inline-flex;
        gap: 10px;
    }

    .category-child_name {
        flex: 0 0 auto;
        padding: 8px 16px;
        border: 1px solid #ccc;
        border-radius: 20px;
        background-color: #f8f8f8;
        cursor: pointer;
        white-space: nowrap;
        transition: background-color 0.3s;
    }

    .category-child_name:hover {
        background-color: #e0e0e0;
    }
</style>
@endsection