@extends('home.home_layout')
@section('sidebar_content')
@include('home.home_sidebar')
@endsection
@section('product_slider')

<section id="slider"><!--slider-->
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div id="slider-carousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                    </ol>

                    <div class="carousel-inner" id="banner-carousel">
                    </div>

                    <a href="#slider-carousel" class="left control-carousel hidden-xs" data-slide="prev">
                        <i class="fa fa-angle-left"></i>
                    </a>
                    <a href="#slider-carousel" class="right control-carousel hidden-xs" data-slide="next">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section><!--/slider-->
<section id="top_selling_section" style="padding: 20px 0; background:rgb(183, 249, 243);">
    <div class="container">
        <h2 style="font-size: 28px; text-align: center; margin-bottom: 25px;">Top 5 sản phẩm bán chạy</h2>
        <div id="top_selling_products"></div>
    </div>
</section>


<section id="product_slider_section" style="background-color: #f8f9fa; padding-top: 20px;">
    <div class="container">
        <div class="product_slider_header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0;">
            <h2 style="margin: 0; font-size: 30px;">Sản phẩm nổi bật</h2>
            <a href="{{ url('/products') }}" style="color: #1a73e8; text-decoration: none;">Xem tất cả</a>
        </div>

        <div class="swiper product_swiper">
            <div class="swiper-wrapper" id="product_slider_track">
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>
@endsection
@section('content')
<div class="col-sm-9 padding-right">

    <div class="features_items">
        <h2 class="title text-center">Sản phẩm mới nhất</h2>

        <div id="product-list" class="row">
        </div>

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
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='/home-cart'">Đi đến giỏ hàng</button>
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

    <ul class="pagination pagination-sm m-t-none m-b-none">
    </ul>
