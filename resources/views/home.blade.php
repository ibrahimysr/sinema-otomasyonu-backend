@extends('layouts.app')

@section('title', 'Ana Sayfa - Sinema Otomasyonu')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section position-relative" style="min-height: 100vh; padding-top: 120px; background: linear-gradient(135deg, rgba(0,0,0,0.95) 0%, rgba(10,10,10,0.9) 100%), url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat fixed;">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-delay="200">
                    <h1 class="display-3 fw-bold text-white mb-3">Sinema Keyfiniz <span class="text-primary">Burada Başlar</span></h1>
                    <p class="lead mb-4 text-white">En yeni filmler, en iyi sinemalar ve en uygun fiyatlarla bilet satın alma deneyimi. Sinema tutkunları için özel tasarlanmış platform.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="{{ route('movies.index') }}" class="btn btn-primary btn-lg px-4 py-3 rounded-pill">
                            <i class="fas fa-film me-2"></i> Filmleri Keşfet
                        </a>
                        <a href="/showtimes" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill">
                            <i class="fas fa-ticket-alt me-2"></i> Bilet Al
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block" data-aos="fade-left" data-aos-delay="400">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1025&q=80" alt="Sinema" class="img-fluid rounded-3 shadow-lg" style="transform: perspective(1000px) rotateY(-15deg);">
                        <div class="position-absolute" style="top: -30px; right: -30px; z-index: -1;">
                            <img src="https://images.unsplash.com/photo-1542204165-65bf26472b9b?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=774&q=80" alt="Popcorn" class="img-fluid rounded-3 shadow-lg" style="width: 200px; transform: perspective(1000px) rotateY(15deg);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="position-absolute bottom-0 start-0 w-100 overflow-hidden" style="height: 70px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none" style="height: 100%; width: 100%;">
                <path fill="#0a0a0a" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,213.3C1248,235,1344,213,1392,202.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
    </section>

    <!-- Vizyondaki Filmler -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" data-aos="fade-right"><i class="fas fa-film text-primary me-2"></i>Vizyondaki Filmler</h2>
                <a href="{{ route('movies.index') }}" class="btn btn-outline-primary" data-aos="fade-left">
                    Tümünü Gör <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            
            <div class="swiper movie-swiper">
                <div class="swiper-wrapper">
                    @foreach($movies as $movie)
                    <div class="swiper-slide">
                        <div class="movie-card h-100" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="position-relative overflow-hidden">
                                <img src="{{ $movie->poster_url }}" class="card-img-top movie-poster" alt="{{ $movie->title }}">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-primary">Yeni</span>
                                </div>
                                <div class="movie-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.7); opacity: 0; transition: all 0.3s ease;">
                                    <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-light rounded-circle p-3">
                                        <i class="fas fa-play"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title text-truncate">{{ $movie->title }}</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-star"></i> {{ $movie->imdb_rating }}
                                    </span>
                                    <small class="text-light">{{ $movie->duration }} dk</small>
                                </div>
                                <p class="card-text small text-muted mt-2">{{ Str::limit($movie->genre, 30) }}</p>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-primary w-100">Detaylar</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Popüler Sinemalar -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--secondary) 0%, var(--dark) 100%);">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-white" data-aos="fade-right"><i class="fas fa-building text-primary me-2"></i>Popüler Sinemalar</h2>
                <a href="{{ route('cinemas.index') }}" class="btn btn-outline-primary" data-aos="fade-left">Tümünü Gör <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="row g-4">
                @foreach($cinemas as $cinema)
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card h-100 border-0 shadow-lg cinema-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="cinema-icon me-3 bg-primary text-white rounded-circle p-3">
                                    <i class="fas fa-film fa-2x"></i>
                                </div>
                                <h5 class="card-title mb-0">{{ $cinema->name }}</h5>
                            </div>
                            <p class="card-text text-muted mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i> {{ $cinema->address }}
                            </p>
                            <p class="card-text mb-2 text-light">
                                <i class="fas fa-phone text-primary me-2"></i> {{ $cinema->phone }}
                            </p>
                            <p class="card-text small mb-3 text-muted">
                                <i class="fas fa-info-circle text-primary me-2"></i> {{ Str::limit($cinema->description, 100) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <span class="badge bg-success me-1">4K</span>
                                    <span class="badge bg-info me-1">Dolby</span>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer p-3">
                            <a href="{{ route('cinemas.show', $cinema->id) }}" class="btn btn-primary w-100">Salonları Gör</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Yaklaşan Seanslar -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold" data-aos="fade-right"><i class="fas fa-clock text-primary me-2"></i>Yaklaşan Seanslar</h2>
                <a href="{{ route('showtimes.index') }}" class="btn btn-outline-primary" data-aos="fade-left">Tümünü Gör <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" data-aos="fade-up">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Film</th>
                                <th>Sinema</th>
                                <th>Salon</th>
                                <th>Tarih & Saat</th>
                                <th>Fiyat</th>
                                <th>Boş Koltuk</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($showtimes as $showtime)
                            <tr class="showtime-row" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $showtime->movie->poster_url }}" alt="{{ $showtime->movie->title }}" class="me-2 rounded" style="width: 40px; height: 60px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-white">{{ $showtime->movie->title }}</div>
                                            <small class="text-muted">{{ $showtime->movie->duration }} dk</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-light">{{ $showtime->cinemaHall->cinema->name }}</td>
                                <td class="text-light">{{ $showtime->cinemaHall->name }}</td>
                                <td>
                                    <div class="fw-bold text-white">{{ $showtime->start_time->format('d.m.Y') }}</div>
                                    <small class="text-muted">{{ $showtime->start_time->format('H:i') }} - {{ $showtime->end_time->format('H:i') }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">{{ number_format($showtime->price, 2) }} ₺</span>
                                </td>
                                <td>
                                    <span class="badge {{ $showtime->available_seats > 10 ? 'bg-success' : ($showtime->available_seats > 0 ? 'bg-warning' : 'bg-danger') }} px-3 py-2">
                                        {{ $showtime->available_seats }} koltuk
                                    </span>
                                </td>
                                <td>
                                    <a href="/showtimes/{{ $showtime->id }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-ticket-alt me-1"></i> Bilet Al
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Avantajlar -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--dark) 0%, var(--secondary) 100%);">
        <div class="container">
            <h2 class="text-center fw-bold mb-5 text-white" data-aos="fade-up">Neden Bizi Tercih Etmelisiniz?</h2>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 border-0 shadow-lg feature-card">
                        <div class="card-body text-center p-4">
                            <div class="mb-4 feature-icon">
                                <i class="fas fa-ticket-alt fa-3x text-primary"></i>
                            </div>
                            <h4 class="text-white">Kolay Bilet Satın Alma</h4>
                            <p class="text-muted">Birkaç tıklama ile istediğiniz filme bilet alın, sıra beklemeyin.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 border-0 shadow-lg feature-card">
                        <div class="card-body text-center p-4">
                            <div class="mb-4 feature-icon">
                                <i class="fas fa-percent fa-3x text-primary"></i>
                            </div>
                            <h4 class="text-white">Özel Kampanyalar</h4>
                            <p class="text-muted">Size özel indirimler ve kampanyalarla daha uygun fiyatlara bilet alın.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100 border-0 shadow-lg feature-card">
                        <div class="card-body text-center p-4">
                            <div class="mb-4 feature-icon">
                                <i class="fas fa-film fa-3x text-primary"></i>
                            </div>
                            <h4 class="text-white">Geniş Film Seçeneği</h4>
                            <p class="text-muted">En yeni filmler ve özel gösterimlerle sizlere geniş bir seçenek sunuyoruz.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sayfa yükleme animasyonu
        setTimeout(function() {
            document.body.classList.add('loaded');
        }, 500);
        
        // Movie Swiper
        new Swiper('.movie-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 4,
                },
            }
        });

        // Movie card hover effect
        const movieCards = document.querySelectorAll('.movie-card');
        movieCards.forEach(card => {
            const overlay = card.querySelector('.movie-overlay');
            if (overlay) {
                card.addEventListener('mouseenter', () => {
                    overlay.style.opacity = '1';
                });
                card.addEventListener('mouseleave', () => {
                    overlay.style.opacity = '0';
                });
            }
        });

        // Feature card hover effect
        const featureCards = document.querySelectorAll('.feature-card');
        featureCards.forEach(card => {
            const icon = card.querySelector('.feature-icon');
            card.addEventListener('mouseenter', () => {
                icon.classList.add('pulse');
            });
            card.addEventListener('mouseleave', () => {
                icon.classList.remove('pulse');
            });
        });

        // Showtime row hover effect
        const showtimeRows = document.querySelectorAll('.showtime-row');
        showtimeRows.forEach(row => {
            row.addEventListener('mouseenter', () => {
                row.style.backgroundColor = 'rgba(255, 255, 255, 0.05)';
                row.style.transform = 'translateY(-2px)';
                row.style.transition = 'all 0.3s ease';
                row.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
            });
            row.addEventListener('mouseleave', () => {
                row.style.backgroundColor = '';
                row.style.transform = '';
                row.style.boxShadow = '';
            });
        });
        
        // Form input focus effect
        const formInputs = document.querySelectorAll('.form-control, .form-select');
        formInputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.style.transform = 'translateY(-2px)';
                input.parentElement.style.transition = 'all 0.3s ease';
            });
            input.addEventListener('blur', () => {
                input.parentElement.style.transform = '';
            });
        });
        
        // Animasyonlu sayaç
        function animateCounter(el, start, end, duration) {
            let startTime = null;
            
            function animation(currentTime) {
                if (!startTime) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);
                const value = Math.floor(progress * (end - start) + start);
                
                el.textContent = value;
                
                if (progress < 1) {
                    requestAnimationFrame(animation);
                }
            }
            
            requestAnimationFrame(animation);
        }
        
        // Görünüm alanına giren öğelere animasyon ekle
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        document.querySelectorAll('.card, .btn-primary, .btn-outline-primary').forEach(el => {
            observer.observe(el);
        });
    });
