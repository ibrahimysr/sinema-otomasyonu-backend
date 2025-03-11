@extends('layouts.app')

@section('title', $showtime->movie->title . ' - Koltuk Seçimi')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/showtime.css') }}">
<style>
    .seat-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        justify-content: center;
        margin: 2rem 0;
    }
    
    .seat-row {
        display: flex;
        flex-direction: row;
        gap: 10px;
        align-items: center;
        justify-content: center;
    }
    
    .seat-label {
        color: var(--text-muted);
        font-weight: 500;
        width: 30px;
        text-align: center;
    }
    
    .seat {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        border: none;
    }
    
    .seat.available {
        background-color: var(--primary);
        color: white;
    }
    
    .seat.available:hover {
        transform: scale(1.1);
        box-shadow: 0 0 15px rgba(229, 9, 20, 0.3);
    }
    
    .seat.selected {
        background-color: var(--success);
        color: white;
    }
    
    .seat.reserved {
        background-color: var(--text-muted);
        color: white;
        cursor: not-allowed;
    }

    .seat.sold {
        background-color: #01579b;
        color: white;
        cursor: not-allowed;
    }
    
    .screen {
        width: 100%;
        height: 8px;
        background: linear-gradient(to right, var(--primary), var(--primary-hover));
        border-radius: 4px;
        margin: 2rem 0;
        position: relative;
    }
    
    .screen::before {
        content: 'PERDE';
        position: absolute;
        top: -25px;
        left: 50%;
        transform: translateX(-50%);
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .seat-info {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        margin: 2rem 0;
        background-color: var(--darker);
        padding: 1rem;
        border-radius: 8px;
    }

    .seat-info-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .seat-info-box {
        width: 30px;
        height: 30px;
        border-radius: 6px;
    }

    .seat-info-box.available {
        background-color: var(--primary);
    }

    .seat-info-box.selected {
        background-color: var(--success);
    }

    .seat-info-box.reserved {
        background-color: var(--text-muted);
    }

    .seat-info-box.sold {
        background-color: #01579b;
    }

    .seat-info-item span {
        color: var(--light);
        font-size: 0.9rem;
    }

    .continue-btn {
        background-color: #2ecc71 !important;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(46, 204, 113, 0.2);
    }

    .continue-btn:hover {
        background-color: #27ae60 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(46, 204, 113, 0.3);
    }

    .continue-btn:disabled {
        background-color: #95a5a6 !important;
        transform: none;
        cursor: not-allowed;
        box-shadow: none;
    }
</style>
@endsection

@section('content')
    <!-- Film ve Seans Bilgileri -->
    <section class="ticket-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-2 text-center" data-aos="fade-right">
                    <img src="{{ $showtime->movie->poster_url }}" alt="{{ $showtime->movie->title }}" class="img-fluid movie-poster">
                </div>
                <div class="col-md-7" data-aos="fade-up">
                    <h1 class="h3 fw-bold mb-3">{{ $showtime->movie->title }}</h1>
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-warning text-dark me-3">
                            <i class="fas fa-star"></i> {{ $showtime->movie->imdb_rating }}
                        </span>
                        <span>{{ $showtime->movie->duration }} dakika</span>
                    </div>
                    
                    <div class="movie-info-item">
                        <i class="fas fa-building"></i>
                        <span>{{ $showtime->cinemaHall->cinema->name }}</span>
                    </div>
                    <div class="movie-info-item">
                        <i class="fas fa-door-open"></i>
                        <span>{{ $showtime->cinemaHall->name }} ({{ $showtime->cinemaHall->type }})</span>
                    </div>
                    <div class="movie-info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $showtime->start_time->format('d.m.Y') }}</span>
                    </div>
                    <div class="movie-info-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ $showtime->start_time->format('H:i') }} - {{ $showtime->end_time->format('H:i') }}</span>
                    </div>
                </div>
                <div class="col-md-3 text-end" data-aos="fade-left">
                    <div class="price-badge">
                        <h5 class="fw-bold mb-2">Bilet Fiyatı</h5>
                        <p class="price-value mb-0">{{ number_format($showtime->price, 2) }} ₺</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Koltuk Seçimi -->
    <section class="py-5">
        <div class="container">
            <h2 class="mb-4 text-center fw-bold text-white" data-aos="fade-up">Koltuk Seçimi</h2>
            
            <div class="seat-info" data-aos="fade-up">
                <div class="seat-info-item">
                    <div class="seat-info-box available"></div>
                    <span>Boş</span>
                </div>
                <div class="seat-info-item">
                    <div class="seat-info-box selected"></div>
                    <span>Seçili</span>
                </div>
                <div class="seat-info-item">
                    <div class="seat-info-box sold"></div>
                    <span>Satılmış</span>
                </div>
                <div class="seat-info-item">
                    <div class="seat-info-box reserved"></div>
                    <span>Rezerve</span>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-8" data-aos="fade-up">
                    <div class="screen"></div>
                    
                    <div class="seat-container">
                        @php
                            // Koltuk düzeni yoksa varsayılan bir düzen oluştur
                            if (empty($seatData)) {
                                $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                                $seatsPerRow = [10, 10, 10, 10, 10, 10, 10];
                                
                                $seatData = [];
                                foreach ($rows as $index => $row) {
                                    $seatData[$row] = $seatsPerRow[$index];
                                }
                            }
                        @endphp
                        
                        <form id="seatSelectionForm" action="{{ route('showtimes.select-seats') }}" method="POST">
                            @csrf
                            <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
                            
                            @foreach($seatData as $row => $seatsCount)
                                <div class="seat-row">
                                    <div class="seat-label">{{ $row }}</div>
                                    
                                    @for($seatNumber = 1; $seatNumber <= $seatsCount; $seatNumber++)
                                        @php
                                            $seatCode = $row . $seatNumber;
                                            $status = isset($seatStatus[$seatCode]) ? $seatStatus[$seatCode] : 'available';
                                        @endphp
                                        
                                        <div class="seat {{ $status }}" 
                                             data-seat="{{ $seatCode }}" 
                                             @if($status == 'available') onclick="toggleSeat(this)" @endif>
                                            {{ $seatNumber }}
                                        </div>
                                    @endfor
                                </div>
                            @endforeach
                            
                            <input type="hidden" name="seats" id="selectedSeatsInput" value="">
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="ticket-summary">
                        <h3 class="h5 fw-bold mb-4">Bilet Özeti</h3>
                        
                        <div class="mb-3">
                            <div class="ticket-summary-item">
                                <span>Film:</span>
                                <span>{{ $showtime->movie->title }}</span>
                            </div>
                            <div class="ticket-summary-item">
                                <span>Sinema:</span>
                                <span>{{ $showtime->cinemaHall->cinema->name }}</span>
                            </div>
                            <div class="ticket-summary-item">
                                <span>Salon:</span>
                                <span>{{ $showtime->cinemaHall->name }}</span>
                            </div>
                            <div class="ticket-summary-item">
                                <span>Tarih:</span>
                                <span>{{ $showtime->start_time->format('d.m.Y') }}</span>
                            </div>
                            <div class="ticket-summary-item">
                                <span>Saat:</span>
                                <span>{{ $showtime->start_time->format('H:i') }}</span>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <h5 class="fw-bold mb-3">Seçilen Koltuklar</h5>
                            <div id="selectedSeatsContainer" class="selected-seats-container">
                                <p class="text-muted">Henüz koltuk seçilmedi</p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="ticket-summary-item mb-3">
                            <span>Bilet Fiyatı:</span>
                            <span>{{ number_format($showtime->price, 2) }} ₺</span>
                        </div>
                        
                        <div class="ticket-summary-item mb-3">
                            <span>Seçilen Koltuk:</span>
                            <span id="seatCount">0</span>
                        </div>
                        
                        <div class="ticket-summary-item mb-4">
                            <span class="fw-bold">Toplam:</span>
                            <span class="ticket-summary-total" id="totalPrice">0,00 ₺</span>
                        </div>
                        
                        <button type="button" 
                                class="btn continue-btn w-100" 
                                id="continueButton"
                                onclick="submitForm()"
                                disabled>
                            <i class="fas fa-arrow-right me-2"></i> Devam Et
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
let selectedSeats = [];
const ticketPrice = {{ $showtime->price }};

function toggleSeat(element) {
    const seatNumber = element.dataset.seat;
    const index = selectedSeats.indexOf(seatNumber);
    
    if (index === -1) {
        selectedSeats.push(seatNumber);
        element.classList.add('selected');
    } else {
        selectedSeats.splice(index, 1);
        element.classList.remove('selected');
    }
    
    updateSelectedSeatsDisplay();
    updateTotalPrice();
}

function updateSelectedSeatsDisplay() {
    const container = document.getElementById('selectedSeatsContainer');
    const seatCountElement = document.getElementById('seatCount');
    
    seatCountElement.textContent = selectedSeats.length;
    
    if (selectedSeats.length === 0) {
        container.innerHTML = '<p class="text-muted">Henüz koltuk seçilmedi</p>';
        return;
    }
    
    container.innerHTML = selectedSeats
        .sort()
        .map(seat => `<span class="selected-seat-tag">${seat}</span>`)
        .join('');
}

function updateTotalPrice() {
    const totalPriceElement = document.getElementById('totalPrice');
    const totalPrice = selectedSeats.length * ticketPrice;
    totalPriceElement.textContent = new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY'
    }).format(totalPrice);
    
    const continueButton = document.getElementById('continueButton');
    if (selectedSeats.length > 0) {
        continueButton.removeAttribute('disabled');
    } else {
        continueButton.setAttribute('disabled', 'disabled');
    }
}

