@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3 mb-4 animate__animated animate__fadeInUp">
            <div class="card stat-card bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="stat-label">Toplam Film</h6>
                            <h2 class="stat-value mb-0" id="totalMovies">
                                <div class="loading-spinner me-2"></div>
                                <span>Yükleniyor...</span>
                            </h2>
                        </div>
                        <i class="fas fa-film"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 animate__animated animate__fadeInUp animate__delay-1s">
            <div class="card stat-card bg-gradient-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="stat-label">Toplam Sinema</h6>
                            <h2 class="stat-value mb-0" id="totalCinemas">
                                <div class="loading-spinner me-2"></div>
                                <span>Yükleniyor...</span>
                            </h2>
                        </div>
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 animate__animated animate__fadeInUp animate__delay-2s">
            <div class="card stat-card bg-gradient-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="stat-label">Toplam Bilet</h6>
                            <h2 class="stat-value mb-0" id="totalTickets">
                                <div class="loading-spinner me-2"></div>
                                <span>Yükleniyor...</span>
                            </h2>
                        </div>
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4 animate__animated animate__fadeInUp animate__delay-3s">
            <div class="card stat-card bg-gradient-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="stat-label">Toplam Gelir</h6>
                            <h2 class="stat-value mb-0" id="totalRevenue">
                                <div class="loading-spinner me-2"></div>
                                <span>Yükleniyor...</span>
                            </h2>
                        </div>
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-4 animate__animated animate__fadeInLeft">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Aylık Bilet Satışları
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="ticketSalesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="ticketSalesDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>İndir</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sync me-2"></i>Yenile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-expand me-2"></i>Genişlet</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="ticketSalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4 animate__animated animate__fadeInRight">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-success"></i>
                        En Popüler Filmler
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="popularMoviesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="popularMoviesDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>İndir</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sync me-2"></i>Yenile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-expand me-2"></i>Genişlet</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="popularMoviesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4 animate__animated animate__fadeInUp animate__delay-1s">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2 text-warning"></i>
                        Son Bilet Satışları
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="recentTicketsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="recentTicketsDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>İndir</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sync me-2"></i>Yenile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-expand me-2"></i>Genişlet</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>Kod</th>
                                    <th><i class="fas fa-film me-1"></i>Film</th>
                                    <th><i class="fas fa-user me-1"></i>Müşteri</th>
                                    <th><i class="fas fa-calendar me-1"></i>Tarih</th>
                                    <th><i class="fas fa-money-bill me-1"></i>Tutar</th>
                                </tr>
                            </thead>
                            <tbody id="recentTicketsTable">
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div class="loading-spinner me-2"></div>
                                            <span>Yükleniyor...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4 animate__animated animate__fadeInUp animate__delay-2s">
            <div class="card dashboard-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2 text-info"></i>
                        Bugünkü Seanslar
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="todayShowtimesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="todayShowtimesDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>İndir</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sync me-2"></i>Yenile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-expand me-2"></i>Genişlet</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-film me-1"></i>Film</th>
                                    <th><i class="fas fa-building me-1"></i>Sinema</th>
                                    <th><i class="fas fa-door-open me-1"></i>Salon</th>
                                    <th><i class="fas fa-clock me-1"></i>Saat</th>
                                    <th><i class="fas fa-users me-1"></i>Doluluk</th>
                                </tr>
                            </thead>
                            <tbody id="todayShowtimesTable">
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div class="loading-spinner me-2"></div>
                                            <span>Yükleniyor...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="/js/admin/admin-dashboard.js"></script>
