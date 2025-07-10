@extends('home.home_layout')
@section('sidebar_content')
@include('home.home_sidebar')
@endsection
@section('content')
<div class="col-sm-9 padding-right">

    <div class="features_items" style="min-height: 300px;"><!--features_items-->
        <h2 class="title text-center">Tìm kiếm</h2>
        <h2>Tìm kiếm: <span id="keyword"></span></h2>
        <div id="filter-criteria">
            <button id="sort-low-high" class="filter-btn">
                <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V4.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3-.007-.007a.5.5 0 0 0-.7.007l-3 3a.5.5 0 1 0 .708.708L7.5 4.707V11.5A.5.5 0 0 0 8 12z" />
                </svg>
                Giá thấp → cao
            </button>

            <button id="sort-high-low" class="filter-btn">
                <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v6.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3-.007.007a.5.5 0 0 1-.7-.007l-3-3a.5.5 0 1 1 .708-.708L7.5 11.293V4.5A.5.5 0 0 1 8 4z" />
                </svg>
                Giá cao → thấp
            </button>

            <button id="filter-by-price" class="filter-btn">
                <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2h-1a2 2 0 0 0-2 2v1H8v2h5v1a2 2 0 0 0 2 2h1v2a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6z" />
                </svg>
                Lọc theo giá
            </button>
            <div id="price-dropdown" class="dropdown-content">
                <div class="price-custom">
                    <label>Tùy chọn giá tiền:</label>
                    <div class="slider-inputs">
                        <input type="text" class="min-price" id="min-price" value="0" /> -
                        <input type="text" class="max-price" id="max-price" value="5000000" />
                    </div>
                    <div class="slider-range">
                        <input type="range" id="range-min" min="0" max="50000000" value="0" step="100000">
                        <input type="range" id="range-max" min="0" max="50000000" value="5000000" step="100000">
                    </div>
                    <div class="price-actions">
                        <button id="clear-filter">Bỏ chọn</button>
                        <button id="apply-filter">Xem kết quả</button>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div id="product-list" class="min-h-[300px] flex items-center justify-center ">
            <!-- Sản phẩm sẽ được hiển thị tại đây -->
        </div>

    </div><!--features_items-->
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
    <ul class="pagination pagination-sm m-t-none m-b-none">

    </ul>
</div>
<style>
    .custom-modal {
        max-width: 50%;
        width: 50%;
    }
</style>


