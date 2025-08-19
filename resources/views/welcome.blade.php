<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main API</title>
    <link rel="icon" href="{{ asset('fire.ico') }}" type="image/x-icon">
    <style>
        /* CSS untuk tema gelap, sederhana, dan tanpa scroll */
        :root {
            --background-color: #121212;
            --surface-color: #1e1e1e;
            --font-color: #e0e0e0;
            --primary-color: #d60101;
            --button-hover: #b40808;
            --border-color: #333333;
        }

        /* Reset dan Styling Dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--font-color);
            background-color: var(--background-color);
            /* Pastikan halaman tidak bisa digulir */
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .container {
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        /* Judul dan Tombol */
        .header {
            margin-bottom: 2rem;
        }

        .header-title {
            font-size: clamp(2rem, 5vw, 3rem);
            margin-bottom: 0.5rem;
        }

        .header-subtitle {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            color: #b0b0b0;
        }

        .documentation-button {
            display: inline-block;
            padding: 0.75rem 2rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        }

        .documentation-button:hover {
            background-color: var(--button-hover);
        }

        /* Daftar Aplikasi */
        .apps-list {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .app-card {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            flex: 1 1 200px;
            max-width: 250px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            /* Tambahan untuk membuat anchor card terlihat seperti block */
            text-decoration: none;
            color: var(--font-color);
            display: block;
        }

        .app-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .app-name {
            font-size: 1.2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .app-description {
            font-size: 0.9rem;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="container">
        <header class="header">
            <img src="{{ asset('fire.ico') }}" alt="Fire Icon" class="fire-icon">
            <h1 class="header-title">Main API</h1>
            <p class="header-subtitle">
                Tulang punggung dari berbagai aplikasi
            </p>
        </header>

        <section class="main-section">
            <a href="{{ URL::to('api/documentation') }}" class="documentation-button">Lihat Dokumentasi</a>
            
            <div class="apps-list">
                <a href="#" class="app-card">
                    <h3 class="app-name">Aplikasi Web</h3>
                    <p class="app-description">Manajemen proyek sederhana</p>
                </a>
            </div>
        </section>
    </div>

</body>
</html>
