@extends('home.home_layout')
@section('content')

<section id="order_detail" class="order-detail-section" style="padding: 80px 20px;">
    <div class="container d-flex justify-content-center" style="width:100%; text-align: center;">
        <div class="card shadow-lg p-5" style="max-width: 900px; width: 100%; border-radius: 20px;">

            <!-- Tiến trình đơn hàng -->
            <div class="order-status-icons d-flex justify-content-between align-items-center mb-5 position-relative" style="max-width: 800px; margin: auto;">
                <div class="status-item text-center step" data-step="0">
                    <div class="icon-circle"><i class="fa-solid fa-wallet fa-lg"></i></div>
                    <div class="status-label mt-2">Chờ xác nhận</div>
                </div>

                <div class="line"></div>

                <div class="status-item text-center step" data-step="1">
                    <div class="icon-circle"><i class="fa-solid fa-box fa-lg"></i></div>
                    <div class="status-label mt-2">Chờ lấy hàng</div>
                </div>

                <div class="line"></div>

                <div class="status-item text-center step" data-step="2">
                    <div class="icon-circle"><i class="fa-solid fa-truck fa-lg"></i></div>
                    <div class="status-label mt-2">Chờ giao hàng</div>
                </div>

                <div class="line"></div>

                <div class="status-item text-center step" data-step="3">
                    <div class="icon-circle"><i class="fa-regular fa-star fa-lg"></i></div>
                    <div class="status-label mt-2">Đánh giá</div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-success fw-bold text-center info m-0" style="">
                    <i class="fa-solid fa-receipt me-2"></i>Thông tin đơn hàng
                </h3>

                <button id="editShipping" class="btn btn-warning btn-sm" onclick="editShippingInfo()">
                    <i class="fa fa-pen mr-1"></i> Sửa thông tin vận chuyển
                </button>
                <button class="btn btn-warning btn-sm" onclick="orderLogInfo()" style="margin-left:5px">
                    <i class="fa fa-history mr-1"></i> Lịch sử thay đổi đơn hàng
                </button>

                <button class="btn btn-secondary btn-sm" onclick="orderPDF()" style="margin-left:5px">
                    <i class="fa fa-file mr-1"></i> In PDF
                </button>
                <div class="modal fade" id="shippingModal" tabindex="-1" role="dialog" aria-labelledby="shippingModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="shippingModalLabel">Chỉnh sửa thông tin vận chuyển</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <form id="shippingEditForm">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Họ tên người nhận</label>
                                            <input type="text" class="form-control" id="edit_name" name="customer_name" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Số điện thoại</label>
                                            <input type="text" class="form-control" id="edit_phone" name="shipping_phone" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Email</label>
                                            <input type="email" class="form-control" id="edit_email" name="shipping_email" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Địa chỉ cụ thể</label>
                                            <input type="text" class="form-control" id="edit_address" name="shipping_address" required>
                                        </div>
                                    </div>
                                    <label for="">Tính phí vận chuyển mới (bắt buộc nếu bạn đổi địa chỉ)</label>
                                    <div class="form-group">
                                        <label>Chọn thành phố</label>
                                        <select name="city" id="city" class="form-control select2 choose city">
                                            <option value="">--Chọn--</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Chọn quận huyện</label>
                                        <select name="province" id="province" class="form-control select2 choose province">
                                            <option value="">--Chọn--</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Chọn xã phường</label>
                                        <select name="ward" id="ward" class="form-control select2 choose ward">
                                            <option value="">--Chọn--</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Phí vận chuyển mới</label>
                                        <input type="text" disabled value="0" id="shipping_fee">
                                        <div id="new-fee-message" style="color:red">Phí vẫn chuyển mới có thể khác dựa vào địa chỉ mới</div>

                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-info calculate_delivery">Tính phí</button>
                                </form>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                <button class="btn btn-primary" onclick="submitShippingUpdate()">Xác nhận thay đổi</button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <div class="card shadow-sm border-0 mb-4" style="max-width: 800px; margin: auto;">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between info-row">
                                <strong>Tên khách hàng:</strong> <span class="customer-name"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 info-row">
                                <strong>Số điện thoại:</strong> <span class="customer-phone"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 info-row">
                                <strong>Email:</strong> <span class="customer-email"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 info-row">
                                <strong>Địa chỉ:</strong> <span class="customer-address"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between info-row">
                                <strong>Phương thức thanh toán:</strong> <span class="payment-method"></span>
                            </div>
                            <div class="d-flex justify-content-between info-row">
                                <strong>Phí vận chuyển:</strong> <span class="order-ship"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 info-row">
                                <strong>Mã giảm giá:</strong> <span class="order-coupon"></span>
                            </div>
                            <div class="info-row bg-light p-2 rounded">
                                <strong class="text-primary">Tổng thanh toán (Đã bao gồm thuế 8%):</strong>
                                <strong class="text-danger order-total"></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h4 class="mb-3 text-primary text-center">
                <i class="fa-solid fa-box-open me-2"></i>Sản phẩm đã đặt
            </h4>

            <div class="table-responsive">
                <table class="table table-bordered text-center shadow-sm product_details">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <a href="{{url('/orders')}}" class="btn btn-outline-primary px-4 py-2" style="border-radius: 30px;">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại Lịch sử đơn hàng
                </a>
                <button id="cancelOrderBtn" data-status="" class="btn btn-outline-danger px-4 py-2 ms-3" style="border-radius: 30px;color:red">
                    <i class="fa-solid fa-ban me-2"></i> Hủy đơn
                </button>
            </div>

        </div>
    </div>
