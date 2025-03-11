@extends('layouts.app')

@section('title', $movie->title . ' - Sinema Otomasyonu')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/movie.css') }}">
@endsection

@section('content')
    <div class="movie-backdrop" style="background-image: url('https://image.tmdb.org/t/p/w1280{{ $movie->backdrop_url ?? 'default_backdrop.jpg' }}')">
        <div class="container py-5">
            <div class="row movie-details align-items-center">
                <div class="col-md-4 text-center text-md-start" data-aos="fade-right">
                    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" class="img-fluid movie-poster">
                </div>
                <div class="col-md-8" data-aos="fade-left">
                    <h1 class="movie-title">{{ $movie->title }}</h1>
                    <div class="d-flex align-items-center flex-wrap mb-4">
                        <span class="movie-rating me-3">
                            <i class="fas fa-star"></i> {{ $movie->imdb_rating }}
                        </span>
                        <span class="movie-meta me-3">
                            <i class="fas fa-clock text-primary me-1"></i> {{ $movie->duration }} dakika
                        </span>
                        <span class="movie-meta">
                            <i class="fas fa-calendar-alt text-primary me-1"></i> {{ $movie->release_date->format('d.m.Y') }}
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        @foreach(explode(', ', $movie->genre) as $genre)
                            <span class="badge-genre">{{ $genre }}</span>
                        @endforeach
                    </div>
                    
                    <p class="movie-description">{{ $movie->description }}</p>
                    
                    <div class="mt-4">
                        <div class="movie-info-item">
                            <i class="fas fa-language"></i>
                            <span><strong>Dil:</strong> {{ $movie->language }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container py-5">
        <h2 class="mb-4 fw-bold text-white" data-aos="fade-up"><i class="fas fa-ticket-alt text-primary me-2"></i> Bilet Al</h2>
        
        <div class="row mb-4 g-4">
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4"><i class="fas fa-map-marker-alt text-primary me-2"></i> Şehir ve Sinema Seçin</h5>
                        
                        <div class="mb-4">
                            <label for="citySelect" class="form-label">Şehir</label>
                            <select id="citySelect" class="form-select city-select">
                                <option value="">Şehir Seçin</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cinemaSelect" class="form-label">Sinema</label>
                            <select id="cinemaSelect" class="form-select cinema-select" disabled>
                                <option value="">Önce Şehir Seçin</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4"><i class="fas fa-calendar-day text-primary me-2"></i> Tarih Seçin</h5>
                        
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $today = \Carbon\Carbon::now();
                            @endphp
                            
                            @for($i = 0; $i < 7; $i++)
                                @php
                                    $date = $today->copy()->addDays($i);
                                    $dateStr = $date->format('Y-m-d');
                                    $isToday = $i === 0;
                                @endphp
                                <button type="button" class="btn {{ $isToday ? 'btn-primary' : 'btn-outline-primary' }} date-btn" data-date="{{ $dateStr }}">
                                    <div class="d-flex flex-column align-items-center">
                                        <small>{{ $date->locale('tr')->format('D') }}</small>
                                        <strong>{{ $date->format('d') }}</strong>
                                        <small>{{ $date->locale('tr')->format('M') }}</small>
                                    </div>
                                </button>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="showtimesContainer" class="mt-4" data-aos="fade-up" data-aos-delay="300">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Lütfen şehir ve sinema seçin.
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const citySelect = document.getElementById('citySelect');
        const cinemaSelect = document.getElementById('cinemaSelect');
        const dateButtons = document.querySelectorAll('.date-btn');
        const showtimesContainer = document.getElementById('showtimesContainer');
        
        let selectedDate = '{{ \Carbon\Carbon::now()->format('Y-m-d') }}';
        let selectedCityId = '';
        let selectedCinemaId = '';
        
        dateButtons.forEach(button => {
            button.addEventListener('click', function() {
                dateButtons.forEach(btn => btn.classList.remove('btn-primary'));
                dateButtons.forEach(btn => btn.classList.add('btn-outline-primary'));
                
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                
                selectedDate = this.dataset.date;
                
                if (selectedCinemaId) {
                    fetchShowtimes();
                }
            });
        });
        
        citySelect.addEventListener('change', function() {
            selectedCityId = this.value;
            
            if (selectedCityId) {
                cinemaSelect.disabled = false;
                
                fetchCinemas(selectedCityId);
            } else {
                cinemaSelect.disabled = true;
                cinemaSelect.innerHTML = '<option value="">Önce Şehir Seçin</option>';
                
                showtimesContainer.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Lütfen şehir ve sinema seçin.
                    </div>
                `;
            }
        });
        
        cinemaSelect.addEventListener('change', function() {
            selectedCinemaId = this.value;
            
            if (selectedCinemaId) {
                fetchShowtimes();
            } else {
                showtimesContainer.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Lütfen sinema seçin.
                    </div>
                `;
            }
        });
        
        function fetchCinemas(cityId) {
            fetch(`/api/cities/${cityId}/cinemas`)
                .then(response => response.json())
                .then(data => {
                    cinemaSelect.innerHTML = '<option value="">Sinema Seçin</option>';
                    
                    data.forEach(cinema => {
                        const option = document.createElement('option');
                        option.value = cinema.id;
                        option.textContent = cinema.name;
                        cinemaSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Sinemalar getirilirken hata oluştu:', error);
                    
                    cinemaSelect.innerHTML = '<option value="">Sinemalar yüklenemedi</option>';
                });
        }
        
        function fetchShowtimes() {
            showtimesContainer.innerHTML = `
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
            `;
            
            fetch(`/api/showtimes?movie_id={{ $movie->id }}&cinema_id=${selectedCinemaId}&date=${selectedDate}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        showtimesContainer.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> Seçilen tarih ve sinemada gösterim bulunamadı.
                            </div>
                        `;
                        return;
                    }
                    
                    let html = `
                        <h4 class="mb-4 text-white">Seanslar</h4>
                        <div class="row g-4">
                    `;
                    
                    data.forEach(showtime => {
                        const startTime = new Date(showtime.start_time).toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
                        const endTime = new Date(showtime.end_time).toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' });
                        
                        html += `
                            <div class="col-md-4">
                                <div class="card showtime-card h-100">
                                    <div class="card-body p-4">
                                        <h5 class="card-title text-white">${startTime} - ${endTime}</h5>
                                        <p class="card-text text-muted mb-2">Salon: ${showtime.cinema_hall.name}</p>
                                        <p class="card-text text-muted mb-3">Boş Koltuk: ${showtime.available_seats}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-primary">${Number(showtime.price).toFixed(2)} ₺</span>
                                            <a href="/showtimes/${showtime.id}" class="btn btn-primary">
                                                <i class="fas fa-ticket-alt me-2"></i> Bilet Al
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `</div>`;
                    
                    showtimesContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Seanslar getirilirken hata oluştu:', error);
                    
                    showtimesContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> Seanslar yüklenirken bir hata oluştu. Lütfen tekrar deneyin.
                        </div>
                    `;
                });
        }
    });
</script>
@endsection