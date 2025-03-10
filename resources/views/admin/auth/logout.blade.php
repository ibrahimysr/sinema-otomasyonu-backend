<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çıkış Yapılıyor...</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            localStorage.removeItem('token');
            
            setTimeout(function() {
                window.location.href = '/login';
            }, 1000);
        });
    </script>
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column;">
        <h3>Çıkış Yapılıyor...</h3>
        <p>Lütfen bekleyin, yönlendiriliyorsunuz.</p>
    </div>
</body>
</html> 