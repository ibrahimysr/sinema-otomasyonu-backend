@extends('layouts.app')

@section('title', 'Filmler - Sinema Otomasyonu')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/movie.css') }}">
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h1 class="hero-title" data-aos="fade-right">Vizyondaki Filmler</h1>
                    <p class="hero-text" data-aos="fade-right" data-aos-delay="100">En yeni ve popüler filmleri keşfedin, biletinizi hemen alın.</p>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                    <form action="{{ route('movies.index') }}" method="GET" class="search-form">
                        <input type="text" name="search" class="form-control search-input" placeholder="Film ara..." value="{{ request('search') }}">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Filmler -->
    <section class="py-5">
        <div class="container">
            @if($movies->isEmpty())
                <div class="alert" data-aos="fade-up">
                    <i class="fas fa-info-circle me-2"></i> Aradığınız kriterlere uygun film bulunamadı.
                </div>
            @else
                <div class="row g-4">
                    @foreach($movies as $movie)
                    <div class="col-lg-3 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 50 }}">
                        <div class="card movie-card h-100">
                            <img src="{{ $movie->poster_url }}" class="movie-poster" alt="{{ $movie->title }}">
                            <div class="card-body">
                                <h5 class="movie-title text-truncate">{{ $movie->title }}</h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="movie-rating">
                                        <i class="fas fa-star"></i> {{ $movie->imdb_rating }}
                                    </span>
                                    <span class="movie-duration">{{ $movie->duration }} dk</span>
                                </div>
                                <p class="movie-genre">{{ Str::limit($movie->genre, 30) }}</p>
                            </div>
                            <div class="card-footer border-0 bg-transparent p-3">
                                <a href="{{ route('movies.show', $movie->id) }}" class="btn movie-button w-100">
                                    <i class="fas fa-info-circle me-2"></i>Detaylar
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center">
                    {{ $movies->links('pagination.custom') }}
                </div>
            @endif
        </div>
    </section>
@endsection