</script>
@endsection
@section('styles')
<style>
    .movie-overlay {
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .movie-card:hover .movie-overlay {
        opacity: 1;
    }
    
    .cinema-icon {
        transition: all 0.3s ease;
    }
    
    .cinema-card:hover .cinema-icon {
        transform: scale(1.1) rotate(10deg);
    }
    
    .feature-icon {
        transition: all 0.3s ease;
    }
    
    .feature-card:hover .feature-icon {
        transform: scale(1.1);
    }
    
    .showtime-row {
        transition: all 0.3s ease;
    }
    
    .hero-section {
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at top right, rgba(229, 9, 20, 0.3), transparent 50%);
        z-index: 1;
    }
    
    .hero-section::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at bottom left, rgba(229, 9, 20, 0.2), transparent 50%);
        z-index: 1;
    }
    
    .table {
        --bs-table-hover-color: var(--light);
        --bs-table-hover-bg: rgba(255, 255, 255, 0.05);
    }
    
    .table tbody tr {
        border-bottom: 1px solid var(--border-color);
    }
    
    .table thead th {
        border-bottom: 2px solid var(--border-color);
        padding: 1rem 0.75rem;
        font-weight: 600;
        color: var(--light);
    }
    
    .badge {
        letter-spacing: 0.5px;
    }
    
    .btn {
        letter-spacing: 0.5px;
        font-weight: 500;
    }
    
    .card {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .rounded-4 {
        border-radius: 1rem !important;
    }
    
    .rounded-pill {
        border-radius: 50rem !important;
    }
    
    .text-muted {
        color: var(--text-muted) !important;
    }
    
    .text-light {
        color: var(--light) !important;
    }
    
    .text-white {
        color: white !important;
    }
    
    .swiper-button-next, .swiper-button-prev {
        background-color: rgba(0, 0, 0, 0.5);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: white;
    }
    
    .swiper-button-next:after, .swiper-button-prev:after {
        font-size: 1.2rem;
    }
    
    .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
    }
    
    .movie-card .card-footer, .cinema-card .card-footer {
        border-top: 1px solid var(--border-color);
    }
    
    .form-control::placeholder {
        color: var(--text-muted);
        opacity: 0.7;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(229, 9, 20, 0.25);
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 30px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    
    .fadeInUp {
        animation: fadeInUp 0.5s ease-out;
    }
</style>
@endsection

