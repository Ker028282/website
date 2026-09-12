<?php
session_start();

// Kalau lu udah login sebelumnya, bakal langsung dilempar ke dashboard
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: admin-dashboard.php");
    exit;
}

$error = '';

// Ngecek kalau form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // GANTI USERNAME DAN PASSWORD INI NANTI BRO!
    $valid_username = 'janda';
    $valid_password = 'janda4321';

    if ($username === $valid_username && $password === $valid_password) {
        // Kredensial benar, set session, perbarui ID sesi untuk keamanan, dan alihkan
        session_regenerate_id(true);
        $_SESSION['is_logged_in'] = true;
        header("Location: admin-dashboard.php");
        exit;
    } else {
        $error = 'Username atau Password salah bro!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - FARoki.xyz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
      tailwind.config = {
        theme: {
          extend: {
            animation: {
              'blob': 'blob 7s infinite',
              'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
              'shake': 'shake 0.5s cubic-bezier(.36,.07,.19,.97) both',
            },
            keyframes: {
              blob: {
                '0%': { transform: 'translate(0px, 0px) scale(1)' },
                '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                '100%': { transform: 'translate(0px, 0px) scale(1)' },
              },
              fadeInUp: {
                '0%': { opacity: '0', transform: 'translateY(30px)' },
                '100%': { opacity: '1', transform: 'translateY(0)' },
              },
              shake: {
                '10%, 90%': { transform: 'translate3d(-1px, 0, 0)' },
                '20%, 80%': { transform: 'translate3d(2px, 0, 0)' },
                '30%, 50%, 70%': { transform: 'translate3d(-4px, 0, 0)' },
                '40%, 60%': { transform: 'translate3d(4px, 0, 0)' }
              }
            }
          }
        }
      }
    </script>
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased flex items-center justify-center min-h-screen relative overflow-hidden">
    
    <!-- Background effects biar estetik kaya halaman depan -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full max-w-2xl opacity-40 pointer-events-none -z-10">
        <div class="absolute top-10 left-10 w-72 h-72 bg-blue-600/40 rounded-full mix-blend-screen filter blur-[80px] animate-blob"></div>
        <div class="absolute bottom-10 right-10 w-72 h-72 bg-indigo-600/40 rounded-full mix-blend-screen filter blur-[80px] animate-blob" style="animation-delay: 2s;"></div>
    </div>

    <!-- Kotak Login -->
    <div class="w-full max-w-md p-8 sm:p-10 bg-slate-900/50 backdrop-blur-xl border border-slate-800 rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] z-10 animate-fade-in-up relative group">
        
        <!-- Efek Glow di atas kotak -->
        <div class="absolute inset-x-0 -top-px h-px w-1/2 mx-auto bg-gradient-to-r from-transparent via-blue-500 to-transparent opacity-50 group-hover:opacity-100 transition-opacity"></div>

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-[0_0_20px_rgba(79,70,229,0.4)]">
                <i class="fa-solid fa-user-shield text-2xl text-white"></i>
            </div>
            <h2 class="text-3xl font-extrabold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent mb-1">Login Admin</h2>
            <p class="text-slate-400 text-sm">Area khusus bos FARoki</p>
        </div>

        <!-- Alert kalau password salah -->
        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm text-center flex items-center justify-center gap-2 animate-shake">
                <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-6">
            <!-- Username Input -->
            <div class="relative group/input">
                <label class="block text-sm font-semibold text-slate-300 mb-2 ml-1">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within/input:text-blue-400 transition-colors">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <input type="text" name="username" required autocomplete="off" class="w-full bg-slate-950/50 border border-slate-700/50 rounded-2xl pl-11 pr-4 py-3.5 focus:outline-none focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/50 text-white transition-all shadow-inner placeholder-slate-600" placeholder="Masukkan username">
                </div>
            </div>
            
            <!-- Password Input -->
            <div class="relative group/input">
                <label class="block text-sm font-semibold text-slate-300 mb-2 ml-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500 group-focus-within/input:text-blue-400 transition-colors">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <input type="password" id="passwordInput" name="password" required class="w-full bg-slate-950/50 border border-slate-700/50 rounded-2xl pl-11 pr-12 py-3.5 focus:outline-none focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/50 text-white transition-all shadow-inner placeholder-slate-600" placeholder="Masukkan password">
                    
                    <!-- Toggle Show/Hide Password -->
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-blue-400 transition-colors focus:outline-none">
                        <i id="eyeIcon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full relative overflow-hidden group/btn rounded-2xl mt-4">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 transition-all duration-300 group-hover/btn:scale-105"></div>
                <div class="relative py-4 text-white font-bold text-lg flex justify-center items-center gap-2 shadow-[0_0_20px_rgba(37,99,235,0.3)] group-hover/btn:shadow-[0_0_30px_rgba(37,99,235,0.5)]">
                    Masuk Dashboard <i class="fa-solid fa-arrow-right-to-bracket group-hover/btn:translate-x-1 transition-transform"></i>
                </div>
            </button>
        </form>

        <div class="mt-8 text-center border-t border-slate-800/80 pt-6">
            <!-- Pastikan href ini sesuai sama nama file index lo -->
            <a href="index.html" class="text-sm text-slate-500 hover:text-blue-400 transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <!-- Script buat fitur lihat password -->
    <script>
        function togglePassword() {
            const passInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
                eyeIcon.classList.add('text-blue-400');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
                eyeIcon.classList.remove('text-blue-400');
            }
        }
    </script>
<!-- Code injected by live-server -->
<script>
	// <![CDATA[  <-- For SVG support
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
	// ]]>
</script>
</body>
</html>