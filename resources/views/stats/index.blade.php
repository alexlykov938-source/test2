<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика посещений</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f0f2f5;color:#111}
        header{background:#fff;padding:16px 32px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 4px rgba(0,0,0,.08)}
        header h1{font-size:18px}
        header .meta{font-size:13px;color:#666}
        header a{color:#4f46e5;text-decoration:none;font-size:14px}
        .container{max-width:1200px;margin:0 auto;padding:32px 24px}
        .kpi-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:32px}
        .kpi{background:#fff;border-radius:12px;padding:20px 24px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
        .kpi .label{font-size:12px;color:#888;text-transform:uppercase;letter-spacing:.5px}
        .kpi .value{font-size:36px;font-weight:700;color:#4f46e5;margin-top:4px}
        .filters{margin-bottom:24px;display:flex;align-items:center;gap:12px}
        .filters label{font-size:14px;font-weight:600}
        .filters input[type=date]{padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:14px}
        .filters button{padding:8px 16px;background:#4f46e5;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:14px}
        .charts{display:grid;grid-template-columns:2fr 1fr;gap:24px}
        @media(max-width:768px){.charts{grid-template-columns:1fr}}
        .chart-card{background:#fff;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
        .chart-card h2{font-size:15px;font-weight:600;margin-bottom:20px;color:#333}
    </style>
</head>
<body>

<header>
    <h1>📊 Статистика посещений</h1>
    <div class="meta">
        Вы вошли как <strong>{{ Auth::user()->email }}</strong>
        &nbsp;·&nbsp;
        <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit()">Выйти</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>
</header>

<div class="container">

    <form class="filters" method="GET" action="{{ route('stats.index') }}">
        <label for="date">Дата:</label>
        <input type="date" id="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}">
        <button type="submit">Применить</button>
    </form>

    <div class="kpi-row">
        <div class="kpi"><div class="label">Всего за {{ $date }}</div><div class="value">{{ $totalToday }}</div></div>
        <div class="kpi"><div class="label">Уникальных IP</div><div class="value">{{ $uniqueToday }}</div></div>
        <div class="kpi"><div class="label">Городов (7 дней)</div><div class="value">{{ $citiesData->count() }}</div></div>
    </div>

    <div class="charts">
        <div class="chart-card">
            <h2>Уникальные посещения по часам — {{ $date }}</h2>
            <canvas id="hourlyChart" height="120"></canvas>
        </div>
        <div class="chart-card">
            <h2>Города (7 дней)</h2>
            <canvas id="citiesChart"></canvas>
        </div>
    </div>
</div>

<script>
const hourlyData = @json($hourlyData);
const citiesData = @json($citiesData);
const PALETTE = ['#4f46e5','#10b981','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#84cc16'];

new Chart(document.getElementById('hourlyChart'), {
    type: 'bar',
    data: {
        labels: hourlyData.map(d => d.hour),
        datasets: [{ label: 'Уникальных IP', data: hourlyData.map(d => d.count), backgroundColor: '#4f46e5', borderRadius: 4 }],
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, title: { display: true, text: 'Посещений' } },
            x: { title: { display: true, text: 'Час' } },
        },
    },
});

new Chart(document.getElementById('citiesChart'), {
    type: 'pie',
    data: {
        labels: citiesData.map(d => d.city || 'unknown'),
        datasets: [{ data: citiesData.map(d => d.count), backgroundColor: citiesData.map((_,i) => PALETTE[i % PALETTE.length]), borderWidth: 2, borderColor: '#fff' }],
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { padding: 12, font: { size: 12 } } } } },
});
</script>
</body>
</html>