<script>
    let allProducts = [];
    let filteredProducts = [];
    let currentPage = 1;
    const productsPerPage = 6; // Số sản phẩm mỗi trang
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');

    function getSearchQuery() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('q') || '';
    }
    const keyword = getSearchQuery();
    if (document.getElementById('keyword')) {
        document.getElementById('keyword').textContent = keyword;
    }

    function fetchSearchProduct() {


        fetch(`/api/products?search=${encodeURIComponent(keyword)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    allProducts = data.data.filter(product => product.product_status == 1);
                    renderProducts(allProducts);
                    renderPagination(allProducts);
                } else {
                    document.getElementById("product-list").innerHTML = "<p>Không có sản phẩm nào.</p>";
                }
            })
            .catch(error => {
                console.error("Lỗi khi gọi API tìm kiếm:", error);
                document.getElementById("product-list").innerHTML = "<p>Không thể tải sản phẩm.</p>";
            });
    }

    function sortProducts(order) {
        let productsToSort = filteredProducts.length > 0 ? filteredProducts : allProducts;

        if (order === 'low-high') {
            productsToSort.sort((a, b) => a.product_price - b.product_price);
        } else if (order === 'high-low') {
            productsToSort.sort((a, b) => b.product_price - a.product_price);
        }

        renderProducts(productsToSort);
    }

    function renderProducts(products) {
        let productList = document.getElementById("product-list");
        productList.innerHTML = "";

        let start = (currentPage - 1) * productsPerPage;
        let end = start + productsPerPage;
        let productsToShow = products.slice(start, end);

        if (productsToShow.length === 0) {
            productList.innerHTML = "<p>Không có sản phẩm nào.</p>";
            return;
        }

        productsToShow.forEach(product => {
            let productName = product.product_name.length > 50 ?
                product.product_name.substring(0, 50) + "..." :
                product.product_name;

            let productURL = `/product-details/${product.product_slug}`;

            let productHTML = `
        <div class="col-sm-4" >
                    <div class="product-image-wrapper" >
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
                                        <img src="{{ asset('uploads/product/') }}/${product.product_image}" alt="${product.product_name}" />
                                        <h2>${new Intl.NumberFormat('vi-VN').format(product.product_price)}đ</h2>                                     
                                        <p>${productName}</p>
                                    </a>
                                    <button data-id_product="${product.product_id}" type="button" class="btn btn-default add-to-cart" name="add-to-cart">Thêm giỏ hàng</button>
                                    <button
                                        type="button"
                                        class="btn btn-default quick-view"
                                        data-toggle="modal"
                                        data-target="#quickview"
                                        onclick="loadQuickViewProduct('${encodeURIComponent(product.product_slug)}')">
                                        <i class="fa fa-eye"></i>
                                    </button>                                </form>
                            </div>
                        </div>
                        <div class="choose">
                            <ul class="nav nav-pills nav-justified">
                            <li>
                                <a href="#" class="add-to-wishlist" data-product_id="${product.product_id}">
                                    <i class="fa fa-heart"></i> Yêu thích
                                </a>
                            </li>                                   
                                        <li><a href="#" class="add-to-compare" data-product_id="${product.product_id}" data-product_image="${product.product_image}" data-product_name="${product.product_name}"><i class="fa fa-plus-square"></i> So sánh</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            productList.innerHTML += productHTML;
        });
    }

    function renderFilteredProducts(products) {
        filteredProducts = products;

        renderProducts(filteredProducts);
        renderPagination(filteredProducts);
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

    function renderPagination(products) {
        let pagination = document.querySelector(".pagination");
        pagination.innerHTML = "";

        let totalPages = Math.ceil(products.length / productsPerPage);
        if (totalPages <= 1) return;

        let prevButton = document.createElement("li");
        prevButton.className = "page-item";
        prevButton.innerHTML = `<a href="#" class="page-link">«</a>`;
        prevButton.addEventListener("click", function(event) {
            event.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                renderProducts(products);
                renderPagination(products);
            }
        });
        pagination.appendChild(prevButton);

        for (let i = 1; i <= totalPages; i++) {
            let pageItem = document.createElement("li");
            pageItem.className = "page-item " + (i === currentPage ? "active" : "");
            pageItem.innerHTML = `<a href="#" class="page-link">${i}</a>`;
            pageItem.addEventListener("click", function(event) {
                event.preventDefault();
                currentPage = i;
                renderProducts(products);
                renderPagination(products);
            });
            pagination.appendChild(pageItem);
        }

        let nextButton = document.createElement("li");
        nextButton.className = "page-item";
        nextButton.innerHTML = `<a href="#" class="page-link">»</a>`;
        nextButton.addEventListener("click", function(event) {
            event.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                renderProducts(products);
                renderPagination(products);
            }
        });
        pagination.appendChild(nextButton);
    }
    document.getElementById('apply-filter').addEventListener('click', function() {
        let min = parseInt(minPrice.value.replace(/[.,]/g, ''));
        let max = parseInt(maxPrice.value.replace(/[.,]/g, ''));

        // Lọc sản phẩm theo khoảng giá
        filteredProducts = allProducts.filter(product => {
            return product.product_price >= min && product.product_price <= max;
        });

        currentPage = 1;
        renderFilteredProducts(filteredProducts);
    });
    document.getElementById('clear-filter').addEventListener('click', function() {
        document.getElementById('min-price').value = 0;
        document.getElementById('max-price').value = 5000000;

        document.getElementById('range-min').value = 0;
        document.getElementById('range-max').value = 5000000;
        filteredProducts = [];

        renderProducts(allProducts);
        renderPagination(allProducts);
    });

    function addToCart(productId) {
        if (!userId) {
            swal({
                title: "Cảnh báo",
                text: "<span style='color:red;'>Vui lòng đăng nhập trước khi thêm vào giỏ!</span>",
                type: "warning",
                html: true
            });
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
                swal({
                    title: "Cảnh báo",
                    text: "<span style='color:red;'>Không đủ sản phẩm trong kho!</span>",
                    type: "warning",
                    html: true
                });
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

    document.addEventListener("DOMContentLoaded", () => {
        fetchSearchProduct();
        document.getElementById('sort-low-high').addEventListener('click', () => {
            sortProducts('low-high');
        });
        document.getElementById('sort-high-low').addEventListener('click', () => {
            sortProducts('high-low');
        });
        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("add-to-cart")) {
                addToCart(event.target.dataset.id_product);
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


@endsection