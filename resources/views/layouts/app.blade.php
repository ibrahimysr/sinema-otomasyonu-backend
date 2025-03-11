<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sinema Otomasyonu')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    @yield('styles')
</head>
<body>
    <!-- Loader -->
    <div class="loader">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <h4 class="text-white">Sinema Otomasyonu Yükleniyor...</h4>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-film me-2"></i>SİNEMA OTOMASYONU
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/"><i class="fas fa-home me-1"></i> Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/movies"><i class="fas fa-film me-1"></i> Filmler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/cinemas"><i class="fas fa-building me-1"></i> Sinemalar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/showtimes"><i class="fas fa-clock me-1"></i> Seanslar</a>
                    </li>
                </ul>
                <ul class="navbar-nav" id="auth-nav-items">
                    <!-- Bu kısım JavaScript ile güncellenecek -->
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="100">
                    <h5><i class="fas fa-film me-2"></i>Sinema Otomasyonu</h5>
                    <p>Türkiye'nin en kapsamlı sinema bileti satış platformu. En yeni filmler, en iyi sinemalar ve en uygun fiyatlarla bilet satın alma deneyimi.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0" data-aos="fade-up" data-aos-delay="200">
                    <h5>Hızlı Erişim</h5>
                    <ul class="list-unstyled">
                        <li><a href="/"><i class="fas fa-chevron-right me-2"></i>Ana Sayfa</a></li>
                        <li><a href="/movies"><i class="fas fa-chevron-right me-2"></i>Filmler</a></li>
                        <li><a href="/cinemas"><i class="fas fa-chevron-right me-2"></i>Sinemalar</a></li>
                        <li><a href="/showtimes"><i class="fas fa-chevron-right me-2"></i>Seanslar</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Hakkımızda</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right me-2"></i>İletişim</a></li>
                    </ul>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <h5>İletişim</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@sinemaotomasyonu.com</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +90 212 123 45 67</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> İstanbul, Türkiye</li>
                    </ul>
                    <div class="mt-4">
                        <h6>Bültenimize Abone Olun</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="E-posta adresiniz">
                            <button class="btn btn-primary" type="button">Abone Ol</button>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2023 Sinema Otomasyonu. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // AOS animasyon ayarları
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        $(document).ready(function() {
            checkAuthStatus();
            
            setTimeout(function() {
                $('.loader').addClass('fade-out');
            }, 500);
            
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });
        });
        
        function checkAuthStatus() {
            const token = localStorage.getItem('auth_token');
            const userName = localStorage.getItem('user_name');
            
            if (token && userName) {
                updateNavbarForLoggedInUser(userName);
            } else {
                updateNavbarForLoggedOutUser();
            }
        }
        
        function updateNavbarForLoggedInUser(userName) {
            const authNavItems = $('#auth-nav-items');
            
            authNavItems.empty();
            
            authNavItems.html(`
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-1"></i> ${userName}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/profile"><i class="fas fa-user-circle me-1"></i> Profilim</a></li>
                        <li><a class="dropdown-item" href="/my-tickets"><i class="fas fa-ticket-alt me-1"></i> Biletlerim</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="logout(); return false;">
                                <i class="fas fa-sign-out-alt me-1"></i> Çıkış Yap
                            </a>
                        </li>
                    </ul>
                </li>
            `);
        }
        
        function updateNavbarForLoggedOutUser() {
            const authNavItems = $('#auth-nav-items');
            
            // Navbar içeriğini temizle
            authNavItems.empty();
            
            authNavItems.html(`
                <li class="nav-item">
                    <a class="nav-link" href="/login-user"><i class="fas fa-sign-in-alt me-1"></i> Giriş Yap</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/register-user"><i class="fas fa-user-plus me-1"></i> Kayıt Ol</a>
                </li>
            `);
        }
        
        function logout() {
            const token = localStorage.getItem('auth_token');
            
            if (token) {
                $.ajax({
                    url: '/api/auth/logout',
                    type: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    },
                    success: function() {
                        clearUserData();
                        window.location.href = '/';
                    },
                    error: function() {
                        clearUserData();
                        window.location.href = '/';
                    }
                });
            } else {
                clearUserData();
                window.location.href = '/';
            }
        }
        
        function clearUserData() {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_id');
            localStorage.removeItem('user_name');
            localStorage.removeItem('user_email');
            
            updateNavbarForLoggedOutUser();
        }
    </script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>