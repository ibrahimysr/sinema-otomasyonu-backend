<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sinema Otomasyonu - Giriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo i {
            font-size: 48px;
            color: #0d6efd;
        }
        .login-form .form-control {
            padding: 12px;
            border-radius: 5px;
        }
        .login-btn {
            padding: 12px;
            border-radius: 5px;
            width: 100%;
        }
        #debug-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 12px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <i class="fas fa-film"></i>
            <h3>Sinema Otomasyonu</h3>
            <p>Admin Paneli</p>
        </div>
        
        <div id="alert-container"></div>
        
        <form class="login-form" id="loginForm">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">E-posta</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="E-posta adresinizi girin" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Şifrenizi girin" required>
            </div>
            <button type="submit" class="btn btn-primary login-btn">Giriş Yap</button>
        </form>
        
        <div id="debug-info"></div>
        
        <div class="text-center mt-3">
            <button id="toggleDebug" class="btn btn-sm btn-outline-secondary">Hata Ayıklama</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#toggleDebug').on('click', function() {
                $('#debug-info').toggle();
            });
            
            const token = localStorage.getItem('token');
            if (token) {
                checkUserAndRedirect(token);
            }
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                const email = $('#email').val();
                const password = $('#password').val();
                
                $('.login-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Giriş yapılıyor...');
                
                $.ajax({
                    url: '/api/auth/login',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        email: email,
                        password: password
                    }),
                    success: function(response) {
                        $('#debug-info').html('<strong>API Yanıtı:</strong><br><pre>' + JSON.stringify(response, null, 2) + '</pre>');
                        
                        let token = null;
                        
                        if (response && response.data && response.data.token) {
                            token = response.data.token;
                        } else if (response && response.token) {
                            token = response.token;
                        } else if (response && response.access_token) {
                            token = response.access_token;
                        }
                        
                        if (token) {
                            localStorage.setItem('token', token);
                            
                            checkUserAndRedirect(token);
                        } else {
                            showAlert('danger', 'Token alınamadı. API yanıtını kontrol edin.');
                            $('.login-btn').prop('disabled', false).text('Giriş Yap');
                        }
                    },
                    error: function(error) {
                        $('#debug-info').html('<strong>Hata:</strong><br><pre>' + JSON.stringify(error, null, 2) + '</pre>');
                        
                        let errorMessage = 'Giriş yapılırken bir hata oluştu.';
                        
                        if (error.responseJSON && error.responseJSON.message) {
                            errorMessage = error.responseJSON.message;
                        }
                        
                        showAlert('danger', errorMessage);
                        $('.login-btn').prop('disabled', false).text('Giriş Yap');
                    }
                });
            });
            
            function checkUserAndRedirect(token) {
                $.ajax({
                    url: '/api/auth/user',
                    method: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(userResponse) {
                        $('#debug-info').append('<br><strong>Kullanıcı Yanıtı:</strong><br><pre>' + JSON.stringify(userResponse, null, 2) + '</pre>');
                        
                        let user = null;
                        let role = null;
                        
                        if (userResponse && userResponse.data) {
                            user = userResponse.data;
                            role = user.role_name;
                        } else if (userResponse && userResponse.user) {
                            user = userResponse.user;
                            role = user.role_name;
                        } else {
                            user = userResponse;
                            role = user.role_name;
                        }
                        
                        if (user && (role === 'admin' || role === 'super_admin')) {
                            window.location.href = '/admin/dashboard';
                        } else {
                            showAlert('danger', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
                            localStorage.removeItem('token');
                            $('.login-btn').prop('disabled', false).text('Giriş Yap');
                        }
                    },
                    error: function(error) {
                        $('#debug-info').append('<br><strong>Kullanıcı Bilgisi Hatası:</strong><br><pre>' + JSON.stringify(error, null, 2) + '</pre>');
                        
                        console.error('Kullanıcı bilgileri alınamadı:', error);
                        showAlert('danger', 'Kullanıcı bilgileri alınamadı. Lütfen tekrar giriş yapın.');
                        localStorage.removeItem('token');
                        $('.login-btn').prop('disabled', false).text('Giriş Yap');
                    }
                });
            }
            
            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                
                $('#alert-container').html(alertHtml);
            }
        });
    </script>
</body>
</html> 