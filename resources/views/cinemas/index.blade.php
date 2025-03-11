@extends('layouts.app')

@section('title', 'Tüm Sinemalar - Sinema Otomasyonu')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/cinema.css') }}">
@endsection

@section('content')
    <!-- Header -->
    <section class="cinema-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h1 class="display-4 fw-bold text-white mb-2">Tüm Sinemalar</h1>
                    <p class="lead text-light mb-0">Şehrinizde ve çevresindeki en iyi sinema salonları</p>
                </div>
                <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                    <a href="{{ route('home') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                @forelse($cinemas as $cinema)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                    <div class="cinema-card">
                        <div class="card-body p-4">
                            <div class="cinema-header-content">
                                <div class="cinema-icon">
                                    <i class="fas fa-film"></i>
                                </div>
                                <h3 class="cinema-title">{{ $cinema->name }}</h3>
                            </div>
                            
                            <div class="cinema-info">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $cinema->address }}</span>
                            </div>
                            <div class="cinema-info">
                                <i class="fas fa-phone"></i>
                                <span>{{ $cinema->phone }}</span>
                            </div>
                            
                            <p class="cinema-description">
                                {{ Str::limit($cinema->description, 100) }}
                            </p>
                            
                            <div class="cinema-features">
                                <span class="badge bg-success">4K</span>
                                <span class="badge bg-info">Dolby</span>
                                <span class="badge bg-warning">IMAX</span>
                            </div>
                            
                            <a href="{{ route('cinemas.show', $cinema->id) }}" class="cinema-button">
                                <i class="fas fa-door-open"></i> Salonları Gör
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        Gösterilecek sinema bulunamadı.
                    </div>
                </div>
                @endforelse
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $cinemas->links('pagination.custom') }}
            </div>
        </div>
    </section>
@endsection 