</section>

<script>
    const orderCode = `{{$order_code}}`;
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    const userId = localStorage.getItem('user_id') || sessionStorage.getItem('user_id');
    let userOrderId = '';
    let methodCode = '0';
    let shipPrice = '0';
    let lastCalculatedAddress = "";
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const vnpTxnRef = urlParams.get('vnp_TxnRef');
        const vnpTransactionStatus = urlParams.get('vnp_TransactionStatus');

        const pathParts = window.location.pathname.split('/');
        const orderCodeFromURL = pathParts[pathParts.length - 1];

        if (!orderCodeFromURL || !userId || !token) {
            alert("Thiếu thông tin order_code hoặc chưa đăng nhập.");
            return;
        }

        if (vnpTransactionStatus === '00' && vnpTxnRef && vnpTxnRef.startsWith('Change_Address_')) {
            const storedFields = localStorage.getItem(`update_field_${userId}`);
            let updatedFields = {};

            try {
                updatedFields = storedFields ? JSON.parse(storedFields) : {};
            } catch (e) {
                console.error("Lỗi khi parse dữ liệu từ localStorage:", e);
                showNotification("Dữ liệu cập nhật không hợp lệ", "red");
                return;
            }

            fetch(`/api/update-shipping`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${atob(token)}`
                    },
                    body: JSON.stringify(updatedFields)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        swal({
                            title: "Thành công",
                            text: "<span style='color:green;'>Cập nhật địa chỉ thành công!</span>",
                            type: "success",
                            html: true
                        }, function() {
                            window.location.href = `/order_details/${orderCodeFromURL}`;
                        });

                        localStorage.removeItem(`update_field_${userId}`);
                    } else {
                        showNotification("Cập nhật thất bại: " + data.message, "red");
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi cập nhật:", error);
                    showNotification("Có lỗi xảy ra", "red");
                });
        }

        fetchOrderDetails();
    });


    function editShippingInfo() {
        $('#shippingModal').modal('show');
        document.getElementById('edit_name').value = document.querySelector('.customer-name').textContent;
        document.getElementById('edit_email').value = document.querySelector('.customer-email').textContent;
        document.getElementById('edit_phone').value = document.querySelector('.customer-phone').textContent;
        document.getElementById('edit_address').value = document.querySelector('.customer-address').textContent;

    }

    function orderLogInfo() {
        window.location.href = `/order-logs/${orderCode}`;
    }

    function orderPDF() {
        window.open(`/orders/${orderCode}/pdf`, '_blank');
    }

    function fetchOrderDetails() {
        fetch(`/api/orders/${orderCode}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success || !res.data) {
                    alert("Không tìm thấy đơn hàng.");
                    return;
                }
                userOrderId = res.data.customer_id;
                renderOrderDetails(res.data);
                updateOrderSteps(res.data.order_status);

                const cancelOrderBtn = document.getElementById('cancelOrderBtn');
                const orderStatus = parseInt(res.data.order_status);
                const editShipping = document.getElementById('editShipping');

                cancelOrderBtn.setAttribute('data-status', orderStatus);

                if ([1, 2].includes(orderStatus)) {
                    cancelOrderBtn.disabled = true; // Vô hiệu hóa nút hủy đơn
                    editShipping.disabled = true;
                    cancelOrderBtn.innerHTML = '<i class="fa-solid fa-ban me-2"></i> Vui lòng liên hệ với nhân viên';
                    cancelOrderBtn.style.color = 'gray'; // Đổi màu nút hủy
                }
                if (orderStatus == 3) {
                    cancelOrderBtn.disabled = true; // Vô hiệu hóa nút hủy đơn
                    editShipping.disabled = true;

                    cancelOrderBtn.innerHTML = '<i class="fa-solid fa-ban me-2"></i> Đã hoàn thành';
                    cancelOrderBtn.style.color = 'gray'; // Đổi màu nút hủy
                } else if (orderStatus == 4) {
                    cancelOrderBtn.disabled = true; // Vô hiệu hóa nút hủy đơn
                    editShipping.disabled = true;

                    cancelOrderBtn.innerHTML = '<i class="fa-solid fa-ban me-2"></i> Đơn đã hủy';
                    cancelOrderBtn.style.color = 'gray'; // Đổi màu nút hủy

                } else if (orderStatus == 5) {
                    cancelOrderBtn.disabled = true; // Vô hiệu hóa nút hủy đơn
                    editShipping.disabled = true;

                    cancelOrderBtn.innerHTML = '<i class="fa-solid fa-ban me-2"></i> Đơn đã hoàn';
                    cancelOrderBtn.style.color = 'gray'; // Đổi màu nút hủy

                } else {
                    cancelOrderBtn.disabled = false; // Kích hoạt nút hủy đơn
                    editShipping.disabled = true;

                    cancelOrderBtn.innerHTML = '<i class="fa-solid fa-ban me-2"></i> Hủy đơn';
                    cancelOrderBtn.style.color = 'red'; // Đổi màu nút hủy về màu đỏ
                }
            })
            .catch(err => {
                console.error(err);
                alert("Có lỗi xảy ra khi tải đơn hàng.");
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const cancelOrderBtn = document.getElementById('cancelOrderBtn');

        cancelOrderBtn.addEventListener('click', function() {
            const orderCode = `{{$order_code}}`;
            const currentStatus = cancelOrderBtn.getAttribute('data-status');
            let isSubmittingCancel = false;

            swal({
                title: "Bạn có chắc chắn muốn hủy đơn?",
                text: "Đơn hàng sẽ bị hủy và không thể khôi phục!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Xác nhận hủy",
                cancelButtonText: "Không",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    if (isSubmittingCancel) return;
                    isSubmittingCancel = true;

                    // Disable nút xác nhận
                    const confirmBtn = document.querySelector('.sweet-alert .confirm');
                    if (confirmBtn) {
                        confirmBtn.disabled = true;
                        confirmBtn.innerText = "Đang xử lý...";
                    }
                    if (!token || !userId) {
                        return swal("Lỗi", "Vui lòng đăng nhập", "error");;
                    } else if (userId != userOrderId) {
                        showNotification("Vui lòng đăng nhập đúng tài khoản đặt đơn hàng!", "red");
                        return swal("Lỗi", "Vui lòng đăng nhập đúng tài khoản đặt đơn hàng", "error");;
                    }
                    fetch(`/api/orders/${orderCode}`, {
                            method: 'PATCH',
                            headers: {
                                'Authorization': 'Bearer ' + atob(token),
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                order_status: 4
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                swal({
                                    title: "Đã hủy đơn hàng!",
                                    text: "<span style='color:red;'>Đơn hàng đã được hủy thành công.</span>",
                                    type: "success",
                                    html: true,
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                setTimeout(() => {
                                    fetchOrderDetails();
                                }, 2000);
                            } else {
                                showNotification("Có lỗi xảy ra khi hủy đơn hàng!", "red");
                                console.error(data);
                                isSubmittingCancel = false;
                            }
                        })
                        .catch(error => {
                            console.error("Lỗi khi gửi yêu cầu hủy:", error);
                            showNotification("Lỗi máy chủ. Vui lòng thử lại sau.", "red");
                            isSubmittingCancel = false;
                        });
                } else {
                    swal("Đã hủy thao tác", "", "error");
                }
            });
        });
    });


    function renderOrderDetails(data) {
        // Tính tổng sản phẩm
        let totalProduct = 0;
        data.order_details.forEach(p => {
            totalProduct += parseInt(p.product_price) * p.product_quantity;
        });

        // Mã giảm giá
        let coupon = data.order_coupon;
        let discount = 0;

        if (coupon && coupon !== 'null-0') {
            const parts = coupon.split('-');
            if (parts.length === 2) {
                coupon = parts[0];
                discount = parseInt(parts[1]) || 0;
            }
        }

        // Tính thuế, phí ship, tổng cuối
        let totalAfterDiscount = totalProduct - discount;
        let tax = Math.round(totalAfterDiscount * 0.08);
        let ship = parseInt(data.order_ship || 0);
        let finalTotal = totalAfterDiscount + tax + ship;
        shipPrice = ship;
        // Cập nhật UI
        document.querySelector('.order-total').textContent = finalTotal.toLocaleString('vi-VN') + 'đ';
        document.querySelector('.customer-name').textContent = data.shipping?.customer_name || '---';
        document.querySelector('.customer-phone').textContent = data.shipping?.shipping_phone || '---';
        document.querySelector('.customer-address').textContent = data.shipping?.shipping_address || '---';
        document.querySelector('.customer-email').textContent = data.shipping?.shipping_email || '---';
        methodCode = data.shipping?.shipping_method;
        let methodText = '---';

        if (methodCode === 1) {
            methodText = 'Thanh toán VNPAY';
        } else if (methodCode === 2) {
            methodText = 'Thanh toán khi nhận hàng';
        }

        document.querySelector('.payment-method').textContent = methodText;

        document.querySelector('.order-ship').textContent = ship.toLocaleString('vi-VN') + 'đ';
        document.querySelector('.order-coupon').textContent = discount > 0 ?
            `${coupon} (-${discount.toLocaleString("vi-VN")} đ)` :
            'Không có';

        // Hiển thị danh sách sản phẩm
        const tbody = document.querySelector('.product_details tbody');
        tbody.innerHTML = '';
        data.order_details.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                        <td class="text-start d-flex align-items-center" style="gap: 10px;">
                            <img src="{{ asset('uploads/product/') }}/${item.products.product_image}" alt="Ảnh sản phẩm" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                            <div>
                                <div class="fw-bold">${item.product_name}</div>
                                ${item.product_option ? `<div class="text-muted" style="font-size: 0.9rem;">${item.product_option}</div>` : ''}
                            </div>
                        </td>
                        <td>${parseInt(item.product_price).toLocaleString('vi-VN')}đ</td>
                        <td>${item.product_quantity}</td>
                        <td class="text-danger fw-bold">${(item.product_price * item.product_quantity).toLocaleString('vi-VN')}đ</td>
                    `;
            tbody.appendChild(tr);
        });
    }


    function updateOrderSteps(status) {
        status = parseInt(status);

        const container = document.querySelector('.order-status-icons');


        if (status === 4) {
            container.innerHTML = '';
            container.className = 'order-status-icons d-flex justify-content-center align-items-center flex-column';
            container.innerHTML = `
        <div class="status-item text-center">
            <div class="icon-circle bg-danger mb-2">
                <i class="fa-solid fa-xmark fa-2xl" style="color: white;"></i>
            </div>
            <div class="status-label mt-2 text-danger">Đã hủy</div>
        </div>
    `;
            return; // Không xử lý tiếp
        }

        if (status === 5) {
            container.innerHTML = '';
            container.className = 'order-status-icons d-flex justify-content-center align-items-center flex-column';
            container.innerHTML = `
        <div class="status-item text-center">
            <div class="icon-circle bg-warning mb-2">
                <i class="fa-solid fa-rotate-left fa-2xl" style="color: white;"></i>
            </div>
            <div class="status-label mt-2 text-warning">Đã hoàn hàng</div>
        </div>
    `;
            return;
        }

        document.querySelectorAll('.status-item').forEach(step => {
            const stepNumber = parseInt(step.getAttribute('data-step'));
            const iconCircle = step.querySelector('.icon-circle');

            // Thêm logic để đổi màu cho bước cuối cùng nếu đã hoàn tất
            if (stepNumber <= status) {
                step.classList.add('step-active');
                if (stepNumber === 3) {
                    iconCircle.style.backgroundColor = 'lime'; // Màu vàng khi hoàn tất
                }
            } else {
                step.classList.remove('step-active');
                iconCircle.style.backgroundColor = '#ccc'; // Màu xám nếu chưa hoàn tất
            }
        });

        document.querySelectorAll('.line').forEach((line, index) => {
            if (index < status) {
                line.style.backgroundColor = '#007bff'; // Màu xanh cho các bước đã hoàn tất
            } else {
                line.style.backgroundColor = '#ccc'; // Màu xám cho các bước chưa hoàn tất
            }
        });
    }





    const host = "https://provinces.open-api.vn/api/";
    let fixedAddressPart = "";

    const callAPI = (api, target) => {
        fetch(api)
            .then(response => response.json())
            .then(data => {
                if (target === "city") {
                    renderSelect(data, "city");
                } else if (target === "province") {
                    renderSelect(data.districts, "province");
                } else if (target === "ward") {
                    renderSelect(data.wards, "ward");
                }
            })
            .catch(error => console.error("Fetch error:", error));
    };

    const renderSelect = (array, selectId) => {
        const select = $("#" + selectId);
        if (select.hasClass("select2-hidden-accessible")) {
            select.select2("destroy");
        }
        let html = '<option value="" disabled selected>--Chọn--</option>';
        array.forEach(item => {
            html += `<option data-id="${item.code}" value="${item.name}">${item.name}</option>`;
        });
        select.html(html);
        select.select2({
            width: '100%',
            placeholder: "--Chọn--",
            allowClear: true
        });
    };

    const resetSelect = (selectId) => {
        const select = $("#" + selectId);
        if (select.hasClass("select2-hidden-accessible")) {
            select.select2("destroy");
        }
        select.html('<option value="" disabled selected>--Chọn--</option>');
        select.select2({
            width: '100%',
            placeholder: "--Chọn--",
            allowClear: true
        });
    };

    const cleanName = (name) => {
        return name.replace(/Tỉnh|Thành phố|Quận|Huyện|Thị xã|Thị trấn|Phường|Xã/gi, "").trim();
    };


    document.addEventListener("DOMContentLoaded", () => {
        callAPI(`${host}?depth=1`, "city");

        $("#city").on("change", function() {
            const cityId = $(this).find(":selected").data("id");
            callAPI(`${host}p/${cityId}?depth=2`, "province");
            resetSelect("province");
            resetSelect("ward");
        });

        $("#province").on("change", function() {
            const districtId = $(this).find(":selected").data("id");
            callAPI(`${host}d/${districtId}?depth=2`, "ward");
            resetSelect("ward");
        });

        $("#ward").on("change", function() {});
    });
    document.querySelector('.calculate_delivery').addEventListener('click', () => {
        const cleanedCity = cleanName($("#city").val());
        const cleanedDistrict = cleanName($("#province").val());
        const cleanedWard = cleanName($("#ward").val());
        const city = $("#city option:selected").text();
        const district = $("#province option:selected").text();
        const ward = $("#ward option:selected").text();
        const fullAddress = `${city}, ${district}, ${ward}`;

        document.getElementById('edit_address').value = fullAddress;


        fetch(`/api/shipping-fee?city=${encodeURIComponent(cleanedCity)}&district=${encodeURIComponent(cleanedDistrict)}&ward=${encodeURIComponent(cleanedWard)}`)
            .then(res => res.json())
            .then(data => {
                if (typeof data.fee === "number") {
                    const newFee = data.fee;
                    const fullAddress = `${city}, ${district}, ${ward}`;
                    showNotification("Tính phí vận chuyển thành công!", "green");
                    document.getElementById("shipping_fee").value = newFee;
                    lastCalculatedAddress = fullAddress;
                } else if (data.error) {
                    document.querySelector("#shipping_fee").textContent = `Lỗi: ${data.message}`;
                } else {
                    document.querySelector("#shipping_fee").textContent = "Không xác định phí";
                    console.warn("Phản hồi không có phí hợp lệ:", data);
                }
            })
            .catch((error) => {
                document.querySelector("#shipping_fee").textContent = "Lỗi khi gửi yêu cầu";
                console.error("Shipping fee error:", error);
            });
    });
    let isSubmitting = false;

    function submitShippingUpdate() {
        let oldName = document.querySelector('.customer-name').textContent.trim();
        let oldPhone = document.querySelector('.customer-phone').textContent.trim();
        let oldEmail = document.querySelector('.customer-email').textContent.trim();
        let oldAddress = document.querySelector('.customer-address').textContent.trim();
        let newName = document.getElementById('edit_name').value.trim();
        let newPhone = document.getElementById('edit_phone').value.trim();
        let newEmail = document.getElementById('edit_email').value.trim();
        let newAddress = document.getElementById('edit_address').value.trim();

        if (
            newAddress !== oldAddress &&
            !newAddress.startsWith(lastCalculatedAddress)
        ) {
            showNotification(
                "Bạn đã thay đổi địa chỉ nhưng chưa bấm 'Tính phí vận chuyển'. Vui lòng chọn lại địa chỉ hợp lệ để tính phí vận chuyển.",
                "red"
            );
            return;
        }


        let newFeeShip = parseInt(document.getElementById('shipping_fee').value || 0);
        let feeDiff = newFeeShip - shipPrice;

        let updatedFields = {
            order_code: orderCode,
            user_id: userId
        };

        if (newName !== oldName) updatedFields.name = newName;
        if (newPhone !== oldPhone) updatedFields.phone = newPhone;
        if (newEmail !== oldEmail) updatedFields.email = newEmail;
        if (newAddress !== oldAddress) {
            updatedFields.address = newAddress;
            if (feeDiff !== 0) {
                updatedFields.feeship = newFeeShip;
            }
        }
        if (!/^[0-9]{10,11}$/.test(newPhone)) {
            showNotification("Số điện thoại không hợp lệ", "red");
            return;
        }
        if (newEmail && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(newEmail)) {
            showNotification("Email không hợp lệ", "red");
            return;
        }

        let fieldsToCheck = {
            ...updatedFields
        };
        delete fieldsToCheck.order_code;
        delete fieldsToCheck.user_id;

        if (Object.keys(fieldsToCheck).length === 0) {
            alert("Không có thay đổi nào để cập nhật.");
            return;
        }
        alert("Những trường thay đổi:\n" + JSON.stringify(updatedFields, null, 2));

        swal({
            title: "Bạn xác nhận thay đổi thông tin?",
            text: `<span style="color: red;">Vui lòng xác nhận kĩ lại thông tin</span>`,
            type: "info",
            html: true,
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Tiếp tục",
            cancelButtonText: "Không",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (!isConfirm) return swal("Đã hủy", "", "error");

            if (isSubmitting) return;
            isSubmitting = true;

            if (methodCode == 1 && feeDiff > 0) {
                fetch(`/api/create-vnpay-shipping`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${atob(token)}`
                        },
                        body: JSON.stringify({
                            order_code: orderCode,
                            amount: feeDiff,
                            description: 'Thanh toán phần chênh lệch phí vận chuyển'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.vnpUrl) {
                            // swal("Thành công!", "Bạn sẽ được chuyển sang trang thanh toán.", "success");

                            localStorage.setItem(`update_field_${userId}`, JSON.stringify(updatedFields))

                            window.location.href = data.vnpUrl;
                        } else {
                            showNotification("Không tạo được đơn thanh toán", "red");
                            isSubmitting = false;
                        }
                    })
                    .catch(error => {
                        console.error("Lỗi VNPAY:", error);
                        showNotification("Lỗi khi tạo đơn thanh toán", "red");
                        isSubmitting = false;
                    });

            } else {
                // ✅ Với phương thức khác (COD...) hoặc không cần thanh toán thêm
                fetch(`/api/update-shipping`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${atob(token)}`
                        },
                        body: JSON.stringify(updatedFields)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            swal({
                                title: "Thành công",
                                text: "<span style='color:green;'>Cập nhật thành công!</span>",
                                type: "success",
                                html: true
                            }, function() {
                                $('#shippingModal').modal('hide');
                            })
                            fetchOrderDetails();
                        } else {
                            showNotification("Cập nhật thất bại: " + data.message, "red");
                        }
                        isSubmitting = false;
                    })
                    .catch(error => {
                        console.error("Lỗi khi cập nhật:", error);
                        showNotification("Có lỗi xảy ra", "red");
                        isSubmitting = false;
                    });
            }
        });
    }
</script>



<style>
    .info {
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        /* hoặc 0.75rem tuỳ ý */
    }

    .list-group-item {
        font-size: 1.1rem;
    }

    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin: auto;
        transition: background-color 0.3s;
    }

    .step-active .icon-circle {
        background-color: #007bff;
    }

    .step .status-label {
        font-size: 14px;
        color: #333;
        font-weight: 500;
    }

    .line {
        flex: 1;
        height: 2px;
        background-color: #ccc;
        margin: 0 0px;
        position: relative;
        top: -10px;
    }

    .step-active+.line {
        background-color: #007bff;
    }

    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }

    .table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 1rem;
        border-color: #dee2e6;
    }

    .table thead {
        background-color: #f8f9fa;
    }

    .table th:first-child,
    .table td:first-child {
        border-left: none;
    }

    .table th:last-child,
    .table td:last-child {
        border-right: none;
    }
</style>


@endsection