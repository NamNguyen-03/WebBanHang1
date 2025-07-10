@extends('admin.admin_layout')
@section('admin_content')
<div class="row">
    <div class="col-md-3 market-update-gd">
        <div class="market-update-block clr-block-2">
            <div class="col-md-4 market-update-right">
                <i class="fa fa-desktop"> </i>
            </div>
            <div class="col-md-8 market-update-left">
                <h4>Tổng sản phẩm đã bán</h4>
                <h3 id="total_productsold"></h3>
            </div>
            <div class="clearfix"> </div>
        </div>
    </div>
    <div class="col-md-3 market-update-gd">
        <div class="market-update-block clr-block-1">
            <div class="col-md-4 market-update-right">
                <i class="fa fa-users"></i>
            </div>
            <div class="col-md-8 market-update-left">
                <h4>Users</h4>
                <h3 id="total-users"></h3>
            </div>
            <div class="clearfix"> </div>
        </div>
    </div>
    <div class="col-md-3 market-update-gd">
        <div class="market-update-block clr-block-3">
            <div class="col-md-4 market-update-right">
                <i class="fa fa-usd"></i>
            </div>
            <div class="col-md-8 market-update-left">
                <h4>Tổng doanh số:</h4>
                <h3 id="total-sales"></h3>
            </div>
            <div class="clearfix"> </div>
        </div>
    </div>
    <div class="col-md-3 market-update-gd">
        <a href="{{url('/admin/orders')}}">
            <div class="market-update-block clr-block-4">
                <div class="col-md-4 market-update-right">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                </div>
                <div class="col-md-8 market-update-left">
                    <h4>Đơn hàng chưa xử lí</h4>
                    <h3 id="new-orders"></h3>
                </div>
                <div class="clearfix"> </div>
            </div>
        </a>
    </div>
</div>
<br>



<div class="row">
    <p style="text-align:center;font-size:30px;margin-bottom: 20px;"><strong>So sánh doanh thu</strong></p>
    <div class="col-md-2">
        <p>
            So sánh theo:
            <select name="" id="dashboard-compare-filter" class="dashboard-filter form-control">
                <option value="">---Chọn---</option>
                <option value="day">Ngày</option>
                <option value="month">Tháng</option>
                <option value="year">Năm</option>
            </select>
        </p>
    </div>
    <div class="col-md-2 position-relative">
        <p>
            <br>
        <div class="input-clearable">
            <input type="text" id="datepicker3" class="form-control">
            <span class="clear-btn" onclick="document.getElementById('datepicker3').value = ''">&times;</span>
        </div>
        </p>
    </div>

    <div class="col-md-2 position-relative">
        <p>
            <br>
        <div class="input-clearable">
            <input type="text" id="datepicker4" class="form-control">
            <span class="clear-btn" onclick="document.getElementById('datepicker4').value = ''">&times;</span>
        </div>
        </p>
    </div>
    <div class="col-md-1"><input style="margin-top:25px" type="button" id="btn-dashboard-compare-filter" class="btn bt-secondary btn-sm" value="So sánh"></div>

    <div class="col-md-2 ml-auto">
        <p>
            Biểu đồ:
            <select name="" id="dashboard-compare-chart-filter" class="dashboard-chart-filter form-control">
                <option value="bar">Biểu đồ cột</option>
                <option value="line">Biểu đồ line</option>
            </select>
        </p>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div id="compare-chart" class="chart-container" style="background-color: #E5E2DC;"></div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12" style="background-color: rgb(243, 251, 168);">
        <div class="">
            <div class="col-md-4">
                <p style="text-align:center;font-size:30px;margin-bottom: 20px;"><strong>Doanh số hôm nay</strong></p>
                <strong>
                    <p style="text-align:center;font-size:30px;margin-bottom: 20px;" id="today-sales"></p>

                </strong>
            </div>
            <div class="col-md-4">
                <p style="text-align:center;font-size:30px;margin-bottom: 20px;"><strong>Doanh số tháng này</strong></p>
                <strong>
                    <p style="text-align:center;font-size:30px;margin-bottom: 20px;" id="thismonth-sales"></p>
                </strong>
            </div>
            <div class="col-md-4">
                <p style="text-align:center;font-size:30px;margin-bottom: 20px;"><strong>Doanh số năm nay</strong></p>
                <strong>
                    <p style="text-align:center;font-size:30px;margin-bottom: 20px;" id="thisyear-sales"></p>
                </strong>
            </div>
        </div>
    </div>
