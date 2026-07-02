<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Risk Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">🌍 Global Supply Chain Risk Platform</span>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-secondary">Pilih Negara Tujuan Logistik</h5>
                        <select id="countrySelect" class="form-select form-select-lg">
                            <option value="" selected disabled>-- Sedang memuat data negara... --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="resultArea" style="display: none;">
            
            <div class="col-md-4 mb-3">
                <div class="card text-white shadow" id="riskCard">
                    <div class="card-header fw-bold">TOTAL RISK SCORE</div>
                    <div class="card-body text-center">
                        <h1 class="display-1 fw-bold" id="riskScoreDisplay">0</h1>
                        <h4 id="riskStatusDisplay">Status</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white fw-bold">🌤️ Cuaca Saat Ini (Open-Meteo)</div>
                    <div class="card-body">
                        <h5 class="card-text mt-2">Suhu: <span id="tempDisplay" class="fw-bold fs-4 text-info">-</span> °C</h5>
                        <h5 class="card-text mt-3">Angin: <span id="windDisplay" class="fw-bold fs-4 text-warning">-</span> km/jam</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white fw-bold">📈 Ekonomi (World Bank)</div>
                    <div class="card-body">
                        <h5 class="card-text mt-2">Inflasi: <span id="inflationDisplay" class="fw-bold fs-4 text-danger">-</span> %</h5>
                        <p class="text-muted mt-3 mb-0"><small>*Inflasi tinggi menaikkan biaya logistik</small></p>
                    </div>
                </div>
            </div>

        </div> </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countrySelect = document.getElementById('countrySelect');
            const resultArea = document.getElementById('resultArea');
            const riskCard = document.getElementById('riskCard');

            // 1. Ambil daftar negara saat halaman pertama kali dibuka
            fetch('{{ url("/api/countries") }}')
                .then(response => response.json())
                .then(data => {
                    countrySelect.innerHTML = '<option value="" selected disabled>-- Pilih Negara Tujuan --</option>';
                    data.forEach(country => {
                        const option = document.createElement('option');
                        option.value = country.code; 
                        option.textContent = country.name + ' (' + country.region + ')';
                        countrySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching countries:', error));

            // 2. Ketika user mengganti pilihan negara
            countrySelect.addEventListener('change', function() {
                const selectedCode = this.value;
                
                // Tampilkan area hasil dan set jadi loading
                resultArea.style.display = 'flex';
                document.getElementById('riskScoreDisplay').innerText = '...';
                document.getElementById('riskStatusDisplay').innerText = 'Menghitung...';
                riskCard.className = 'card text-white shadow bg-secondary'; // Warna loading

                // Panggil API Internal untuk menghitung risiko
                fetch('{{ url("/api/risk") }}?code=' + selectedCode)
                    .then(response => response.json())
                    .then(data => {
                        // Jika ada error dari server
                        if(data.error) {
                            alert(data.error);
                            return;
                        }

                        // A. Tampilkan Data Risiko
                        const riskData = data.risk_analysis;
                        document.getElementById('riskScoreDisplay').innerText = riskData.total_score;
                        document.getElementById('riskStatusDisplay').innerText = riskData.status;

                        // Ubah warna kartu risiko otomatis (Hijau/Kuning/Merah)
                        if(riskData.status === 'Low Risk') {
                            riskCard.className = 'card text-white shadow bg-success';
                        } else if(riskData.status === 'Medium Risk') {
                            riskCard.className = 'card text-dark shadow bg-warning';
                        } else {
                            riskCard.className = 'card text-white shadow bg-danger';
                        }

                        // B. Tampilkan Data Cuaca
                        const weather = data.raw_data.weather;
                        document.getElementById('tempDisplay').innerText = weather ? weather.temperature : 'N/A';
                        document.getElementById('windDisplay').innerText = weather ? weather.windspeed : 'N/A';

                        // C. Tampilkan Data Ekonomi
                        const economy = data.raw_data.economy;
                        document.getElementById('inflationDisplay').innerText = economy && economy.inflation ? economy.inflation : '0';
                    })
                    .catch(error => {
                        console.error('Error fetching risk data:', error);
                        alert("Gagal menarik data. Pastikan API berjalan.");
                    });
            });
        });
    </script>
</body>
</html>