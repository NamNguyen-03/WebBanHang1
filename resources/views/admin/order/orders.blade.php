@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{ url('/admin') }}">
                <img src="{{ asset('backend/images/back.png') }}" alt="Back"
                    style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <span>Liệt kê đơn hàng</span>
        </div>

        <div class="row w3-res-tb">
            <div class="col-sm-3">
                <p>Từ ngày:
                <div class="input-clearable">
                    <input type="text" id="datepicker" class="form-control">
                    <span class="clear-btn" onclick="document.getElementById('datepicker').value = ''">&times;</span>
                </div>
                </p>
            </div>
            <div class="col-sm-3">
                <p>Đến ngày:
                <div class="input-clearable">
                    <input type="text" id="datepicker2" class="form-control">
                    <span class="clear-btn" onclick="document.getElementById('datepicker2').value = ''">&times;</span>
                </div>
                </p>
            </div>
            <div class="col-sm-1">
                <input style="margin-top:25px" type="button" id="btn-dashboard-filter" class="btn btn-primary btn-sm" value="Lọc theo ngày">
            </div>
            <div class="col-sm-1">
                <input style="margin-top:25px" type="button" id="btn-reset-filter" class="btn btn-secondary btn-sm" value="Xóa lọc">
            </div>
        </div>

        <br>
        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="orderTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>OrderCode</th>
                        <th>Tên khách</th>
                        <th>Địa chỉ</th>
                        <th>Thời gian đặt</th>
                        <th>Tình trạng</th>
                        <th style="width:30px;"></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    let allOrders = [];
    let dataTable = null;
    // const adminTokenRaw = localStorage.getItem("admin_token");
    // const adminToken = atob(adminTokenRaw);

    function getStatusText(status) {
        switch (status) {
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
                return 'Đã hoàn';
            default:
                return 'Không rõ';
        }
    }

    function getStatusColor(status) {
        switch (status) {
            case 0:
                return '#ff8040';
            case 1:
                return '#00a7d1';
            case 2:
                return '#0050b9';
            case 3:
                return '#06b900';
            case 4:
                return 'grey';
            default:
                return 'black';
        }
    }

    function renderDataTable(data) {
        if (dataTable) {
            dataTable.clear();
            dataTable.rows.add(data);
            dataTable.draw();
            return;
        }

        dataTable = $('#orderTable').DataTable({
            data: data,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // STT
                    }
                },
                {
                    data: 'order_code',
                    defaultContent: '---'
                },
                {
                    data: 'shipping.customer_name',
                    defaultContent: '---'
                },
                {
                    data: 'shipping.shipping_address',
                    defaultContent: '---'
                },
                {
                    data: 'created_at',
                    defaultContent: '---'
                },
                {
                    data: 'order_status',
                    render: function(data, type, row) {
                        let color = getStatusColor(data);
                        let text = getStatusText(data);
                        return `<span style="color:${color}">${text}</span>`;
                    }
                },
                {
                    data: 'order_code',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `
                            <a href="/admin/order-details/${data}" class="active">
                                <i class="fa fa-eye text-success text-active" style="font-size: 18px;"></i>
                            </a>
                            <a href="javascript:void(0)" onclick="deleteOrder('${data}')" class="active" style="margin-left:8px;">
                                <i class="fa fa-trash text" style="color:red"></i>
                            </a>
                        `;
                    }
                }
            ],
            paging: true,
            searching: true,
            ordering: true,
            language: {
                search: "Tìm kiếm:",
                lengthMenu: "Hiển thị _MENU_ đơn hàng",
                info: "Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ đơn hàng",
                infoEmpty: "Không có đơn hàng nào",
                zeroRecords: "Không tìm thấy đơn hàng",
                paginate: {
                    first: "Đầu",
                    last: "Cuối",
                    next: "Tiếp",
                    previous: "Trước"
                }
            }
        });
    }

    function fetchOrders() {
        fetch(`{{ url('/api/orders') }}`)
            .then(res => res.json())
            .then(resData => {
                if (resData.success) {
                    allOrders = resData.data;
                    renderDataTable(allOrders);
                } else {
                    alert(resData.message || 'Không có đơn hàng nào.');
                }
            })
            .catch(error => {
                console.error('Lỗi khi tải đơn hàng:', error);
                alert('Đã xảy ra lỗi khi tải đơn hàng.');
            });
    }

    function deleteOrder(orderCode) {
        if (!adminTokenRaw) {
            alert("Bạn cần đăng nhập để thực hiện thao tác này!");
            window.location.href = "{{ url('admin-login') }}";
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa đơn hàng này không?")) {
            fetch(`{{ url('/api/orders/') }}/${orderCode}`, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": "Bearer " + adminToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Xóa đơn hàng thành công!");
                        fetchOrders();
                    } else {
                        alert("Lỗi: " + (data.message || "Không thể xóa đơn hàng."));
                    }
                })
                .catch(error => alert(error.message));
        }
    }

    function filterOrdersByDate(from, to) {
        if (!from || !to) {
            alert("Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc.");
            return;
        }

        const fromDate = new Date(from);
        const toDate = new Date(to);
        toDate.setHours(23, 59, 59, 999);

        const filtered = allOrders.filter(order => {
            if (!order.created_at) return false;
            const orderDate = new Date(order.created_at);
            return orderDate >= fromDate && orderDate <= toDate;
        });

        renderDataTable(filtered);
    }

    document.addEventListener('DOMContentLoaded', () => {
        fetchOrders();

        $("#datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            onSelect: function(selectedDate) {
                $("#datepicker2").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#datepicker2").datepicker({
            dateFormat: "yy-mm-dd"
        });

        document.getElementById("btn-dashboard-filter").addEventListener("click", () => {
            const fromDate = document.getElementById("datepicker").value;
            const toDate = document.getElementById("datepicker2").value;
            filterOrdersByDate(fromDate, toDate);
        });

        document.getElementById("btn-reset-filter").addEventListener("click", () => {
            $("#datepicker").val('');
            $("#datepicker2").val('');
            renderDataTable(allOrders);
        });
    });
</script>

@endsection