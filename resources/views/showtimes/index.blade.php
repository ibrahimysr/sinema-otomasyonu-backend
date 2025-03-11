@extends('layouts.app')

@section('title', 'Tüm Seanslar - Sinema Otomasyonu')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/showtime.css') }}">
@endsection

@section('content')
    <!-- Header -->
    <section class="showtime-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h1 class="display-4 fw-bold text-white mb-2">Tüm Seanslar</h1>
                    <p class="lead text-light mb-0">Bugün ve önümüzdeki 7 gün için mevcut seanslar</p>
                </div>
                <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                    <a href="{{ route('home') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Ana Sayfaya Dön
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Seanslar -->
    <section class="py-4">
        <div class="container">
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Film</th>
                                <th>Tarih</th>
                                <th>Saat</th>
                                <th>Sinema</th>
                                <th>Salon</th>
                                <th>Boş Koltuk</th>
                                <th>Fiyat</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($showtimes as $showtime)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $showtime->movie->poster_url }}" alt="{{ $showtime->movie->title }}" class="movie-poster">
                                        <div class="movie-info">
                                            <div class="movie-title">{{ $showtime->movie->title }}</div>
                                            <div class="movie-duration">{{ $showtime->movie->duration }} dk</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $showtime->start_time->format('d.m.Y') }}</td>
                                <td>{{ $showtime->start_time->format('H:i') }}</td>
                                <td>{{ $showtime->cinemaHall->cinema->name }}</td>
                                <td>{{ $showtime->cinemaHall->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $showtime->available_seats > 10 ? 'success' : ($showtime->available_seats > 5 ? 'warning' : 'danger') }}">
                                        {{ $showtime->available_seats }}
                                    </span>
                                </td>
                                <td>{{ number_format($showtime->price, 2) }} ₺</td>
                                <td>
                                    <a href="{{ route('showtimes.show', $showtime->id) }}" class="btn-ticket">
                                        <i class="fas fa-ticket-alt me-1"></i> Bilet Al
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle me-2"></i> Gösterilecek seans bulunamadı.
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $showtimes->links() }}
            </div>
        </div>
    </section>
@endsection 