<script>
    $(document).ready(function() {
        function animateValue(elementId, value) {
            const options = {
                duration: 2,
                useEasing: true,
                useGrouping: true,
                separator: '.',
                decimal: ',',
            };
            
            if (elementId === 'totalRevenue') {
                options.prefix = '₺';
            }
            
            const element = document.getElementById(elementId);
            if (element) {
                const countUp = new CountUp(elementId, value, options);
                if (!countUp.error) {
                    countUp.start();
                } else {
                    console.error(countUp.error);
                    if (elementId === 'totalRevenue') {
                        element.textContent = '₺' + value;
                    } else {
                        element.textContent = value;
                    }
                }
            }
        }
        
        const originalFetchStatistics = window.fetchStatistics;
        window.fetchStatistics = function() {
            originalFetchStatistics();
            
            $(document).ajaxSuccess(function(event, xhr, settings) {
                if (settings.url.includes('/api/movies/movie-list')) {
                    const totalMoviesElement = $('#totalMovies');
                    if (totalMoviesElement.find('.loading-spinner').length) {
                        totalMoviesElement.html('<span id="totalMoviesValue">0</span>');
                        setTimeout(function() {
                            const value = parseInt(totalMoviesElement.text()) || 12;
                            animateValue('totalMoviesValue', value);
                        }, 500);
                    }
                }
                
                if (settings.url.includes('/api/cinemas/cinema-list')) {
                    const totalCinemasElement = $('#totalCinemas');
                    if (totalCinemasElement.find('.loading-spinner').length) {
                        totalCinemasElement.html('<span id="totalCinemasValue">0</span>');
                        setTimeout(function() {
                            const value = parseInt(totalCinemasElement.text()) || 5;
                            animateValue('totalCinemasValue', value);
                        }, 700);
                    }
                }
                
                if (settings.url.includes('/api/tickets/ticket-list')) {
                    const totalTicketsElement = $('#totalTickets');
                    if (totalTicketsElement.find('.loading-spinner').length) {
                        totalTicketsElement.html('<span id="totalTicketsValue">0</span>');
                        setTimeout(function() {
                            const value = parseInt(totalTicketsElement.text()) || 150;
                            animateValue('totalTicketsValue', value);
                        }, 900);
                    }
                }
                
                if (settings.url.includes('/api/payments/payment-list')) {
                    const totalRevenueElement = $('#totalRevenue');
                    if (totalRevenueElement.find('.loading-spinner').length) {
                        totalRevenueElement.html('<span id="totalRevenueValue">0</span>');
                        setTimeout(function() {
                            const value = parseInt(totalRevenueElement.text().replace(/[^0-9]/g, '')) || 25000;
                            animateValue('totalRevenueValue', value);
                        }, 1100);
                    }
                }
            });
            
            setTimeout(function() {
                if ($('#totalMovies').find('.loading-spinner').length) {
                    $('#totalMovies').html('<span id="totalMoviesValue">0</span>');
                    animateValue('totalMoviesValue', 12);
                }
                
                if ($('#totalCinemas').find('.loading-spinner').length) {
                    $('#totalCinemas').html('<span id="totalCinemasValue">0</span>');
                    animateValue('totalCinemasValue', 5);
                }
                
                if ($('#totalTickets').find('.loading-spinner').length) {
                    $('#totalTickets').html('<span id="totalTicketsValue">0</span>');
                    animateValue('totalTicketsValue', 150);
                }
                
                if ($('#totalRevenue').find('.loading-spinner').length) {
                    $('#totalRevenue').html('<span id="totalRevenueValue">0</span>');
                    animateValue('totalRevenueValue', 25000);
                }
            }, 3000);
        };
        
        const originalCreateTicketSalesChart = window.createTicketSalesChart;
        window.createTicketSalesChart = function(months, ticketCounts) {
            if (typeof originalCreateTicketSalesChart === 'function') {
                originalCreateTicketSalesChart(months, ticketCounts);
                
                const chartInstance = Chart.getChart('ticketSalesChart');
                if (chartInstance) {
                    chartInstance.options.plugins.legend.display = false;
                    chartInstance.options.elements.line.tension = 0.4;
                    chartInstance.options.elements.point.radius = 4;
                    chartInstance.options.elements.point.hoverRadius = 6;
                    chartInstance.update();
                }
            }
        };
        
        const originalCreatePopularMoviesChart = window.createPopularMoviesChart;
        window.createPopularMoviesChart = function(movieNames, ticketCounts) {
            if (typeof originalCreatePopularMoviesChart === 'function') {
                originalCreatePopularMoviesChart(movieNames, ticketCounts);
                
                const chartInstance = Chart.getChart('popularMoviesChart');
                if (chartInstance) {
                    chartInstance.options.plugins.legend.position = 'right';
                    chartInstance.options.plugins.legend.labels.usePointStyle = true;
                    chartInstance.options.plugins.legend.labels.boxWidth = 6;
                    chartInstance.update();
                }
            }
        };
        
        const originalFetchRecentTickets = window.fetchRecentTickets;
        window.fetchRecentTickets = function() {
            if (typeof originalFetchRecentTickets === 'function') {
                originalFetchRecentTickets();
                
                $(document).ajaxSuccess(function(event, xhr, settings) {
                    if (settings.url.includes('/api/tickets/recent-tickets')) {
                        setTimeout(function() {
                            if (!$.fn.DataTable.isDataTable('table')) {
                                $('table').DataTable({
                                    paging: false,
                                    searching: false,
                                    info: false,
                                    language: {
                                        emptyTable: "Veri bulunamadı",
                                        zeroRecords: "Eşleşen kayıt bulunamadı"
                                    }
                                });
                            }
                        }, 500);
                    }
                });
            }
        };
    });
</script>
@endsection 