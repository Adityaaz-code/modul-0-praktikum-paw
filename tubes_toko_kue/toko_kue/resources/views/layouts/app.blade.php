<!DOCTYPE html>
<html>
<head>
    <title>Toko Kue - @yield('title')</title>
    <style>
        /* CSS GLOBAL SESUAI MODUL 1 PAW */
        body { font-family: Arial, sans-serif; padding: 20px; }
        
        /* Styling Form Input */
        input[type="text"], input[type="number"], textarea, select {
            padding: 10px; border-radius: 5px; border: 1px solid #ccc;
            width: 100%; box-sizing: border-box; margin-bottom: 10px;
        }
        /* Efek Focus Hijau */
        input:focus, textarea:focus, select:focus {
            border-color: #28a745; 
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5); /* Highlight Hijau */
        }
        
        /* Styling Tombol Submit */
        .btn-submit {
            background-color: #28a745; /* Warna Hijau */
            color: white; padding: 10px 15px; border: none; 
            border-radius: 5px; cursor: pointer;
            text-decoration: none; display: inline-block;
        }
        .btn-submit:hover { background-color: #218838; } /* Efek Hover Hijau */
        
        /* Styling Tabel */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ccc; }
        th { background-color: #28a745; color: white; } /* Header Hijau */
        tr:nth-child(even) { background-color: #f2f2f2; } /* Baris Bergantian */
        tr:hover { background-color: #ddd; } /* Efek Hover */
        
        /* Styling Error Message */
        .error-message { color: #dc3545; font-size: 12px; margin-top: -8px; margin-bottom: 8px; }
        .success-message { color: green; font-weight: bold; margin-bottom: 15px; }
        
    </style>
</head>
<body>
    <div class="container">
        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error-message">{{ session('error') }}</div>
        @endif
        
        @yield('content')
    </div>
</body>
</html>