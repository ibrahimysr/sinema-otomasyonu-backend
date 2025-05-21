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
            <div class="card stat-card bg-gradient-danger text-white h-100">
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
                            <li><a class="dropdown-item" href="#" id="refreshTicketSales"><i class="fas fa-sync me-2"></i>Yenile</a></li>
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
                            <li><a class="dropdown-item" href="#" id="refreshPopularMovies"><i class="fas fa-sync me-2"></i>Yenile</a></li>
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
                            <li><a class="dropdown-item" href="#" id="refreshRecentTickets"><i class="fas fa-sync me-2"></i>Yenile</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="recentTicketsTable">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>Kod</th>
                                    <th><i class="fas fa-film me-1"></i>Film</th>
                                    <th><i class="fas fa-user me-1"></i>Müşteri</th>
                                    <th><i class="fas fa-calendar me-1"></i>Tarih</th>
                                    <th><i class="fas fa-money-bill me-1"></i>Tutar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables tarafından doldurulacak -->
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
                            <li><a class="dropdown-item" href="#" id="refreshTodayShowtimes"><i class="fas fa-sync me-2"></i>Yenile</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="todayShowtimesTable">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-film me-1"></i>Film</th>
                                    <th><i class="fas fa-building me-1"></i>Sinema</th>
                                    <th><i class="fas fa-door-open me-1"></i>Salon</th>
                                    <th><i class="fas fa-clock me-1"></i>Saat</th>
                                    <th><i class="fas fa-users me-1"></i>Doluluk</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables tarafından doldurulacak -->
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        const recentTicketsTable = $('#recentTicketsTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: false,
            paging: false,
            info: false,
            ordering: false,
            ajax: {
                url: '/api/dashboard/recent-tickets',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        localStorage.removeItem('token');
                        window.location.href = '/login';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Veriler yüklenirken bir hata oluştu: ' + xhr.statusText
                        });
                    }
                }
            },
            columns: [
                { data: 'ticket_code', name: 'ticket_code' },
                { data: 'movie', name: 'movie' },
                { data: 'customer', name: 'customer' },
                { data: 'date', name: 'date' },
                { data: 'price', name: 'price' }
            ],
            language: {
                url: '/js/i18n/tr.json'
            }
        });

        const todayShowtimesTable = $('#todayShowtimesTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: false,
            paging: false,
            info: false,
            ordering: false,
            ajax: {
                url: '/api/dashboard/today-showtimes',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        localStorage.removeItem('token');
                        window.location.href = '/login';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Veriler yüklenirken bir hata oluştu: ' + xhr.statusText
                        });
                    }
                }
            },
            columns: [
                { data: 'movie', name: 'movie' },
                { data: 'cinema', name: 'cinema' },
                { data: 'hall', name: 'hall' },
                { data: 'time', name: 'time' },
                { data: 'occupancy', name: 'occupancy' }
            ],
            language: {
                url: '/js/i18n/tr.json'
            }
        });

        function loadStatistics() {
            $.ajax({
                url: '/api/dashboard/statistics',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const stats = response.data;
                        $('#totalMovies').html(stats.total_movies);
                        $('#totalCinemas').html(stats.total_cinemas);
                        $('#totalTickets').html(stats.total_tickets);
                        $('#totalRevenue').html(formatCurrency(stats.total_revenue));
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        localStorage.removeItem('token');
                        window.location.href = '/login';
                    }
                }
            });
        }

        function loadPopularMovies() {
            $.ajax({
                url: '/api/dashboard/popular-movies',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                success: function(response) {
                    console.log('Popüler filmler yanıtı:', response);
                    if (response.success && response.data) {
                        const movies = response.data;
                        createPopularMoviesChart(movies);
                    } else {
                        console.error('Popüler filmler verisi alınamadı:', response);
                        const exampleMovies = [
                            {id: 1, title: 'Inception', ticket_count: 25},
                            {id: 2, title: 'The Dark Knight', ticket_count: 20},
                            {id: 3, title: 'Interstellar', ticket_count: 18},
                            {id: 4, title: 'Dune', ticket_count: 15},
                            {id: 5, title: 'Avatar', ticket_count: 12}
                        ];
                        createPopularMoviesChart(exampleMovies);
                    }
                },
                error: function(xhr) {
                    console.error('Popüler filmler API hatası:', xhr);
                    if (xhr.status === 401) {
                        localStorage.removeItem('token');
                        window.location.href = '/login';
                    } else {
                        const exampleMovies = [
                            {id: 1, title: 'Inception', ticket_count: 25},
                            {id: 2, title: 'The Dark Knight', ticket_count: 20},
                            {id: 3, title: 'Interstellar', ticket_count: 18},
                            {id: 4, title: 'Dune', ticket_count: 15},
                            {id: 5, title: 'Avatar', ticket_count: 12}
                        ];
                        createPopularMoviesChart(exampleMovies);
                    }
                }
            });
        }

        function loadTicketSales() {
            $.ajax({
                url: '/api/dashboard/ticket-sales',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                success: function(response) {
                    console.log('Bilet satışları yanıtı:', response);
                    if (response.success && response.data) {
                        const data = response.data;
                        createTicketSalesChart(data.months, data.counts);
                    } else {
                        console.error('Bilet satışları verisi alınamadı:', response);
                        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        const counts = [15, 20, 25, 30, 35, 40, 45, 50, 45, 40, 35, 30];
                        createTicketSalesChart(months, counts);
                    }
                },
                error: function(xhr) {
                    console.error('Bilet satışları API hatası:', xhr);
                    if (xhr.status === 401) {
                        localStorage.removeItem('token');
                        window.location.href = '/login';
                    } else {
                        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        const counts = [15, 20, 25, 30, 35, 40, 45, 50, 45, 40, 35, 30];
                        createTicketSalesChart(months, counts);
                    }
                }
            });
        }

        function createPopularMoviesChart(movies) {
            const ctx = document.getElementById('popularMoviesChart').getContext('2d');
            
            if (window.popularMoviesChart && typeof window.popularMoviesChart.destroy === 'function') {
                window.popularMoviesChart.destroy();
            }
            
            const labels = movies.map(movie => movie.title);
            const data = movies.map(movie => movie.ticket_count);
            
            window.popularMoviesChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        title: {
                            display: true,
                            text: 'En Çok Bilet Satılan Filmler'
                        }
                    }
                }
            });
        }

        function createTicketSalesChart(months, counts) {
            const ctx = document.getElementById('ticketSalesChart').getContext('2d');
            
            if (window.ticketSalesChart && typeof window.ticketSalesChart.destroy === 'function') {
                window.ticketSalesChart.destroy();
            }
            
            window.ticketSalesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Bilet Satışları',
                        data: counts,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Aylık Bilet Satışları'
                        }
                    }
                }
            });
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(amount);
        }

        $('#refreshRecentTickets').on('click', function(e) {
            e.preventDefault();
            recentTicketsTable.ajax.reload();
        });

        $('#refreshTodayShowtimes').on('click', function(e) {
            e.preventDefault();
            todayShowtimesTable.ajax.reload();
        });

        $('#refreshPopularMovies').on('click', function(e) {
            e.preventDefault();
            loadPopularMovies();
        });

        $('#refreshTicketSales').on('click', function(e) {
            e.preventDefault();
            loadTicketSales();
        });

        loadStatistics();
        
        setTimeout(function() {
            loadPopularMovies();
        }, 500);
        
        setTimeout(function() {
            loadTicketSales();
        }, 1000);
    });
</script>
@endsection 