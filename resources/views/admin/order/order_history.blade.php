@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{ url()->previous() }}">
                <img src="{{ asset('backend/images/back.png') }}" alt="Back"
                    style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <span>Lịch sử cập nhật đơn hàng</span>
        </div>

        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
            </div>
            <div class="col-sm-4"></div>
            <div class="col-sm-3">
                <!-- <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchOrders()">Go</button>
                    </span>
                </div> -->
            </div>

        </div>

        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="orderHistoryTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Order CODE</th>
                        <th>Lý do cập nhật</th>
                        <th>Tên Admin cập nhật</th>
                        <th>Tên Người dùng cập nhật</th>
                        <th>Trường thay đổi</th>
                        <th>Giá trị cũ</th>
                        <th>Giá trị mới</th>
                        <th>Thời gian đặt</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div id="pagination" class="text-center" style="margin-top: 20px;"></div>

    </div>
</div>
<script>
    const orderCode = "{{$order_code}}";

    document.addEventListener("DOMContentLoaded", function() {
        fetchOrderHistory();
    });

    function fetchOrderHistory() {
        fetch(`/api/get-order-history/${orderCode}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.histories.length > 0) {
                    renderHistoryTable(data.histories);
                } else {
                    document.querySelector("#orderHistoryTable tbody").innerHTML = `
                        <tr>
                            <td colspan="10" class="text-center">Không có dữ liệu lịch sử cập nhật.</td>
                        </tr>`;
                }
            })
            .catch(error => {
                console.error("Lỗi khi fetch lịch sử:", error);
            });
    }

    function renderHistoryTable(histories) {
        const tbody = document.querySelector("#orderHistoryTable tbody");
        tbody.innerHTML = "";

        histories.forEach((item, index) => {
            const isStatusField = item.field === "order_status";
            const oldValue = isStatusField ? getStatusText(item.old_value) : item.old_value || '';
            const newValue = isStatusField ? getStatusText(item.new_value) : item.new_value || '';

            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.order_code}</td>
                    <td>${item.reason_change || ''}</td>
                    <td>${item.admin_name || ''}</td>
                    <td>${item.customer_name || ''}</td>
                    <td>${item.field || ''}</td>
                    <td>${oldValue}</td>
                    <td>${newValue}</td>
                    <td>${formatDate(item.created_at)}</td>
                    <td></td>
                </tr>
            `;
            tbody.insertAdjacentHTML("beforeend", row);
        });
    }

    function getStatusText(status) {
        switch (parseInt(status)) {
            case 0:
                return 'Đang xử lý';
            case 1:
                return 'Đã xác nhận';
            case 2:
                return 'Đang giao';
            case 3:
                return 'Hoàn tất';
            case 4:
                return 'Đã hủy';
            case 5:
                return 'Hoàn hàng';
            default:
                return 'Không rõ';
        }
    }

    function formatDate(dateString) {
        const options = {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        };
        return new Date(dateString).toLocaleString("vi-VN", options);
    }
</script>


@endsection