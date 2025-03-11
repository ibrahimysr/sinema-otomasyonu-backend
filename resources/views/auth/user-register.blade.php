@extends('layouts.app')

@section('title', 'Kayıt Ol - Sinema Otomasyonu')

@section('styles')
<style>
    .register-section {
        margin-top: 76px; /* Navbar yüksekliği kadar margin */
        min-height: calc(100vh - 76px);
        display: flex;
        align-items: center;
        padding: 3rem 0;
        background: linear-gradient(to right, var(--dark) 0%, var(--darker) 100%);
    }
    
    .register-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .register-card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    }
    
    .register-row {
        display: flex;
        flex-wrap: wrap;
    }
    
    .register-image {
        flex: 1;
        background-image: url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80');
        background-size: cover;
        background-position: center;
        position: relative;
        min-height: 400px;
        display: none;
    }
    
    .register-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.4));
    }
    
    .register-image-content {
        position: relative;
        z-index: 1;
        padding: 2rem;
        color: white;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .register-form {
        flex: 1;
        padding: 2.5rem;
    }
    
    .register-header {
        margin-bottom: 2rem;
        text-align: center;
    }
    
    .register-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--light);
        margin-bottom: 0.5rem;
    }
    
    .register-subtitle {
        color: var(--text-muted);
        font-size: 1rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: var(--light);
    }
    
    .form-control {
        background-color: var(--darker);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--light);
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        background-color: var(--darker);
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(229, 9, 20, 0.25);
    }
    
    .form-check-input {
        background-color: var(--darker);
        border-color: var(--border-color);
    }
    
    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .register-btn {
        background-color: var(--primary);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }
    
    .register-btn:hover {
        background-color: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(229, 9, 20, 0.3);
    }
    
    .register-footer {
        margin-top: 2rem;
        text-align: center;
        color: var(--text-muted);
    }
    
    .register-footer a {
        color: var(--primary);
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .register-footer a:hover {
        color: var(--primary-hover);
        text-decoration: underline;
    }
    
    .social-register {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .social-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem;
        border-radius: 8px;
        background-color: var(--darker);
        border: 1px solid var(--border-color);
        color: var(--light);
        transition: all 0.3s ease;
    }
    
    .social-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    .social-btn i {
        margin-right: 0.5rem;
    }
    
    .social-btn.google {
        color: #ea4335;
    }
    
    .social-btn.facebook {
        color: #3b5998;
    }
    
    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        border-color: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }
    
    @media (min-width: 768px) {
        .register-image {
            display: block;
        }
    }
</style>
@endsection

@section('content')
<section class="register-section">
    <div class="container">
        <div class="register-container">
            <div class="register-card">
                <div class="register-row">
                    <div class="register-image">
                        <div class="register-image-content">
                            <h2 class="h1 mb-4">Sinema Dünyasına<br>Katılın</h2>
                            <p class="lead mb-4">Ücretsiz üye olun ve en yeni filmler, en iyi sinemalar ve en uygun fiyatlarla bilet satın alma ayrıcalıklarından yararlanın.</p>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-film fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1">Sinema Otomasyonu</h4>
                                    <p class="mb-0">Türkiye'nin en kapsamlı sinema bileti satış platformu</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="register-form">
                        <div class="register-header">
                            <h1 class="register-title">Kayıt Ol</h1>
                            <p class="register-subtitle">Hemen ücretsiz hesap oluşturun</p>
                        </div>
                        
                        <div id="register-error" class="alert alert-danger mb-4" style="display: none;"></div>
                        
                        <form id="registerForm" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="name" class="form-label">Ad Soyad</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">E-posta Adresi</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="password" class="form-label">Şifre</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="new-password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Şifre Tekrar</label>
                                <input id="password_confirmation" type="password" class="form-control" 
                                    name="password_confirmation" required autocomplete="new-password">
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        <a href="#" target="_blank">Kullanım Şartları</a>'nı ve <a href="#" target="_blank">Gizlilik Politikası</a>'nı okudum ve kabul ediyorum.
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn register-btn" id="registerButton">
                                    <i class="fas fa-user-plus me-2"></i> Kayıt Ol
                                </button>
                            </div>
                            
                            <div class="register-footer">
                                <p>Zaten hesabınız var mı? <a href="/login-user">Giriş Yap</a></p>
                            </div>
                            
                            <div class="text-center mt-4">
                                <p class="text-muted">Veya şununla kayıt olun:</p>
                                <div class="social-register">
                                    <a href="#" class="social-btn google">
                                        <i class="fab fa-google"></i> Google
                                    </a>
                                    <a href="#" class="social-btn facebook">
                                        <i class="fab fa-facebook-f"></i> Facebook
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // CSRF token ayarı
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Register formu gönderimi
        $('#registerForm').submit(function(e) {
            e.preventDefault();
            
            // Buton durumunu güncelle
            const $button = $('#registerButton');
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Kayıt Yapılıyor...');
            
            // Hata mesajını gizle
            $('#register-error').hide();
            
            // Form verilerini al
            const formData = {
                name: $('#name').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                password_confirmation: $('#password_confirmation').val()
            };
            
            // AJAX isteği gönder
            $.ajax({
                url: '/api/auth/register',
                type: 'POST',
                data: JSON.stringify(formData),
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Kayıt başarılı:', response);
                    
                    // Token'ı localStorage'a kaydet
                    if (response.data && response.data.token) {
                        localStorage.setItem('auth_token', response.data.token);
                        localStorage.setItem('user_id', response.data.id);
                        localStorage.setItem('user_name', response.data.name);
                        localStorage.setItem('user_email', response.data.email);
                    }
                    
                    // Başarılı mesajı göster
                    Swal.fire({
                        icon: 'success',
                        title: 'Kayıt Başarılı!',
                        text: 'Hesabınız başarıyla oluşturuldu.',
                        confirmButtonText: 'Tamam',
                        confirmButtonColor: '#e50914'
                    }).then(() => {
                        // Yönlendirme yap
                        window.location.href = '/';
                    });
                },
                error: function(xhr) {
                    console.error('Kayıt hatası:', xhr);
                    
                    // Hata mesajını göster
                    let errorMessage = 'Kayıt yapılırken bir hata oluştu.';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = [];
                            
                            for (const field in errors) {
                                errorMessages.push(errors[field][0]);
                            }
                            
                            errorMessage = errorMessages.join('<br>');
                        } else if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    }
                    
                    $('#register-error').html(errorMessage).show();
                    
                    // Buton durumunu sıfırla
                    $button.prop('disabled', false).html('<i class="fas fa-user-plus me-2"></i> Kayıt Ol');
                }
            });
        });
    });
</script>
@endpush 