function submitForm() {
    if (selectedSeats.length === 0) {
        alert('Lütfen en az bir koltuk seçin.');
        return;
    }
    
    document.getElementById('selectedSeatsInput').value = JSON.stringify(selectedSeats);
    
    const form = document.getElementById('seatSelectionForm');
    const formData = new FormData(form);
    
    const continueButton = document.getElementById('continueButton');
    continueButton.disabled = true;
    continueButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> İşleniyor...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 401) {
                // Kullanıcı giriş yapmamış
                return response.json().then(data => {
                    alert('Bu işlemi gerçekleştirmek için giriş yapmalısınız.');
                    window.location.href = data.redirect;
                    throw new Error('Giriş yapılmadı');
                });
            } else if (response.status === 500) {
                throw new Error('Sunucu hatası: İşlem sırasında bir sorun oluştu.');
            } else if (response.status === 404) {
                throw new Error('Sayfa bulunamadı: Route tanımlanmamış olabilir.');
            } else if (response.status === 422) {
                return response.json().then(data => {
                    throw new Error('Validasyon hatası: ' + Object.values(data.errors).flat().join(', '));
                });
            } else {
                return response.json().then(data => {
                    if (data && data.error) {
                        throw new Error(data.error);
                    }
                    throw new Error('Sunucu hatası: ' + response.status);
                }).catch(e => {
                    throw new Error('Sunucu hatası: ' + response.status);
                });
            }
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            alert(data.error);
            continueButton.disabled = false;
            continueButton.innerHTML = '<i class="fas fa-arrow-right me-2"></i> Devam Et';
        } else {
            console.log('Başarılı:', data);
            window.location.href = data.redirect;
        }
    })
    .catch(error => {
        console.error('Hata:', error);
        if (error.message !== 'Giriş yapılmadı') {
            alert(error.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
            continueButton.disabled = false;
            continueButton.innerHTML = '<i class="fas fa-arrow-right me-2"></i> Devam Et';
        }
    });
}
</script>
@endsection 