</div>

<br>
<div class="row">
    <p style="text-align:center;font-size:30px;margin-bottom: 20px;"><strong>Thống kê đơn hàng doanh số</strong></p>
    <div class="col-md-2 position-relative">
        <p>
            Từ ngày:
        <div class="input-clearable">
            <input type="text" id="datepicker" class="form-control">
            <span class="clear-btn" onclick="document.getElementById('datepicker').value = ''">&times;</span>
        </div>
        </p>
    </div>

    <div class="col-md-2 position-relative">
        <p>
            Đến ngày:
        <div class="input-clearable">
            <input type="text" id="datepicker2" class="form-control">
            <span class="clear-btn" onclick="document.getElementById('datepicker2').value = ''">&times;</span>
        </div>
        </p>
    </div>

    <div class="col-md-1"><input style="margin-top:25px" type="button" id="btn-dashboard-filter" class="btn bt-primary btn-sm" value="Lọc theo ngày"></div>
    <div class="col-md-2">
        <p>
            Lọc theo:
            <select name="" id="dashboard-filter" class="dashboard-filter form-control">
                <option value="">---Chọn---</option>
                <option value="7ngay">7 ngày qua</option>
                <option value="thangtruoc">Tháng trước</option>
                <option value="thangnay">Tháng này</option>
                <option value="namnay">Năm nay</option>
                <option value="namngoai">Năm ngoái</option>
            </select>
        </p>
    </div>
    <div class="col-md-2 ml-auto">
        <p>
            Biểu đồ:
            <select name="" id="dashboard-chart-filter" class="dashboard-chart-filter form-control">
                <option value="bar">Biểu đồ cột</option>
                <option value="line">Biểu đồ line</option>
            </select>
        </p>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div id="chart" class="chart-container" style="background-color: #E5E2DC;"></div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div id="filter-table" class="" style="background-color: #E5E2DC;"></div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-4">
        <div>
            <p>
                Lọc theo:
                <select name="" id="dashboard-order-filter" class="dashboard-order-filter form-control" style="width: 200px;">
                    <option value="homnay">Hôm nay</option>
                    <option value="7ngay">7 ngày qua</option>
                    <option value="thangtruoc">Tháng trước</option>
                    <option value="thangnay">Tháng này</option>
                    <option value="namnay">Năm nay</option>
                </select>

            </p>
        </div>
        <br></br>
        <div id="orderStatusChart" style="height: 250px;">

        </div>
    </div>
    <div class="col-md-8">
        <div id="topProductChart">
            <h3 style="text-align:center; padding:10px 0;"><strong>Top Sản Phẩm Bán Chạy</strong></h3>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #ccc;">
                        <th style="padding: 8px; border: 1px solid #999;">#</th>
                        <th style="padding: 8px; border: 1px solid #999;">Tên sản phẩm</th>
                        <th style="padding: 8px; border: 1px solid #999;">Đã bán</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="topLowProduct">
            <h3 style="text-align:center; padding:10px 0;"><strong>Các sản phẩm ít hàng</strong></h3>

            <label for="quantityInput">Tìm các sản phẩm có số lượng ít hơn:</label>
            <input type="number" id="quantityInput" placeholder="Nhập số lượng" style="width: 100px;" min="0">
            <button onclick="searchLowStockProducts()">Tìm kiếm</button>

            <br><br>

            <table style="width: 100%; border-collapse: collapse;color:black">
                <thead>
                    <tr style="background-color: #ccc;">
                        <th style="padding: 8px; border: 1px solid #999;">#</th>
                        <th style="padding: 8px; border: 1px solid #999;">Tên sản phẩm</th>
                        <th style="padding: 8px; border: 1px solid #999;">Số lượng trong kho</th>
                    </tr>
                </thead>
                <tbody id="lowStockTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row" style="height: 200px;"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

