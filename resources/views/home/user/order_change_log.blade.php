@extends('home.home_layout')
@section('content')
<div class="container mt-5 mb-5">
    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">
        <i class="fa fa-arrow-left"></i> Trở về
    </a>

    <h3 class="mb-4">Lịch sử thay đổi đơn hàng của bạn</h3>

    <div class="accordion" id="orderChangeAccordion">
        <!-- Mỗi đơn hàng là 1 accordion item -->
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userId = localStorage.getItem("user_id") || sessionStorage.getItem("user_id");
        const token = localStorage.getItem("auth_token") || sessionStorage.getItem("auth_token");
        const orderCode = `{{$order_code}}`; // thay thế giá trị order_code động

        if (!userId || !token) {
            alert("Thiếu thông tin đăng nhập.");
            return;
        }

        const userCache = {};



        function fetchUserName(userId) {
            if (!userId) return Promise.resolve('');
            if (userCache[userId]) return Promise.resolve(userCache[userId]);

            return fetch(`/api/users/${userId}`, {
                    headers: {
                        'Authorization': `Bearer ${atob(token)}`
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data && data.data.name) {
                        userCache[userId] = data.data.name;
                        return data.data.name;
                    }
                    return '';
                })
                .catch(() => '');
        }

        function loadChangeHistory() {
            // alert(orderCode);
            fetch(`/api/orders/${orderCode}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success || !data.data || !data.data.change_logs) {
                        document.getElementById('orderChangeAccordion').innerHTML = "<p>Không có dữ liệu thay đổi.</p>";
                        return;
                    }

                    const changeLogs = data.data.change_logs;
                    const container = document.getElementById('orderChangeAccordion');
                    container.innerHTML = '';

                    if (changeLogs.length === 0) {
                        container.innerHTML = "<p>Không có thay đổi nào trong đơn hàng của bạn.</p>";
                        return;
                    }

                    // Lấy tên người thay đổi từng change log, nối Promise
                    let promiseChain = Promise.resolve();

                    promiseChain = promiseChain.then(() => {
                        let rowsPromises = changeLogs.map(log => {
                            if (log.admin_id) {
                                return Promise.resolve({
                                    log,
                                    changerName: 'Admin'
                                });
                            } else if (log.user_id) {
                                return fetchUserName(log.user_id).then(name => ({
                                    log,
                                    changerName: name || 'Không xác định'
                                }));
                            } else {
                                return Promise.resolve({
                                    log,
                                    changerName: 'Không xác định'
                                });
                            }
                        });


                        return Promise.all(rowsPromises).then(rowsData => {
                            let rowsHtml = rowsData.map(({
                                log,
                                changerName
                            }) => `
                        <tr>
                            <td>${log.field}</td>
                            <td>${log.old_value}</td>
                            <td>${log.new_value}</td>
                            <td>${log.reason_change||''}</td>
                            <td>${new Date(log.changed_at).toLocaleString()}</td>
                            <td>${changerName || 'Không xác định'}</td>
                        </tr>
                    `).join('');

                            const card = `
                        <div class="card mb-3">
                            <div class="card-header bg-info text-white" data-toggle="collapse" data-target="#collapseOrderChange" style="cursor:pointer;">
                                Mã đơn: ${data.data.order_code}
                            </div>
                            <div id="collapseOrderChange" class="collapse show" data-parent="#orderChangeAccordion">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Trường thay đổi</th>
                                                <th>Giá trị cũ</th>
                                                <th>Giá trị mới</th>
                                                <th>Lý do thay đổi</th>
                                                <th>Thời gian thay đổi</th>
                                                <th>Người thay đổi</th>
                                            </tr>
                                        </thead>
                                        <tbody>${rowsHtml}</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    `;

                            container.insertAdjacentHTML('beforeend', card);
                        });
                    });

                    promiseChain.catch(() => {
                        container.innerHTML = "<p>Lỗi khi tải dữ liệu thay đổi.</p>";
                    });
                })
                .catch(error => {
                    console.error("Lỗi:", error);
                    document.getElementById('orderChangeAccordion').innerHTML = "<p>Lỗi khi tải dữ liệu.</p>";
                });
        }

        loadChangeHistory();
    });
</script>

@endsection