@extends('layouts.app')

@section('title', 'Bilet Onayı - Sinema Otomasyonu')

@section('styles')
<style>
    .confirmation-header {
        background: linear-gradient(to right, var(--dark) 0%, var(--darker) 100%);
        color: var(--light);
        padding: 3rem 0;
        margin-top: 76px; 
        text-align: center;
    }
    
    .success-icon {
        font-size: 80px;
        color: var(--success);
        margin-bottom: 1.5rem;
    }
    
    .ticket-card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        margin-bottom: 2rem;
    }
    
    .ticket-header {
        background: linear-gradient(to right, var(--primary-dark) 0%, var(--primary) 100%);
        color: white;
        padding: 1.5rem;
    }
    
    .ticket-body {
        padding: 2rem;
        color: var(--light);
    }
    
    .ticket-footer {
        background-color: var(--darker);
        padding: 1rem;
        border-top: 1px dashed var(--border-color);
        color: var(--light);
    }
    
    .ticket-info {
        margin-bottom: 1.5rem;
    }
    
    .ticket-info-label {
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }
    
    .ticket-info-value {
        font-weight: 600;
        color: var(--light);
    }
    
    .ticket-seats {
        display: flex;
        flex-wrap: wrap;
        margin-top: 0.5rem;
        gap: 0.5rem;
    }
    
    .ticket-seat {
        background-color: var(--primary);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    
    .ticket-seat:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }
    
    .qr-code {
        width: 150px;
        height: 150px;
        background-color: white;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }
    
    .payment-card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        margin-bottom: 2rem;
    }
    
    .payment-header {
        background: linear-gradient(to right, var(--primary-dark) 0%, var(--primary) 100%);
        color: white;
        padding: 1.25rem 1.5rem;
    }
    
    .payment-body {
        padding: 1.5rem;
        color: var(--light);
    }
    
    .payment-method {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        padding: 1rem;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: var(--darker);
    }
    
    .payment-method:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .payment-method.active {
        border-color: var(--primary);
        background-color: var(--primary-dark);
    }
    
    .payment-method-icon {
        font-size: 24px;
        margin-right: 15px;
        color: var(--text-muted);
    }
    
    .payment-method.active .payment-method-icon {
        color: var(--primary);
    }
    
    .payment-method-title {
        font-weight: 600;
        color: var(--light);
    }
    
    .payment-method-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    
    .alert-info {
        background-color: rgba(13, 202, 240, 0.1);
        border-color: rgba(13, 202, 240, 0.2);
        color: var(--info);
    }
    
    .alert-primary {
        background-color: rgba(var(--primary-rgb), 0.1);
        border-color: rgba(var(--primary-rgb), 0.2);
        color: var(--primary);
    }
    
    .alert-warning {
        background-color: rgba(255, 193, 7, 0.1);
        border-color: rgba(255, 193, 7, 0.2);
        color: #ffc107;
    }
    
    .alert-success {
        background-color: rgba(25, 135, 84, 0.1);
        border-color: rgba(25, 135, 84, 0.2);
        color: #198754;
    }
    
    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        transform: translateY(-2px);
    }
    
    .btn-outline-primary {
        border-color: var(--primary);
        color: var(--primary);
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary);
        color: white;
        transform: translateY(-2px);
    }
    
    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 1rem;
    }
</style>
@endsection

