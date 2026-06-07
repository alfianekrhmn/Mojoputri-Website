@extends('layouts.admin')

@section('content')
<style>
    :root { --primary-blue: #4e73df; --bg-gray: #f8f9fc; }
    body { background-color: var(--bg-gray); font-family: 'Nunito', sans-serif; }
    .card { border-radius: 12px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); }
    .stat-card { transition: transform 0.2s; border-left: 4px solid var(--primary-blue); }
    .text-xs { font-size: .7rem; }
    .icon-circle { height: 40px; width: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #eaecf4; color: var(--primary-blue); }
</style>

<div class="container-fluid py-4">
    <div class=" align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Inventory & Operations Overview</h1>
        <p class="mb-0 text-gray-800 fw-bold">Real-time status of catalog, stock levels, and daily movement</p>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Stock Unit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle"><i class="fas fa-calendar fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 py-2" style="border-left-color: #1cc88a;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Low Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalStock) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle text-success"><i class="fas fa-boxes fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 py-2" style="border-left-color: #36b9cc;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Incoming Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle text-info"><i class="fas fa-users fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Outgoing Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalOrders) }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle"><i class="fas fa-calendar fa-2x"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Top Products By Category</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Electronics <span class="badge bg-primary rounded-pill">45%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Fashion <span class="badge bg-success rounded-pill">30%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Food & Drinks <span class="badge bg-info rounded-pill">25%</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: "Earnings",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: {!! json_encode($chartData['income']) !!},
            }],
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false, drawBorder: false } },
                y: { ticks: { maxTicksLimit: 5, padding: 10 } },
            },
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
@endsection
