<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinema Otomasyonu - Admin Paneli</title>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <style>
        :root {
            --primary-color: #3a0ca3;
            --secondary-color: #4361ee;
            --accent-color: #7209b7;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --info-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            overflow-x: hidden;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }
        
        .main-content {
            transition: all 0.3s ease;
            background-color: #f5f7fa;
        }
        
        .content-header {
            padding: 20px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .dashboard-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 20px;
            background-color: white;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-card .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            font-weight: 600;
        }
        
        .dashboard-card .card-body {
            padding: 20px;
        }
        
        .stat-card {
            border-radius: 15px;
            padding: 20px;
            height: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            z-index: -1;
        }
        
        .stat-card i {
            font-size: 3rem;
            opacity: 0.2;
            position: absolute;
            bottom: 10px;
            right: 10px;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover i {
            transform: scale(1.2) rotate(15deg);
            opacity: 0.3;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .table-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: rgba(0, 0, 0, 0.02);
            border-bottom: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 15px;
        }
        
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -250px;
                width: 250px;
                z-index: 1030;
                transition: all 0.3s ease;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
            
            .toggle-sidebar {
                display: block !important;
            }
        }
        
        .toggle-sidebar {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1040;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .toggle-sidebar:hover {
            background-color: var(--secondary-color);
            transform: scale(1.1);
        }
        
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--primary-color);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        .preloader.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        
        .preloader .spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }
        
        .bg-gradient-primary {
            background: linear-gradient(45deg, #3a0ca3, #4361ee);
        }
        
        .bg-gradient-success {
            background: linear-gradient(45deg, #4cc9f0, #4895ef);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(45deg, #f72585, #b5179e);
        }
        
        .bg-gradient-info {
            background: linear-gradient(45deg, #4895ef, #4cc9f0);
        }
        
        .bg-gradient-danger {
            background: linear-gradient(45deg, #f72585, #ff4d6d);
        }
        
        .bg-gradient-secondary {
            background: linear-gradient(45deg, #6c757d, #495057);
        }
        
        .bg-gradient-dark {
            background: linear-gradient(45deg, #212529, #343a40);
        }
        
        .bg-gradient-light {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        }
        
        .hover-zoom {
            transition: transform 0.3s ease;
        }
        
        .hover-zoom:hover {
            transform: scale(1.2);
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .btn-group .btn {
            margin: 0 2px;
            border-radius: 4px !important;
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .input-group .form-control:focus {
            border-color: #ced4da;
            box-shadow: none;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .input-group:focus-within .form-control {
            border-color: var(--primary-color);
        }
        
        .modal-content {
            border: none;
            border-radius: 15px;
        }
        
        .modal-header {
            background-color: #f8f9fa;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-footer {
            background-color: #f8f9fa;
            border-radius: 0 0 15px 15px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        
        .badge.bg-gradient-success {
            background: linear-gradient(45deg, #4cc9f0, #4895ef);
        }
        
        .badge.bg-gradient-danger {
            background: linear-gradient(45deg, #f72585, #ff4d6d);
        }
        
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 15px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        
        .table tbody td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .pagination {
            margin-bottom: 0;
        }
        
        .pagination .page-link {
            border: none;
            padding: 10px 15px;
            margin: 0 3px;
            border-radius: 5px;
            color: var(--primary-color);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
        }
        
        .pagination .page-item.disabled .page-link {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        
        .card-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-outline-secondary {
            border-color: #e9ecef;
            color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: #ced4da;
            color: #495057;
        }
        
        .cinema-halls-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .cinema-halls-page .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .cinema-halls-page .card:hover {
            transform: translateY(-5px);
        }
        
        .cinema-halls-page .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .cinema-halls-page .table th {
            background: rgba(0, 0, 0, 0.05);
            border: none;
        }
        
        .cinema-halls-page .table td {
            border: none;
            vertical-align: middle;
        }
        
        .cinema-halls-page .table tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }
        
        .cinema-halls-page .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .cinema-halls-page .btn-group .btn {
            border-radius: 8px;
            margin: 0 2px;
            padding: 6px 12px;
            transition: all 0.3s ease;
        }
        
        .cinema-halls-page .btn-group .btn:hover {
            transform: translateY(-2px);
        }
        
        .cinema-halls-page .input-group {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .cinema-halls-page .input-group-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .cinema-halls-page .form-control,
        .cinema-halls-page .form-select {
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
        }
        
        .cinema-halls-page .form-control:focus,
        .cinema-halls-page .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }
        
        .cinema-halls-page .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .cinema-halls-page .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .cinema-halls-page .modal-footer {
            border-top: 1px solid #e0e0e0;
        }
        
        .cinema-halls-page .alert {
            border-radius: 10px;
            border: none;
        }
        
        .cinema-halls-page .loading-spinner {
            width: 2rem;
            height: 2rem;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .cinema-halls-page .hover-zoom {
            transition: transform 0.3s ease;
        }
        
        .cinema-halls-page .hover-zoom:hover {
            transform: scale(1.1);
        }
        
        .showtimes-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .showtimes-page .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .showtimes-page .card:hover {
            transform: translateY(-5px);
        }
        
        .showtimes-page .card-header {
            background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .showtimes-page .table th {
            background: rgba(0, 0, 0, 0.05);
            border: none;
        }
        
        .showtimes-page .table td {
            border: none;
            vertical-align: middle;
        }
        
        .showtimes-page .table tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }
        
        .showtimes-page .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .showtimes-page .badge.bg-success {
            background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%) !important;
        }
        
        .showtimes-page .badge.bg-danger {
            background: linear-gradient(135deg, #f72585 0%, #ff4d6d 100%) !important;
        }
        
        .showtimes-page .badge.bg-info {
            background: linear-gradient(135deg, #4895ef 0%, #4cc9f0 100%) !important;
        }
        
        .showtimes-page .badge.bg-primary {
            background: linear-gradient(135deg, #3a0ca3 0%, #4361ee 100%) !important;
        }
        
        .showtimes-page .btn-group .btn {
            border-radius: 8px;
            margin: 0 2px;
            padding: 6px 12px;
            transition: all 0.3s ease;
        }
        
        .showtimes-page .btn-group .btn:hover {
            transform: translateY(-2px);
        }
        
        .showtimes-page .input-group {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .showtimes-page .input-group-text {
            background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
            color: white;
            border: none;
        }
        
        .showtimes-page .form-control,
        .showtimes-page .form-select {
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
        }
        
        .showtimes-page .form-control:focus,
        .showtimes-page .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(76, 201, 240, 0.25);
            border-color: #4cc9f0;
        }
        
        .showtimes-page .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .showtimes-page .modal-header {
            background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .showtimes-page .modal-footer {
            border-top: 1px solid #e0e0e0;
        }
        
        .showtimes-page .alert {
            border-radius: 10px;
            border: none;
        }
        
        .showtimes-page .loading-spinner {
            width: 2rem;
            height: 2rem;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4cc9f0;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .showtimes-page .form-check-input:checked {
            background-color: #4cc9f0;
            border-color: #4cc9f0;
        }
        
        .showtimes-page .form-switch .form-check-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
        }
        
        .showtimes-page .form-switch .form-check-input:focus {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%234cc9f0'/%3e%3c/svg%3e");
        }
        
        .showtimes-page .hover-zoom {
            transition: transform 0.3s ease;
        }
        
        .showtimes-page .hover-zoom:hover {
            transform: scale(1.1);
        }
        
        .modal-open {
            overflow: hidden;
            padding-right: 0 !important;
        }
        
        .modal {
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-backdrop {
            display: none !important;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        
        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        
        .modal .btn-close:focus {
            box-shadow: none;
        }
        
        .modal.fade .modal-dialog {
            transform: scale(0.8);
            transition: transform 0.3s ease-in-out;
        }
        
        .modal.show .modal-dialog {
            transform: scale(1);
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="preloader">
        <div class="spinner"></div>
    </div>

    <button class="toggle-sidebar" id="toggleSidebar">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar">
                <div class="sidebar-header text-center py-4">
                    <h4 class="mb-0">Sinema Otomasyonu</h4>
                    <p class="text-white-50 mb-0">Admin Paneli</p>
                </div>
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}" href="/admin/dashboard">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="/admin/users">
                                <i class="fas fa-users"></i> Kullanıcılar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/movies*') ? 'active' : '' }}" href="/admin/movies">
                                <i class="fas fa-film"></i> Filmler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/cinemas*') ? 'active' : '' }}" href="/admin/cinemas">
                                <i class="fas fa-building"></i> Sinemalar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/cinema-halls*') ? 'active' : '' }}" href="/admin/cinema-halls">
                                <i class="fas fa-door-open"></i> Sinema Salonları
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/showtimes*') ? 'active' : '' }}" href="/admin/showtimes">
                                <i class="fas fa-clock"></i> Seanslar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/tickets*') ? 'active' : '' }}" href="/admin/tickets">
                                <i class="fas fa-ticket-alt"></i> Biletler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/payments*') ? 'active' : '' }}" href="/admin/payments">
                                <i class="fas fa-credit-card"></i> Ödemeler
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link" href="/logout">
                                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="content-header d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h2 animate__animated animate__fadeInDown">@yield('title')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0 animate__animated animate__fadeInRight">
                        @yield('actions')
                    </div>
                </div>
                
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInUp" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInUp" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="content-wrapper animate__animated animate__fadeIn">
                @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.6.0/dist/countUp.umd.js"></script>
    
    <script>
        $(window).on('load', function() {
            setTimeout(function() {
                $('.preloader').addClass('fade-out');
            }, 500);
        });
        
        $(document).ready(function() {
            $('#toggleSidebar').on('click', function() {
                $('#sidebar').toggleClass('show');
            });
            
            $(document).on('click', function(e) {
                if ($(window).width() < 768) {
                    if (!$(e.target).closest('#sidebar').length && !$(e.target).closest('#toggleSidebar').length) {
                        $('#sidebar').removeClass('show');
                    }
                }
            });
            
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl)
            });
        });
        
        $(document).ready(function() {
            $.fn.modal.Constructor.Default.backdrop = 'static';
            $.fn.modal.Constructor.Default.keyboard = true;
            
            $(document).on('show.bs.modal', '.modal', function() {
                $('body').addClass('modal-open');
            });
            
            $(document).on('hidden.bs.modal', '.modal', function() {
                $(this).find('form').trigger('reset');
                if ($('.modal:visible').length === 0) {
                    $('body').removeClass('modal-open');
                    $('body').css('padding-right', '');
                }
            });
            
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('.modal:visible').length > 0) {
                    $('.modal:visible').modal('hide');
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html> 