<script>
    let chart;
    let currentFromDate = null; // Lưu mốc thời gian bắt đầu
    let currentToDate = null; // Lưu mốc thời gian kết thúc
    let allStatisticsData = [];
    let allOrdersData = [];
    let orderStatusChart;
    let orderData = [];
    document.addEventListener('DOMContentLoaded', function() {
        // Gán mặc định 30 ngày gần nhất nếu chưa có from/to
        const today = new Date();
        const toDate = today.toISOString().split('T')[0];

        const thirtyDaysAgo = new Date(today);
        thirtyDaysAgo.setDate(today.getDate() - 30);
        const fromDate = thirtyDaysAgo.toISOString().split('T')[0];

        currentFromDate = fromDate;
        currentToDate = toDate;

        fetchAndRenderStatistics(fromDate, toDate);

        fetchOrderStatusData('homnay');
        loadTopSellingProducts();
        fetchUser();
    });

    window.addEventListener('resize', function() {
        if (chart && typeof chart.redraw === 'function') {
            chart.redraw();
        }
    });

    document.getElementById('btn-dashboard-filter').addEventListener('click', function() {
        let fromDate = document.getElementById('datepicker').value;
        let toDate = document.getElementById('datepicker2').value;

        if (fromDate && toDate) {
            let from = new Date(fromDate);
            let to = new Date(toDate);
            if (to < from) {
                alert('Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu!');
                return;
            } else {
                fetchAndRenderStatistics(fromDate, toDate);
            }

        } else {
            alert('Vui lòng chọn đầy đủ khoảng thời gian!');
        }
    });

    function searchLowStockProducts() {
        const quantityInput = document.getElementById("quantityInput");
        const quantity = parseInt(quantityInput.value);


        fetch(`/api/products`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById("lowStockTableBody");
                tbody.innerHTML = '';

                if (data && Array.isArray(data.data)) {
                    // Lọc các sản phẩm có số lượng < quantity
                    const filtered = data.data.filter(product => product.product_quantity < quantity);

                    if (filtered.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3">Không có sản phẩm phù hợp.</td></tr>';
                        return;
                    }

                    filtered.forEach((product, index) => {
                        const row = `
                            <tr>
                                <td style="padding: 8px; border: 1px solid #999;">${index + 1}</td>
                                <td style="padding: 8px; border: 1px solid #999;">${product.product_name}</td>
                                <td style="padding: 8px; border: 1px solid #999;">${product.product_quantity}</td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="3">Không có dữ liệu.</td></tr>';
                }
            })
            .catch(error => {
                console.error("Lỗi khi tìm sản phẩm:", error);
                const tbody = document.getElementById("lowStockTableBody");
                tbody.innerHTML = '<tr><td colspan="3">Đã xảy ra lỗi khi tải dữ liệu.</td></tr>';
            });
    }

    document.getElementById('dashboard-filter').addEventListener('change', function() {
        let value = this.value;
        let today = new Date();
        let fromDate, toDate;

        if (value === '7ngay') {
            toDate = today;
            fromDate = new Date();
            fromDate.setDate(today.getDate() - 7);
        } else if (value === 'thangtruoc') {
            toDate = new Date(today.getFullYear(), today.getMonth(), 0);
            fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        } else if (value === 'thangnay') {
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            toDate = today;
        } else if (value === 'namnay') {
            fromDate = new Date(today.getFullYear(), 0, 1);
            toDate = today;
        } else if (value === 'namngoai') {
            fromDate = new Date(today.getFullYear() - 1, 0, 1);
            toDate = new Date(today.getFullYear() - 1, 11, 31);
        } else {
            fetchAndRenderStatistics(currentFromDate, currentToDate);

            return;
        }

        function formatDate(date) {
            let d = date.getDate().toString().padStart(2, '0');
            let m = (date.getMonth() + 1).toString().padStart(2, '0');
            let y = date.getFullYear();
            return `${y}-${m}-${d}`;
        }

        fetchAndRenderStatistics(formatDate(fromDate), formatDate(toDate));
    });

    document.getElementById('dashboard-chart-filter').addEventListener('change', function() {
        fetchAndRenderStatistics(currentFromDate, currentToDate);
    });

    function fetchAndRenderStatistics(fromDate, toDate) {
        if (allStatisticsData.length === 0) {
            fetch('/api/statistics')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        allStatisticsData = data.data.map(stat => ({
                            period: stat.order_date,
                            total_order: stat.total_order,
                            sales: stat.sales,
                            profit: stat.profit,
                            quantity: stat.quantity
                        })).sort((a, b) => new Date(a.period) - new Date(b.period));

                        renderStatisticsSummary(allStatisticsData);
                        renderStatisticsChart(filterDataByDate(fromDate, toDate));
                    }
                })
                .catch(error => console.log('Error fetching data:', error));
            return;
        }

        currentFromDate = fromDate;
        currentToDate = toDate;

        const filteredData = filterDataByDate(fromDate, toDate);

        if (filteredData.length > 0) {
            renderStatisticsChart(filteredData);
            renderFilteredSummary(filteredData);
        } else {
            document.getElementById('chart').innerHTML = '';
        }
    }

    function renderFilteredSummary(filteredData) {
        const totalOrder = filteredData.reduce((sum, stat) => sum + Number(stat.total_order), 0);
        console.log(filteredData)
        const totalSold = filteredData.reduce((sum, stat) => sum + Number(stat.quantity || 0), 0);
        const totalSales = filteredData.reduce((sum, stat) => sum + Number(stat.sales), 0);
        const totalProfit = filteredData.reduce((sum, stat) => sum + Number(stat.profit), 0);

        const tableHTML = `
        <h3 style="text-align:center;padding:10px 0;"><strong>Thống kê theo khoảng thời gian đã chọn</strong></h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #ccc;">
                    <th style="padding: 10px; border: 1px solid #999;">Số lượng đơn hàng</th>
                    <th style="padding: 10px; border: 1px solid #999;">Sản phẩm đã bán</th>
                    <th style="padding: 10px; border: 1px solid #999;">Doanh thu</th>
                    <th style="padding: 10px; border: 1px solid #999;">Lãi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px; border: 1px solid #999; text-align: center;">${totalOrder.toLocaleString()}</td>
                    <td style="padding: 10px; border: 1px solid #999; text-align: center;">${totalSold.toLocaleString()}</td>
                    <td style="padding: 10px; border: 1px solid #999; text-align: center;">${totalSales.toLocaleString()} đ</td>
                    <td style="padding: 10px; border: 1px solid #999; text-align: center;">${totalProfit.toLocaleString()} đ</td>
                </tr>
            </tbody>
        </table>
    `;

        document.getElementById('filter-table').innerHTML = tableHTML;
    }


    function renderStatisticsSummary(data) {
        // Tổng doanh số toàn bộ
        const totalSales = data.reduce((sum, stat) => sum + Number(stat.sales), 0);
        document.getElementById('total-sales').textContent = `${totalSales.toLocaleString()} đ`;

        // Doanh số hôm nay
        const today = new Date().toISOString().split('T')[0];
        const todaySales = data
            .filter(stat => stat.period === today)
            .reduce((sum, stat) => sum + Number(stat.sales), 0);
        document.getElementById('today-sales').textContent = `${todaySales.toLocaleString()} đ`;

        // Doanh số tháng này
        const now = new Date();
        const currentMonth = now.getMonth();
        const currentYear = now.getFullYear();
        const thisMonthSales = data
            .filter(stat => {
                const date = new Date(stat.period);
                return date.getFullYear() === currentYear && date.getMonth() === currentMonth;
            })
            .reduce((sum, stat) => sum + Number(stat.sales), 0);
        document.getElementById('thismonth-sales').textContent = `${thisMonthSales.toLocaleString()} đ`;
        //Doanh số năm nay
        const thisYearSales = data
            .filter(stat => new Date(stat.period).getFullYear() === currentYear)
            .reduce((sum, stat) => sum + Number(stat.sales), 0);
        document.getElementById('thisyear-sales').textContent = `${thisYearSales.toLocaleString()} đ`;
    }

    function filterDataByDate(fromDate, toDate) {
        return allStatisticsData.filter(stat => {
            const statDate = new Date(stat.period);
            return statDate >= new Date(fromDate) && statDate <= new Date(toDate);
        });
    }


    function renderStatisticsChart(statisticsData) {
        statisticsData = statisticsData.filter(stat => {
            return stat.total_order >= 0 && stat.sales >= 0 && stat.profit >= 0 && stat.period !== null;
        });

        if (statisticsData.length === 0) {
            document.getElementById('chart').innerHTML = 'Không có dữ liệu phù hợp.';
            return;
        }

        statisticsData = statisticsData.map(stat => ({
            ...stat,
            total_order: Number(stat.total_order),
            sales: Number(stat.sales),
            profit: Number(stat.profit),
        }));

        document.getElementById('chart').innerHTML = '';

        const maxSales = Math.max(...statisticsData.map(stat => stat.sales));
        const yMax = Math.ceil(maxSales / 10000000) * 10000000;

        const chartType = document.getElementById('dashboard-chart-filter').value;

        if (chartType === 'bar') {
            chart = new Morris.Bar({
                element: 'chart',
                data: statisticsData,
                xkey: 'period',
                ykeys: ['total_order', 'sales', 'profit'],
                labels: ['Tổng đơn hàng', 'Doanh thu', 'Lợi nhuận'],
                barColors: ['#0b62a4', 'red', 'lime'],
                xLabelAngle: 60,
                hideHover: 'auto',
                ymax: yMax, // Thiết lập giá trị tối đa của trục y
                yLabelFormat: function(y) {
                    return new Intl.NumberFormat('vi-VN').format(y) + ' đ'; // Định dạng hiển thị tiền tệ
                },
                ytickValues: [0, 25000000, 50000000, 100000000, 150000000], // Các mốc giá trị cho trục y
                hoverCallback: function(index, options, content, row) {
                    var labels = ['Tổng đơn hàng', 'Doanh thu', 'Lợi nhuận'];
                    var newContent = '<b>' + row.period + '</b><br>';

                    function formatCurrency(value) {
                        return new Intl.NumberFormat('vi-VN').format(value);
                    }

                    for (var i = 0; i < options.ykeys.length; i++) {
                        if (labels[i] === 'Tổng đơn hàng') {
                            newContent += '<b>' + labels[i] + ': </b>' + row[options.ykeys[i]] + '<br>';
                        } else {
                            newContent += '<b>' + labels[i] + ': </b>' + formatCurrency(row[options.ykeys[i]]) + ' đ<br>';
                        }
                    }
                    return newContent;
                },
                redraw: true,
                barSizeRatio: 1,
                hover: true
            });

        } else if (chartType === 'line') {
            chart = new Morris.Line({
                element: 'chart',
                data: statisticsData,
                xkey: 'period',
                ykeys: ['total_order', 'sales', 'profit'],
                labels: ['Tổng đơn hàng', 'Doanh thu', 'Lợi nhuận'],
                lineColors: ['#0b62a4', 'red', '#4da74d'],
                xLabelAngle: 60,
                hideHover: 'auto',
                ymax: yMax, // Thiết lập giá trị tối đa của trục y
                yLabelFormat: function(y) {
                    return new Intl.NumberFormat('vi-VN').format(y) + ' đ';
                },
                ytickValues: [0, 25000000, 50000000, 100000000, 150000000],
                hoverCallback: function(index, options, content, row) {
                    var labels = ['Tổng đơn hàng', 'Doanh thu', 'Lợi nhuận'];
                    var newContent = '<b>' + row.period + '</b><br>';

                    function formatCurrency(value) {
                        return new Intl.NumberFormat('vi-VN').format(value);
                    }

                    for (var i = 0; i < options.ykeys.length; i++) {
                        if (labels[i] === 'Tổng đơn hàng') {
                            newContent += '<b>' + labels[i] + ': </b>' + row[options.ykeys[i]] + '<br>';
                        } else {
                            newContent += '<b>' + labels[i] + ': </b>' + formatCurrency(row[options.ykeys[i]]) + ' đ<br>';
                        }
                    }
                    return newContent;
                },
                redraw: true,
                hover: true
            });
        }
    }



    //DONUT CHART


    function fetchOrderStatusData(filterValue) {
        const today = new Date();
        let fromDate, toDate;

        // Lọc theo giá trị của filterValue
        if (filterValue === 'homnay') {
            fromDate = toDate = today.toISOString().split('T')[0]; // Lọc theo ngày hôm nay
        } else if (filterValue === '7ngay') {
            fromDate = new Date(today);
            fromDate.setDate(today.getDate() - 7); // 7 ngày trước
            fromDate = fromDate.toISOString().split('T')[0];
            toDate = today.toISOString().split('T')[0];
        } else if (filterValue === 'thangtruoc') {
            fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1); // Mùng 1 tháng trước
            toDate = new Date(today.getFullYear(), today.getMonth(), 0); // Ngày cuối tháng trước
            toDate = toDate.toISOString().split('T')[0];
            fromDate = fromDate.toISOString().split('T')[0];
        } else if (filterValue === 'thangnay') {
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1); // Mùng 1 tháng này
            fromDate = fromDate.toISOString().split('T')[0]; // Đổi định dạng về YYYY-MM-DD
            toDate = today.toISOString().split('T')[0]; // Hôm nay
        } else if (filterValue === 'namnay') {
            fromDate = new Date(today.getFullYear(), 0, 1); // Mùng 1 tháng 1 năm nay
            fromDate = fromDate.toISOString().split('T')[0]; // Đổi định dạng về YYYY-MM-DD
            toDate = today.toISOString().split('T')[0]; // Hôm nay
        } else {
            return;
        }


        fetch(`/api/orders`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let orderData = data.data;
                    const totalUnprocessed = orderData.filter(order => order.order_status === 0).length;

                    // Cập nhật vào h3
                    document.getElementById('new-orders').textContent =
                        `${totalUnprocessed}`;

                    orderData = orderData.filter(order => {
                        const orderDate = new Date(order.created_at);
                        const orderDateString = orderDate.toISOString().split('T')[0];
                        return orderDateString >= fromDate && orderDateString <= toDate;
                    });

                    const statusCounts = [0, 0, 0, 0, 0, 0];
                    orderData.forEach(order => {
                        const status = order.order_status;
                        if (status >= 0 && status <= 5) {
                            statusCounts[status]++;
                        }
                    });

                    // Hiển thị biểu đồ donut
                    renderOrderStatusChart(statusCounts);
                } else {
                    // Nếu không có dữ liệu
                    renderOrderStatusChart([0, 0, 0, 0, 0, 0]);
                }
            })
            .catch(error => console.log('Error fetching orders:', error));
    }


    // Hàm vẽ biểu đồ donut
    function renderOrderStatusChart(statusCounts) {
        if (orderStatusChart) {
            orderStatusChart.setData([{
                    label: 'Đang xử lý',
                    value: statusCounts[0]
                },
                {
                    label: 'Đã xử lý',
                    value: statusCounts[1]
                },
                {
                    label: 'Đang giao',
                    value: statusCounts[2]
                },
                {
                    label: 'Hoàn thành',
                    value: statusCounts[3]
                },
                {
                    label: 'Đã hủy',
                    value: statusCounts[4]
                },
                {
                    label: 'Đã hoàn trả',
                    value: statusCounts[5]
                }
            ]);
        } else {
            orderStatusChart = new Morris.Donut({
                element: 'orderStatusChart',
                data: [{
                        label: 'Đang xử lý',
                        value: statusCounts[0]
                    },
                    {
                        label: 'Đã xử lý',
                        value: statusCounts[1]
                    },
                    {
                        label: 'Đang giao',
                        value: statusCounts[2]
                    },
                    {
                        label: 'Hoàn thành',
                        value: statusCounts[3]
                    },
                    {
                        label: 'Đã hủy',
                        value: statusCounts[4]
                    },
                    {
                        label: 'Đã hoàn trả',
                        value: statusCounts[5]
                    }
                ],
                colors: ['#FEED59', '#913E9F', '#44A6F4', 'green', '#EE5350', '#2A353B'],
                formatter: function(x) {
                    return x + ' đơn';
                }
            });
        }
    }

    // Lắng nghe sự kiện thay đổi filter
    document.getElementById('dashboard-order-filter').addEventListener('change', function() {
        const value = this.value;
        fetchOrderStatusData(value);
    });






    function loadTopSellingProducts() {
        fetch('/api/products')
            .then(res => res.json())
            .then(data => {
                if (!data || !Array.isArray(data.data)) {
                    console.error('Dữ liệu không hợp lệ');
                    return;
                }

                const products = data.data;

                // ======= Top sản phẩm bán chạy =======
                const totalProductSold = products.reduce((total, product) => total + product.product_sold, 0);
                document.getElementById('total_productsold').innerText = totalProductSold;

                const sortedBySold = [...products].sort((a, b) => b.product_sold - a.product_sold);
                const topSoldProducts = sortedBySold.slice(0, 10);

                let soldHTML = '';
                topSoldProducts.forEach((product, index) => {
                    soldHTML += `
                    <tr style="background-color:${
                        (index + 1 === 1) ? '#4CAF50' :
                        (index + 1 === 2) ? '#FFC107' :
                        (index + 1 === 3) ? '#FF9800' :
                        '#ccc'
                    }">
                        <td style="padding: 8px; border: 1px solid #999;">${index + 1}</td>
                        <td style="padding: 8px; border: 1px solid #999;">${product.product_name}</td>
                        <td style="padding: 8px; border: 1px solid #999;">${product.product_sold}</td>
                    </tr>
                `;
                });

                const soldTbody = document.querySelector('#topProductChart table tbody');
                if (soldTbody) {
                    soldTbody.innerHTML = soldHTML;
                }

                // ======= Top sản phẩm sắp hết hàng =======
                const sortedByQuantity = [...products]
                    .filter(p => p.product_quantity !== null && p.product_quantity >= 0)
                    .sort((a, b) => a.product_quantity - b.product_quantity);

                const lowStockProducts = sortedByQuantity.slice(0, 10);

                let lowHTML = '';
                lowStockProducts.forEach((product, index) => {
                    lowHTML += `
                    <tr style="background-color:${
                        (product.product_quantity <= 10) ? 'rgb(255, 142, 142)' : 
                        (product.product_quantity <= 30) ? '#ff9800' : 
                        '#ccc'
                    }">
                        <td style="padding: 8px; border: 1px solid #999;">${index + 1}</td>
                        <td style="padding: 8px; border: 1px solid #999;">${product.product_name}</td>
                        <td style="padding: 8px; border: 1px solid #999;">${product.product_quantity}</td>
                    </tr>
                `;
                });

                const lowTbody = document.querySelector('#topLowProduct table tbody');
                if (lowTbody) {
                    lowTbody.innerHTML = lowHTML;
                }

            })
            .catch(function(error) {
                console.error("Lỗi khi tải sản phẩm:", error);
            });
    }





    function fetchUser() {
        fetch(`/api/users`)
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    let total = data.data.length;
                    console.log(total);
                    document.getElementById('total-users').innerText = total;
                } else {
                    console.log("Lỗi dữ liệu");
                }
            })
            .catch(error => {
                console.error("lỗi API:" + error);
            })
    }


    $(document).ready(function() {
        $("#datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            maxDate: 0, // Không cho chọn ngày sau hôm nay
            onSelect: function(selectedDate) {
                $("#datepicker2").datepicker("option", "minDate", selectedDate); // Khi chọn ngày bắt đầu, đặt minDate cho ngày kết thúc
            }
        });

        $("#datepicker2").datepicker({
            dateFormat: "yy-mm-dd",
            maxDate: 0, // Không cho chọn ngày sau hôm nay
            onSelect: function(selectedDate) {
                $("#datepicker").datepicker("option", "maxDate", selectedDate); // Khi chọn ngày kết thúc, đặt maxDate cho ngày bắt đầu
            }
        });
    });
</script>
<script>
    function initDatePickers(type) {
        $("#datepicker3, #datepicker4").datepicker("destroy");
        setTimeout(() => {
            $(".ui-datepicker").removeClass("hide-calendar");
        }, 0);
        if (type === "day") {
            $("#datepicker3, #datepicker4").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true,
                maxDate: 0
            });
        } else if (type === "month") {
            $("#datepicker3, #datepicker4").datepicker({
                dateFormat: "yy-mm",
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                maxDate: 0,
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val($.datepicker.formatDate('yy-mm', new Date(year, month, 1)));
                },
                beforeShow: function(input, inst) {
                    $(input).datepicker("widget").addClass("hide-calendar");
                }
            });
        } else if (type === "year") {
            $("#datepicker3, #datepicker4").datepicker({
                dateFormat: "yy",
                changeYear: true,
                showButtonPanel: true,
                maxDate: 0,
                onClose: function(dateText, inst) {
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val(year);
                },
                beforeShow: function(input, inst) {
                    $(input).datepicker("widget").addClass("hide-calendar");
                }
            });
        }
    }

    $(document).ready(function() {
        initDatePickers("day");

        $("#dashboard-compare-filter").on("change", function() {
            const selected = $(this).val();
            document.getElementById('datepicker3').value = '';
            document.getElementById('datepicker4').value = '';
            initDatePickers(selected);
        });
    });
    let currentChartData = null;
    let currentCompareType = null;

    document.getElementById('btn-dashboard-compare-filter').addEventListener('click', function() {
        const type = document.getElementById('dashboard-compare-filter').value;
        const from = document.getElementById('datepicker3').value;
        const to = document.getElementById('datepicker4').value;
        const chartType = document.getElementById('dashboard-compare-chart-filter').value;

        if (!type || !from || !to) {
            alert("Vui lòng chọn loại so sánh và khoảng thời gian.");
            return;
        }

        fetch('/api/statistics')
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    const chartData = prepareCompareChartData(type, res.data, from, to);
                    currentChartData = chartData;
                    currentCompareType = type;
                    drawCompareChart(chartData, type, chartType);
                } else {
                    alert("Không có dữ liệu phù hợp.");
                }
            })
            .catch(error => {
                console.error(error);
                alert("Lỗi khi lấy dữ liệu.");
            });
    });

    document.getElementById('dashboard-compare-chart-filter').addEventListener('change', function() {
        const chartType = this.value;
        if (currentChartData && currentCompareType) {
            drawCompareChart(currentChartData, currentCompareType, chartType);
        }
    });



    function prepareCompareChartData(type, data, fromDate, toDate) {
        if (type === 'day') {
            return [{
                label: 'So sánh',
                [fromDate]: getSalesByExactDate(data, fromDate),
                [toDate]: getSalesByExactDate(data, toDate)
            }];
        }

        if (type === 'month') {
            return [{
                label: 'So sánh',
                [fromDate]: getSalesByMonth(data, fromDate),
                [toDate]: getSalesByMonth(data, toDate)
            }];
        }


        if (type === 'year') {
            const result = [];

            for (let m = 1; m <= 12; m++) {
                const mm = m < 10 ? `0${m}` : `${m}`;
                const monthLabel = `Th${m}`;
                result.push({
                    label: monthLabel,
                    [fromDate]: getSalesByMonth(data, `${fromDate}-${mm}`),
                    [toDate]: getSalesByMonth(data, `${toDate}-${mm}`)
                });
            }

            return result;
        }
        return [];
    }

    function getSalesByExactDate(data, dateStr) {
        const item = data.find(d => d.order_date === dateStr);
        return item ? parseInt(item.sales) : 0;
    }

    function getSalesByMonth(data, yearMonth) {
        return data
            .filter(d => d.order_date.startsWith(yearMonth))
            .reduce((sum, d) => sum + parseInt(d.sales), 0);
    }

    function drawCompareChart(chartData, type, chartType = 'bar') {
        $('#compare-chart').empty();

        const ykeys = Object.keys(chartData[0]).filter(k => k !== 'label');
        const colors = ['#bdbdbd', '#e74c3c', '#2ecc71', '#f39c12', '#8e44ad', '#16a085'];

        const commonConfig = {
            element: 'compare-chart',
            data: chartData,
            xkey: 'label',
            ykeys: ykeys,
            labels: ykeys,
            hideHover: 'auto',
            resize: true
        };

        if (chartType === 'bar') {
            commonConfig.barColors = function(row, series, type) {
                return colors[series.index % colors.length];
            };
            new Morris.Bar(commonConfig);
        } else if (chartType === 'line') {
            commonConfig.lineColors = colors;
            commonConfig.parseTime = false; // không phải mốc thời gian thực
            new Morris.Line(commonConfig);
        }
    }
</script>

<style>
    #datepicker3.hide-calendar+.ui-datepicker .ui-datepicker-calendar,
    #datepicker4.hide-calendar+.ui-datepicker .ui-datepicker-calendar {
        display: none;
    }

    #datepicker3.hide-calendar+.ui-datepicker .ui-datepicker-close,
    #datepicker4.hide-calendar+.ui-datepicker .ui-datepicker-close {
        display: none;
    }

    #datepicker3.hide-calendar~.ui-datepicker .ui-datepicker-calendar,
    #datepicker4.hide-calendar~.ui-datepicker .ui-datepicker-calendar {
        display: none;
    }
</style>

@endsection