@section('content')
    <!-- Başlık -->
    <section class="confirmation-header">
        <div class="container">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="display-5 fw-bold">Biletiniz Başarıyla Oluşturuldu!</h1>
            <p class="lead">Bilet detaylarınız e-posta adresinize gönderilmiştir.</p>
        </div>
    </section>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Bilet Kartı -->
                <div class="ticket-card" data-aos="fade-up">
                    <div class="ticket-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="h4 fw-bold mb-0">{{ $showtime->movie->title }}</h2>
                                <p class="mb-0 opacity-75">{{ $showtime->cinemaHall->cinema->name }} - {{ $showtime->cinemaHall->name }}</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-star"></i> {{ $showtime->movie->imdb_rating }}
                                </span>
                                <span class="badge bg-light text-dark ms-2">
                                    {{ $showtime->movie->duration }} dk
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ticket-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="ticket-info">
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-4">
                                            <div class="ticket-info-label">Tarih</div>
                                            <div class="ticket-info-value">{{ $showtime->start_time->format('d.m.Y') }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="ticket-info-label">Saat</div>
                                            <div class="ticket-info-value">{{ $showtime->start_time->format('H:i') }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="ticket-info-label">Salon</div>
                                            <div class="ticket-info-value">{{ $showtime->cinemaHall->name }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="ticket-info-label">Koltuklar</div>
                                    <div class="ticket-seats">
                                        @php
                                            // Koltukları sırala
                                            $sortedSeats = $seats;
                                            usort($sortedSeats, function($a, $b) {
                                                if ($a[0] !== $b[0]) {
                                                    return $a[0] <=> $b[0];
                                                }
                                                return intval(substr($a, 1)) <=> intval(substr($b, 1));
                                            });
                                        @endphp
                                        
                                        @foreach($sortedSeats as $seat)
                                            <div class="ticket-seat">{{ $seat }}</div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-4">
                                    <i class="fas fa-info-circle me-2"></i> Lütfen seansın başlamasından en az 15 dakika önce sinemada olunuz.
                                </div>
                            </div>
                            
                            <div class="col-md-4 text-center">
                                <div class="qr-code">
                                    <i class="fas fa-qrcode fa-5x text-muted"></i>
                                </div>
                                <div class="ticket-info-label">Bilet Kodu</div>
                                <div class="ticket-info-value">{{ $tickets[0]->ticket_code }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ticket-footer text-center">
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i> {{ $showtime->cinemaHall->cinema->address }}
                        </p>
                    </div>
                </div>
                
                <!-- Ödeme Bölümü -->
                <div class="payment-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="payment-header">
                        <h3 class="h5 fw-bold mb-0">Ödeme Yap</h3>
                    </div>
                    <div class="payment-body">
                        <div class="alert alert-primary mb-4">
                            <i class="fas fa-info-circle me-2"></i> Toplam Tutar: <strong>{{ number_format($tickets[0]->price * count($seats), 2) }} ₺</strong>
                        </div>
                        
                        @if($isLoggedIn)
                        <!-- Kullanıcı giriş yapmış -->
                        <form id="paymentForm">
                            <input type="hidden" id="ticketId" value="{{ $tickets[0]->id }}">
                            <input type="hidden" id="userId" value="{{ $userId }}">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Ödeme Yöntemi Seçin</label>
                                
                                <div class="payment-method active" data-method="online">
                                    <i class="fas fa-credit-card payment-method-icon"></i>
                                    <div>
                                        <div class="payment-method-title">Online Ödeme</div>
                                        <div class="payment-method-desc">Kredi kartı veya banka kartı ile güvenli ödeme</div>
                                    </div>
                                </div>
                                
                                <div class="payment-method" data-method="credit_card">
                                    <i class="fas fa-credit-card payment-method-icon"></i>
                                    <div>
                                        <div class="payment-method-title">Kredi Kartı</div>
                                        <div class="payment-method-desc">Visa, Mastercard, American Express</div>
                                    </div>
                                </div>
                                
                                <div class="payment-method" data-method="bank_transfer">
                                    <i class="fas fa-university payment-method-icon"></i>
                                    <div>
                                        <div class="payment-method-title">Banka Havalesi</div>
                                        <div class="payment-method-desc">Banka hesabımıza havale yapın</div>
                                    </div>
                                </div>
                                
                                <div class="payment-method" data-method="cash">
                                    <i class="fas fa-money-bill-wave payment-method-icon"></i>
                                    <div>
                                        <div class="payment-method-title">Nakit Ödeme</div>
                                        <div class="payment-method-desc">Sinema gişesinde nakit ödeme yapın</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="button" id="paymentButton" class="btn btn-primary py-2">
                                    <i class="fas fa-lock me-2"></i> Ödemeyi Tamamla
                                </button>
                            </div>
                        </form>
                        @else
                        <!-- Kullanıcı giriş yapmamış -->
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i> Ödeme yapabilmek için giriş yapmanız gerekmektedir.
                        </div>
                        <div class="d-grid">
                            <a href="{{ route('login-user') }}" class="btn btn-primary py-2">
                                <i class="fas fa-sign-in-alt me-2"></i> Giriş Yap
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="action-buttons" data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i> Ana Sayfaya Dön
                    </a>
                    <a href="#" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> Bileti Yazdır
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    @section('scripts')
    <script>
        $(document).ready(function() {
            // Kullanıcının giriş durumunu kontrol et
            const authToken = localStorage.getItem('auth_token');
            const userId = localStorage.getItem('user_id');
            
            // Eğer token varsa, kullanıcı giriş yapmış demektir
            if (authToken) {
                // Ödeme formunu göster
                showPaymentForm();
            } else {
                // Giriş yapma uyarısını göster
                showLoginWarning();
            }
            
            // Ödeme formunu göster
            function showPaymentForm() {
                $('.payment-body').html(`
                    <div class="alert alert-primary mb-4">
                        <i class="fas fa-info-circle me-2"></i> Toplam Tutar: <strong>{{ number_format($tickets[0]->price * count($seats), 2) }} ₺</strong>
                    </div>
                    
                    <form id="paymentForm">
                        <input type="hidden" id="ticketId" value="{{ $tickets[0]->id }}">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Ödeme Yöntemi Seçin</label>
                            
                            <div class="payment-method active" data-method="online">
                                <i class="fas fa-credit-card payment-method-icon"></i>
                                <div>
                                    <div class="payment-method-title">Online Ödeme</div>
                                    <div class="payment-method-desc">Kredi kartı veya banka kartı ile güvenli ödeme</div>
                                </div>
                            </div>
                            
                            <div class="payment-method" data-method="credit_card">
                                <i class="fas fa-credit-card payment-method-icon"></i>
                                <div>
                                    <div class="payment-method-title">Kredi Kartı</div>
                                    <div class="payment-method-desc">Visa, Mastercard, American Express</div>
                                </div>
                            </div>
                            
                            <div class="payment-method" data-method="bank_transfer">
                                <i class="fas fa-university payment-method-icon"></i>
                                <div>
                                    <div class="payment-method-title">Banka Havalesi</div>
                                    <div class="payment-method-desc">Banka hesabımıza havale yapın</div>
                                </div>
                            </div>
                            
                            <div class="payment-method" data-method="cash">
                                <i class="fas fa-money-bill-wave payment-method-icon"></i>
                                <div>
                                    <div class="payment-method-title">Nakit Ödeme</div>
                                    <div class="payment-method-desc">Sinema gişesinde nakit ödeme yapın</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="button" id="paymentButton" class="btn btn-primary py-2">
                                <i class="fas fa-lock me-2"></i> Ödemeyi Tamamla
                            </button>
                        </div>
                    </form>
                `);
                
                // Ödeme yöntemi seçimi
                $('.payment-method').click(function() {
                    $('.payment-method').removeClass('active');
                    $(this).addClass('active');
                });
                
                // Ödeme işlemi
                $('#paymentButton').click(function() {
                    const ticketId = $('#ticketId').val();
                    const paymentMethod = $('.payment-method.active').data('method');
                    
                    // Buton durumunu güncelle
                    const $button = $(this);
                    $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> İşleniyor...');
                    
                    // API isteği gönder
                    $.ajax({
                        url: '/api/payments/payment-add',
                        type: 'POST',
                        dataType: 'json',
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Authorization': 'Bearer ' + authToken
                        },
                        data: JSON.stringify({
                            ticket_id: ticketId,
                            payment_method: paymentMethod
                        }),
                        success: function(response) {
                            console.log('Ödeme başarılı:', response);
                            
                            // Başarılı ödeme
                            Swal.fire({
                                icon: 'success',
                                title: 'Ödeme Başarılı!',
                                text: 'Ödemeniz başarıyla tamamlandı.',
                                confirmButtonText: 'Tamam',
                                confirmButtonColor: '#e50914'
                            }).then(() => {
                                // Ödeme yöntemini göster
                                let methodText = '';
                                switch(paymentMethod) {
                                    case 'online':
                                        methodText = 'Online Ödeme';
                                        break;
                                    case 'credit_card':
                                        methodText = 'Kredi Kartı';
                                        break;
                                    case 'bank_transfer':
                                        methodText = 'Banka Havalesi';
                                        break;
                                    case 'cash':
                                        methodText = 'Nakit Ödeme';
                                        break;
                                }
                                
                                // Ödeme bölümünü başarılı mesajla değiştir
                                $('.payment-body').html(`
                                    <div class="alert alert-success mb-0">
                                        <i class="fas fa-check-circle me-2"></i> 
                                        <strong>Ödeme Başarıyla Tamamlandı!</strong><br>
                                        Ödeme Yöntemi: ${methodText}<br>
                                        Tarih: ${new Date().toLocaleString('tr-TR')}<br>
                                        Tutar: ${$('.alert-primary strong').text()}<br>
                                        İşlem No: ${response.data ? response.data.id : 'N/A'}
                                    </div>
                                `);
                                
                                // Ödeme başlığını güncelle
                                $('.payment-header h3').text('Ödeme Bilgileri');
                            });
                        },
                        error: function(xhr) {
                            console.error('Ödeme hatası:', xhr);
                            
                            if (xhr.status === 401) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Oturum Süresi Dolmuş',
                                    text: 'Oturum süreniz dolmuş. Lütfen tekrar giriş yapın.',
                                    confirmButtonText: 'Giriş Yap',
                                    confirmButtonColor: '#e50914',
                                    showCancelButton: true,
                                    cancelButtonText: 'İptal'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('login-user') }}?returnUrl=" + encodeURIComponent(window.location.href);
                                    } else {
                                        $button.prop('disabled', false).html('<i class="fas fa-lock me-2"></i> Ödemeyi Tamamla');
                                    }
                                });
                                return;
                            }
                            
                            let errorMessage = 'Ödeme işlemi sırasında bir hata oluştu.';
                            
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Ödeme Başarısız',
                                text: errorMessage,
                                confirmButtonText: 'Tamam',
                                confirmButtonColor: '#e50914'
                            });
                            
                            $button.prop('disabled', false).html('<i class="fas fa-lock me-2"></i> Ödemeyi Tamamla');
                        }
                    });
                });
            }
            
            function showLoginWarning() {
                $('.payment-body').html(`
                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i> Ödeme yapabilmek için giriş yapmanız gerekmektedir.
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('login-user') }}?returnUrl={{ urlencode(url()->current()) }}" class="btn btn-primary py-2">
                            <i class="fas fa-sign-in-alt me-2"></i> Giriş Yap
                        </a>
                    </div>
                `);
            }
        });
    </script>
    @endsection
@endsection 