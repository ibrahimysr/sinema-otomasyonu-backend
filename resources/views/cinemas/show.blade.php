@extends('layouts.app')

@section('title', $cinema->name . ' - Sinema Otomasyonu')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/cinema.css') }}">
@endsection

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="fw-bold">{{ $cinema->name }}</h1>
            <p class="lead text-muted">{{ $cinema->city->name }}</p>
        </div>
        <div class="col-md-4">
            <div class="d-flex justify-content-end align-items-center h-100">
                <a href="{{ route('cinemas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Tüm Sinemalara Dön
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Sinema Bilgileri</h5>
                </div>
                <div class="card-body">
                    <p>
                        <i class="fas fa-map-marker-alt text-primary me-2"></i> 
                        <strong>Adres:</strong> {{ $cinema->address }}
                    </p>
                    <p>
                        <i class="fas fa-phone text-primary me-2"></i> 
                        <strong>Telefon:</strong> {{ $cinema->phone }}
                    </p>
                    <p>
                        <i class="fas fa-info-circle text-primary me-2"></i> 
                        <strong>Açıklama:</strong> {{ $cinema->description }}
                    </p>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Sinema Salonları</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($cinema->halls as $hall)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $hall->name }}
                            <span class="badge bg-primary rounded-pill">{{ $hall->capacity }} Koltuk</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center py-4">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i> Bu sinemada henüz salon bulunmuyor.
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Yaklaşan Seanslar</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Film</th>
                                    <th>Tarih</th>
                                    <th>Saat</th>
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
                                            <img src="{{ $showtime->movie->poster_url }}" alt="{{ $showtime->movie->title }}" class="me-2" style="width: 40px; height: 60px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold">{{ $showtime->movie->title }}</div>
                                                <small class="text-muted">{{ $showtime->movie->duration }} dk</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $showtime->start_time->format('d.m.Y') }}</td>
                                    <td>{{ $showtime->start_time->format('H:i') }}</td>
                                    <td>{{ $showtime->cinemaHall->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $showtime->available_seats > 10 ? 'success' : ($showtime->available_seats > 5 ? 'warning' : 'danger') }}">
                                            {{ $showtime->available_seats }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($showtime->price, 2) }} ₺</td>
                                    <td>
                                        <a href="{{ route('showtimes.show', $showtime->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-ticket-alt me-1"></i> Bilet Al
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i> Bu sinemada yaklaşan seans bulunmuyor.
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 