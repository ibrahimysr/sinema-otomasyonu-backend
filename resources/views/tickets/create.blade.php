@extends('layouts.app')

@section('title', 'Bilet Satın Al - Sinema Otomasyonu')

@section('styles')
<style>
    .movie-info {
        background-color: #032541;
        color: white;
        padding: 20px 0;
    }
    .movie-poster {
        max-height: 200px;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    }
    .selected-seat-tag {
        display: inline-block;
        background-color: #90cea1;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        margin: 5px;
        font-size: 14px;
    }
    .payment-method {
        border: 2px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .payment-method:hover {
        border-color: #01b4e4;
    }
    .payment-method.selected {
        border-color: #01b4e4;
        background-color: rgba(1, 180, 228, 0.1);
    }
    .payment-method-radio {
        display: none;
    }
</style>
@endsection

@section('content')
    <section class="movie-info">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="{{ $showtime->movie->poster_url }}" alt="{{ $showtime->movie->title }}" class="img-fluid movie-poster">
                </div>
                <div class="col-md-7">
                    <h1 class="h3 fw-bold">{{ $showtime->movie->title }}</h1>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-warning text-dark me-2">
                            <i class="fas fa-star"></i> {{ $showtime->movie->imdb_rating }}
                        </span>
                        <span class="text-white">{{ $showtime->movie->duration }} dakika</span>
                    </div>
                    <p class="mb-1">
                        <i class="fas fa-building me-2"></i> {{ $showtime->cinemaHall->cinema->name }}
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-door-open me-2"></i> {{ $showtime->cinemaHall->name }} ({{ $showtime->cinemaHall->type }})
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i> {{ $showtime->start_time->format('d.m.Y') }}
                        <i class="fas fa-clock ms-3 me-2"></i> {{ $showtime->start_time->format('H:i') }} - {{ $showtime->end_time->format('H:i') }}
                    </p>
                </div>
                <div class="col-md-3 text-end">
                    <div class="bg-light text-dark p-3 rounded">
                        <h5 class="fw-bold">Toplam Tutar</h5>
                        <p class="h3 text-primary mb-0">{{ number_format($totalPrice, 2) }} ₺</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="h4 fw-bold mb-4">Kişisel Bilgiler</h2>
                            
                            <form id="ticketForm" action="{{ route('tickets.store') }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Ad Soyad</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-posta</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Bilet bilgileriniz bu e-posta adresine gönderilecektir.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefon</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <h2 class="h4 fw-bold mb-4 mt-5">Ödeme Yöntemi</h2>
                                
                                <div class="mb-4">
                                    <div class="payment-method @if(old('payment_method') == 'credit_card') selected @endif" onclick="selectPaymentMethod('credit_card')">
                                        <input type="radio" name="payment_method" id="credit_card" value="credit_card" class="payment-method-radio" @if(old('payment_method') == 'credit_card') checked @endif>
                                        <label for="credit_card" class="d-flex align-items-center">
                                            <i class="fas fa-credit-card fa-2x text-primary me-3"></i>
                                            <div>
                                                <h5 class="mb-0">Kredi Kartı</h5>
                                                <p class="text-muted mb-0">Güvenli ödeme işlemi</p>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    <div class="payment-method @if(old('payment_method') == 'cash') selected @endif" onclick="selectPaymentMethod('cash')">
                                        <input type="radio" name="payment_method" id="cash" value="cash" class="payment-method-radio" @if(old('payment_method') == 'cash') checked @endif>
                                        <label for="cash" class="d-flex align-items-center">
                                            <i class="fas fa-money-bill-wave fa-2x text-success me-3"></i>
                                            <div>
                                                <h5 class="mb-0">Nakit</h5>
                                                <p class="text-muted mb-0">Gişeden biletinizi alırken ödeme yapın</p>
                                            </div>
                                        </label>
                                    </div>
                                    
                                    @error('payment_method')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-ticket-alt me-2"></i> Bileti Onayla ve Satın Al
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-body">
                            <h3 class="h5 fw-bold mb-4">Bilet Özeti</h3>
                            
                            <div class="mb-3">
                                <p class="mb-1"><strong>Film:</strong> {{ $showtime->movie->title }}</p>
                                <p class="mb-1"><strong>Sinema:</strong> {{ $showtime->cinemaHall->cinema->name }}</p>
                                <p class="mb-1"><strong>Salon:</strong> {{ $showtime->cinemaHall->name }}</p>
                                <p class="mb-1"><strong>Tarih:</strong> {{ $showtime->start_time->format('d.m.Y') }}</p>
                                <p class="mb-1"><strong>Saat:</strong> {{ $showtime->start_time->format('H:i') }}</p>
                            </div>
                            
                            <hr>
                            
                            <div class="mb-3">
                                <h5 class="fw-bold">Seçilen Koltuklar</h5>
                                <div>
                                    @php
                                        // Koltukları sırala
                                        $sortedSeats = $selectedSeats;
                                        usort($sortedSeats, function($a, $b) {
                                            if ($a[0] !== $b[0]) {
                                                return $a[0] <=> $b[0];
                                            }
                                            return intval(substr($a, 1)) <=> intval(substr($b, 1));
                                        });
                                    @endphp
                                    
                                    @foreach($sortedSeats as $seat)
                                        <span class="selected-seat-tag">{{ $seat }}</span>
                                    @endforeach
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Bilet Fiyatı:</span>
                                <span>{{ number_format($ticketPrice, 2) }} ₺</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Seçilen Koltuk:</span>
                                <span>{{ count($selectedSeats) }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <span class="fw-bold">Toplam:</span>
                                <span class="fw-bold text-primary h5 mb-0">{{ number_format($totalPrice, 2) }} ₺</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    function selectPaymentMethod(method) {
        document.querySelectorAll('.payment-method').forEach(element => {
            element.classList.remove('selected');
        });
        
        document.querySelector(`.payment-method:has(#${method})`).classList.add('selected');
        
        document.getElementById(method).checked = true;
    }
</script>
@endsection 