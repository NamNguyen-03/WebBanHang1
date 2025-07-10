@extends('admin.admin_layout')
@section('admin_content')

<div class="table-agile-info">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="{{url('/admin/all-users') }}">
                <img src="{{asset('backend/images/back.png')}}" alt="Back" style="float: left; margin-right: 10px; margin-top:11px;width: 40px; height: 40px;">
            </a>
            <!-- <a href="{{url('/admin/add-brand')}}" class="btn btn-default" style="height: 40px; line-height: 30px;float: left; margin-right: 10px; margin-top:10px;">
                Thêm thương hiệu
            </a> -->
            Danh sách đơn hàng của <p id="userName"></p>
        </div>

        <div class="row w3-res-tb">
            <div class="col-sm-5 m-b-xs">
                <!-- <button id="showAllBtn">Hiện tất cả thương hiệu</button> -->

            </div>
            <div class="col-sm-4">
            </div>
            <div class="col-sm-3">
                <!-- <div class="input-group">
                    <input type="text" class="input-sm form-control" placeholder="Search" id="searchInput">
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-default" type="button" onclick="searchBrands()">Search</button>
                    </span>
                </div> -->
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped b-t b-light" id="userOrderTable">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>OrderCode</th>
                        <th>Ngày tạo</th>
                        <th>Tình trạng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div id="pagination" class="text-center" style="margin-top: 20px;"></div>
    </div>
</div>
<!-- Modal đổi mật khẩu -->
<!-- <div class="modal fade" id="updatePasswordModal" tabindex="-1" role="dialog" aria-labelledby="updatePasswordLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="updatePasswordForm">
                <div class="modal-header">
                    <h4 class="modal-title" id="updatePasswordLabel">Đổi mật khẩu người dùng</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="password_user_id">

                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Nhập lại mật khẩu</label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_password">Mật khẩu admin</label>
                        <input type="password" class="form-control" id="admin_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                </div>
            </form>
        </div>
    </div>
</div> -->
@endsection
@section('scripts')
<script>
    const userId = "{{$user_id}}";
    document.addEventListener("DOMContentLoaded", function() {
        fetchUserOrders(userId);
    });

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

    function fetchUserOrders(userId) {
        fetch(`/api/users/${userId}`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    renderUserOrders(res.data.orders);
                    document.getElementById('userName').innerText = res.data.name;
                } else {
                    alert("Không thể lấy danh sách đơn hàng người dùng!");
                }
            })
            .catch(error => {
                console.error("Lỗi khi gọi API:", error);
            });
    }

    function renderUserOrders(orders) {
        const tbody = document.querySelector("#userOrderTable tbody");
        tbody.innerHTML = "";

        orders.forEach((order, index) => {
            const tr = document.createElement("tr");
            let orderStatus = getStatusText(order.order_status);
            let statusColor = '';
            switch (order.order_status) {
                case 0:
                    statusColor = '#ff8040';
                    break;
                case 1:
                    statusColor = '#00a7d1';
                    break;
                case 2:
                    statusColor = '#0050b9';
                    break;
                case 3:
                    statusColor = '#06b900';
                    break;
                case 4:
                    statusColor = 'grey';
                    break;
            }
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${order.order_code}</td>
                <td>${order.created_at}</td>
                <td style="color:${statusColor}">${orderStatus}</td>
                <td>
                    <a href="/admin/order-details/${order.order_code}" class="active">
                        <i class="fa fa-eye text-success text-active"></i>
                    </a>
                    
                    
                </td>
            `;

            tbody.appendChild(tr);
        });
        if ($.fn.DataTable.isDataTable('#userOrderTable')) {
            $('#userOrderTable').DataTable().destroy();
        }

        $('#userOrderTable').DataTable({
            paging: true,
            searching: true,
            ordering: true
        });
    }
</script>

@endsection