</div>
<script>
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

    function fetchProducts() {
        fetch(`{{ url('/api/products') }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const products = data.data;

                    // Sort để lấy top 3 sản phẩm bán chạy nhất
                    const topSellingProducts = [...products]
                        .sort((a, b) => b.product_sold - a.product_sold)
                        .slice(0, 5);

                    // HTML cho top bán chạy
                    const orderClass = ['top-first', 'top-second', 'top-third', 'top-fourth', 'top-fifth'];
                    const topContainer = document.getElementById("top_selling_products");
                    topContainer.innerHTML = `
                                    <div class="top-selling-wrapper">
                                        ${topSellingProducts.slice(0, 5).map((product, index) => {
                                            const productURL = `/product-details/${product.product_slug}`;
                                            return `
                                                <div class="top-product-card ${orderClass[index]}">
                                                    <div class="top-rank">#${index + 1}</div>
                                                    <a href="${productURL}" style="color:white">
                                                        <img src="/uploads/product/${product.product_image}" alt="${product.product_name}" />
                                                    
                                                    <div class="top-product-info">
                                                        <h3>${product.product_name.length > 30 ?
                                                                product.product_name.substring(0, 30) + "..." :
                                                                product.product_name}</h3>
                                                        <p class="price">${Number(product.product_price).toLocaleString('vi-VN')}đ</p>
                                                        <p class="sold">Đã bán: ${product.product_sold}</p>
                                                    </div>
                                                    </a>
                                                </div>
                                            `;
                                        }).join('')}
                                    </div>
                                `;


                    // Featured Products
                    const activeProducts = products.filter(product => product.product_status == 1);

                    const shuffledProducts = activeProducts.sort(() => 0.5 - Math.random());

                    const featuredProducts = shuffledProducts.slice(0, 6);
                    const sliderProducts = shuffledProducts.slice(6);

                    const productList = document.getElementById("product-list");
                    productList.innerHTML = "";

                    featuredProducts.forEach(product => {
                        const productName = product.product_name.length > 50 ?
                            product.product_name.substring(0, 50) + "..." :
                            product.product_name;

                        const productURL = `/product-details/${product.product_slug}`;

                        const productHTML = `
                                <div class="col-sm-4" style="height:550px">
                                    <div class="product-image-wrapper">
                                        <div class="single-products">
                                            <div class="productinfo text-center">
                                                <form>
                                                    @csrf
                                                    <input type="hidden" value="${product.product_id}" class="cart_product_id_${product.product_id}">
                                                    <input type="hidden" value="${product.product_name}" class="cart_product_name_${product.product_id}">
                                                    <input type="hidden" value="${product.product_slug}" class="cart_product_slug_${product.product_id}">
                                                    <input type="hidden" value="${product.product_image}" class="cart_product_image_${product.product_id}">
                                                    <input type="hidden" value="${product.product_quantity}" class="cart_product_quantity_${product.product_id}">
                                                    <input type="hidden" value="${product.product_price}" class="cart_product_price_${product.product_id}">

                                                    <a href="${productURL}">
                                                        <img src="/uploads/product/${product.product_image}" alt="${product.product_name}" />
                                                        <h2>${Number(product.product_price).toLocaleString('vi-VN')}đ</h2>
                                                        <p>${productName}</p>
                                                    </a>
                                                    <button data-id_product="${product.product_id}" type="button" class="btn btn-default add-to-cart">Thêm giỏ hàng</button>
                                                    <button type="button" class="btn btn-default quick-view"
                                                        data-toggle="modal" data-target="#quickview"
                                                        onclick="loadQuickViewProduct('${encodeURIComponent(product.product_slug)}')">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="choose">
                                            <ul class="nav nav-pills nav-justified">
                                                <li><a href="#" class="add-to-wishlist" data-product_id="${product.product_id}"><i class="fa fa-heart"></i> Yêu thích</a></li>
                                                <li><a href="#" class="add-to-compare" data-product_id="${product.product_id}" data-product_image="${product.product_image}" data-product_name="${product.product_name}"><i class="fa fa-plus-square"></i> So sánh</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            `;
                        productList.innerHTML += productHTML;
                    });

                    // Slider
                    const track = document.getElementById('product_slider_track');
                    if (sliderProducts.length > 0) {
                        const html = sliderProducts.map(product => {
                            const productURL = `/product-details/${product.product_slug}`;
                            return `
                            <div class="swiper-slide">
                                <div class="product-image-wrapper">
                                    <div class="single-products">
                                        <div class="productinfo text-center">
                                            <form>
                                                @csrf
                                                <input type="hidden" value="${product.product_id}" class="cart_product_id_${product.product_id}">
                                                <input type="hidden" value="${product.product_name}" class="cart_product_name_${product.product_id}">
                                                <input type="hidden" value="${product.product_slug}" class="cart_product_slug_${product.product_id}">
                                                <input type="hidden" value="${product.product_image}" class="cart_product_image_${product.product_id}">
                                                <input type="hidden" value="${product.product_quantity}" class="cart_product_quantity_${product.product_id}">
                                                <input type="hidden" value="${product.product_price}" class="cart_product_price_${product.product_id}">
                                                <input type="hidden" value="1" class="cart_product_qty_${product.product_id}">

                                                <a href="${productURL}">
                                                    <img src="/uploads/product/${product.product_image}" alt="${product.product_name}" />
                                                    <h2>${new Intl.NumberFormat('vi-VN').format(product.product_price)}đ</h2>
                                                    <p>${product.product_name}</p>
                                                </a>
                                                <button data-id_product="${product.product_id}" type="button" class="btn btn-default add-to-cart">Thêm giỏ hàng</button>
                                                <button type="button" class="btn btn-default quick-view"
                                                    data-toggle="modal" data-target="#quickview"
                                                    onclick="loadQuickViewProduct('${encodeURIComponent(product.product_slug)}')">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="choose">
                                        <ul class="nav nav-pills nav-justified">
                                            <li><a href="#" class="add-to-wishlist" data-product_id="${product.product_id}"><i class="fa fa-heart"></i> Yêu thích</a></li>
                                            <li><a href="#" class="add-to-compare" data-product_id="${product.product_id}" data-product_image="${product.product_image}" data-product_name="${product.product_name}"><i class="fa fa-plus-square"></i> So sánh</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;
                        }).join('');
                        track.innerHTML = html;

                        new Swiper('.product_swiper', {
                            slidesPerView: 3,
                            spaceBetween: 20,
                            loop: true,
                            autoplay: {
                                delay: 3000,
                                disableOnInteraction: false,
                            },
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
                                992: {
                                    slidesPerView: 3
                                }
                            }
                        });
                    } else {
                        track.innerHTML = "<p>Không có sản phẩm cho slider.</p>";
                    }
                } else {
                    document.getElementById("product-list").innerHTML = "<p>Không có sản phẩm nào.</p>";
                }
            })
            .catch(error => {
                console.error("Lỗi khi lấy sản phẩm:", error);
                document.getElementById("product-list").innerHTML = "<p>Không thể tải sản phẩm.</p>";
            });
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
                const maxLength = 250;

                // Kiểm tra độ dài mô tả
                if (fullDescription.length <= maxLength) {
                    descriptionElement.innerHTML = fullDescription;
                    document.getElementById("more").style.display = "none";
                } else {
                    const shortDescription = fullDescription.substring(0, maxLength) + "...";
                    descriptionElement.innerHTML = shortDescription;

                    // Hiển thị nút "Xem thêm" nếu mô tả dài
                    const moreButton = document.getElementById("more");
                    moreButton.style.display = "inline";
                    moreButton.setAttribute('data-expanded', 'false');
                    moreButton.onclick = function() {
                        if (moreButton.getAttribute('data-expanded') === 'false') {
                            descriptionElement.innerHTML = fullDescription;
                            moreButton.innerHTML = " <i class='fa fa-chevron-up'></i> Rút gọn";
                            moreButton.setAttribute('data-expanded', 'true');
                        } else {
                            descriptionElement.innerHTML = shortDescription;
                            moreButton.innerHTML = " <i class='fa fa-chevron-down'></i> Xem thêm";
                            moreButton.setAttribute('data-expanded', 'false');
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

    function addToCart(productId) {
        if (!userId) {
            swal({
                title: "Cảnh báo",
                text: "<span style='color:red;'>Vui lòng đăng nhập trước khi thêm vào giỏ!</span>",
                type: "warning",
                html: true
            });
            window.location.href = "/login";
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
        const product = {
            product_id: productId,
            product_name: document.querySelector(`.cart_product_name_${productId}`).value,
            product_image: document.querySelector(`.cart_product_image_${productId}`).value,
            product_quantity: document.querySelector(`.cart_product_quantity_${productId}`).value,
            product_slug: document.querySelector(`.cart_product_slug_${productId}`).value,
            product_price: document.querySelector(`.cart_product_price_${productId}`).value,
            quantity: 1
        };

        const cartKey = `cart_${userId}`;
        const currentCart = JSON.parse(localStorage.getItem(cartKey)) || [];

        const existingIndex = currentCart.findIndex(item => item.product_id === product.product_id);

        if (existingIndex !== -1) {
            currentCart[existingIndex].quantity += product.quantity;
            if (currentCart[existingIndex].quantity > product.product_quantity) {
                currentCart[existingIndex].quantity = product.product_quantity;
                swal({
                    title: "Cảnh báo",
                    text: "<span style='color:red;'>Không đủ sản phẩm trong kho!</span>",
                    type: "warning",
                    html: true
                });
                return;
            }
        } else {
            currentCart.push(product);
        }

        localStorage.setItem(cartKey, JSON.stringify(currentCart));
        swal("Success", "Đã thêm sản phẩm vào giỏ hàng!", "success");
        document.getElementById('cart-count').textContent = currentCart.length;

    }

    document.addEventListener("DOMContentLoaded", () => {
        fetchProducts();
        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("add-to-cart")) {
                const productId = event.target.dataset.id_product;
                addToCart(productId);
            }
        });
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-to-wishlist')) {
                e.preventDefault();
                let productId = e.target.closest('.add-to-wishlist').getAttribute('data-product_id');

                if (!userId || !token) {
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
</script>

<style>
    .top-selling-wrapper {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        /* để các thẻ xếp từ trên xuống dưới */
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .top-product-card {
        width: 200px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        position: relative;
    }


    .top-product-card img {
        width: 100%;
        height: auto;
    }

    .top-product-info {
        padding: 10px;
    }

    .top-product-info h3 {
        font-size: 16px;
        margin: 5px 0;
    }

    .top-product-info .price {
        font-weight: bold;
        color: #e91e63;
    }

    .top-product-info .sold {
        font-size: 14px;
        color: #666;
    }

    .top-rank {
        position: absolute;
        top: 8px;
        left: 8px;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 13px;
    }

    /* Hạng 1: cao nhất (ít margin-top nhất) */
    .top-first {
        background-color: rgb(33, 229, 30);
        color: white;
        margin-top: 0px;
    }

    /* Hạng 2 */
    .top-second {
        background-color: #fbc02d;
        margin-top: 20px;
    }

    /* Hạng 3 */
    .top-third {
        background-color: #fb8c00;
        margin-top: 40px;
    }

    /* Hạng 4 */
    .top-fourth {
        background-color: rgb(239, 69, 109);
        color: white;
        margin-top: 60px;
    }

    /* Hạng 5: thấp nhất (nhiều margin-top nhất) */
    .top-fifth {
        background-color: rgb(131, 23, 139);
        color: white;
        margin-top: 80px;
    }
</style>
@endsection