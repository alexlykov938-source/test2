<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Статистика</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; border-radius: 12px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
        h1 { font-size: 22px; margin-bottom: 8px; color: #111; }
        p  { color: #666; font-size: 14px; margin-bottom: 28px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 6px; }
        input[type=email], input[type=password] { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 15px; outline: none; transition: border .2s; margin-bottom: 18px; }
        input:focus { border-color: #4f46e5; }
        .error { color: #e53e3e; font-size: 13px; margin-top: -14px; margin-bottom: 14px; }
        button { width: 100%; padding: 12px; background: #4f46e5; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background .2s; }
        button:hover { background: #4338ca; }
    </style>
</head>
<body>
<div class="card">
    <h1>📊 Статистика посещений</h1>
    <p>Введите данные для входа</p>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" autofocus>
        @error('email') <div class="error">{{ $message }}</div> @enderror
        <label for="password">Пароль</label>
        <input type="password" id="password" name="password">
        <button type="submit">Войти</button>
    </form>
</div>
</body>
</html>