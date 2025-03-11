@extends('layouts.app')

@section('title', 'Kullanıcı Girişi - Sinema Otomasyonu')

@section('styles')
<style>
    .login-section {
        margin-top: 76px; /* Navbar yüksekliği kadar margin */
        min-height: calc(100vh - 76px);
        display: flex;
        align-items: center;
        padding: 3rem 0;
        background: linear-gradient(to right, var(--dark) 0%, var(--darker) 100%);
    }
    
    .login-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .login-card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    }
    
    .login-row {
        display: flex;
        flex-wrap: wrap;
    }
    
    .login-image {
        flex: 1;
        background-image: url('https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80');
        background-size: cover;
        background-position: center;
        position: relative;
        min-height: 400px;
        display: none;
    }
    
    .login-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.4));
    }
    
    .login-image-content {
        position: relative;
        z-index: 1;
        padding: 2rem;
        color: white;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .login-form {
        flex: 1;
        padding: 2.5rem;
    }
    
    .login-header {
        margin-bottom: 2rem;
        text-align: center;
    }
    
    .login-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--light);
        margin-bottom: 0.5rem;
    }
    
    .login-subtitle {
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
    
    .login-btn {
        background-color: var(--primary);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }
    
    .login-btn:hover {
        background-color: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(229, 9, 20, 0.3);
    }
    
    .login-footer {
        margin-top: 2rem;
        text-align: center;
        color: var(--text-muted);
    }
    
    .login-footer a {
        color: var(--primary);
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .login-footer a:hover {
        color: var(--primary-hover);
        text-decoration: underline;
    }
    
    .social-login {
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
        .login-image {
            display: block;
        }
    }
</style>
@endsection

@section('content')
<section class="login-section">
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-row">
                    <div class="login-image">
                        <div class="login-image-content">
                            <h2 class="h1 mb-4">Sinema Dünyasına<br>Hoş Geldiniz</h2>
                            <p class="lead mb-4">En yeni filmler, en iyi sinemalar ve en uygun fiyatlarla bilet satın alma deneyimi için giriş yapın.</p>
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
                    
                    <div class="login-form">
                        <div class="login-header">
                            <h1 class="login-title">Giriş Yap</h1>
                            <p class="login-subtitle">Hesabınıza giriş yaparak devam edin</p>
                        </div>
                        
                        <div id="login-error" class="alert alert-danger mb-4" style="display: none;"></div>
                        
                        <form id="loginForm" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="email" class="form-label">E-posta Adresi</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="password" class="form-label">Şifre</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        Beni Hatırla
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn login-btn" id="loginButton">
                                    <i class="fas fa-sign-in-alt me-2"></i> Giriş Yap
                                </button>
                            </div>
                            
                            <div class="login-footer">
                                <p>Hesabınız yok mu? <a href="/register-user">Kayıt Ol</a></p>
                                <p><a href="#">Şifremi Unuttum</a></p>
                            </div>
                            
                            <div class="text-center mt-4">
                                <p class="text-muted">Veya şununla giriş yapın:</p>
                                <div class="social-login">
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $('#loginForm').submit(function(e) {
            e.preventDefault();
            
            const $button = $('#loginButton');
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Giriş Yapılıyor...');
            
            $('#login-error').hide();
            
            const formData = {
                email: $('#email').val(),
                password: $('#password').val(),
                remember: $('#remember').is(':checked')
            };
            
            $.ajax({
                url: '/api/auth/login',
                type: 'POST',
                data: JSON.stringify(formData),
                dataType: 'json',
                contentType: 'application/json',
                headers: {
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Giriş başarılı:', response);
                    
                    if (response.data && response.data.token) {
                        localStorage.setItem('auth_token', response.data.token);
                        localStorage.setItem('user_id', response.data.id);
                        localStorage.setItem('user_name', response.data.name);
                        localStorage.setItem('user_email', response.data.email);
                    }
                    
                    // Başarılı mesajı göster
                    Swal.fire({
                        icon: 'success',
                        title: 'Giriş Başarılı!',
                        text: 'Yönlendiriliyorsunuz...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Yönlendirme yap
                        const returnUrl = new URLSearchParams(window.location.search).get('returnUrl');
                        window.location.href = returnUrl || '/';
                    });
                },
                error: function(xhr) {
                    console.error('Giriş hatası:', xhr);
                    
                    let errorMessage = 'Giriş yapılırken bir hata oluştu.';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors && xhr.responseJSON.errors.email) {
                            errorMessage = xhr.responseJSON.errors.email[0];
                        } else if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    }
                    
                    $('#login-error').text(errorMessage).show();
                    
                    $button.prop('disabled', false).html('<i class="fas fa-sign-in-alt me-2"></i> Giriş Yap');
                }
            });
        });
    });
</script>
@endpush 