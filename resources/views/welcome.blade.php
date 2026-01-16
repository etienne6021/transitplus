<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transit Plus - SaaS Maritime Intelligence</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-sea { background-color: #0A4D68; }
        .text-sea { color: #0A4D68; }
        .bg-accent { background-color: #F37335; }
        .border-accent { border-color: #F37335; }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, #0A4D68 0%, #082f49 100%);
        }
    </style>
</head>
<body class="hero-gradient text-white min-h-screen">
    <!-- Navigation -->
    <nav class="flex items-center justify-between px-12 py-8">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/logo.png') }}" class="h-12 w-auto" alt="Logo">
            <span class="text-2xl font-bold tracking-tight">TRANSIT <span class="text-[#F37335]">PLUS</span></span>
        </div>
        <div class="hidden md:flex space-x-8 font-medium">
            <a href="#" class="hover:text-[#F37335] transition">Solutions</a>
            <a href="#" class="hover:text-[#F37335] transition">Modules</a>
            <a href="#" class="hover:text-[#F37335] transition">Tarifs</a>
        </div>
        <div>
            <a href="/admin/login" class="bg-[#F37335] hover:bg-[#e66a2e] px-6 py-2.5 rounded-full font-semibold transition shadow-lg shadow-orange-500/20">
                Connexion
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="container mx-auto px-6 pt-20 flex flex-col items-center text-center">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
            L'excellence du <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F37335] to-orange-300">Transit Douane</span><br>
            dans un SaaS Intelligent.
        </h1>
        <p class="text-xl text-blue-100/70 max-w-2xl mb-12">
            Une plateforme modulaire conçue pour les experts du transit et la gestion de MAD.
            Pilotez votre activité avec une précision chirurgicale et une élégance rare.
        </p>

        <div class="flex space-x-4">
            <button class="bg-[#F37335] px-10 py-4 rounded-xl font-bold text-lg hover:scale-105 transition shadow-2xl shadow-orange-500/30">
                Démarrer l'essai gratuit
            </button>
            <button class="glass px-10 py-4 rounded-xl font-bold text-lg hover:bg-white/10 transition">
                Voir la démo
            </button>
        </div>

        <!-- App Preview Mockup -->
        <div class="mt-24 relative max-w-5xl">
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-[#F37335] rounded-full blur-[100px] opacity-20"></div>
            <div class="absolute -bottom-10 -right-10 w-60 h-60 bg-[#0ea5e9] rounded-full blur-[100px] opacity-20"></div>
            
            <div class="relative glass p-4 rounded-3xl shadow-2xl">
                <div class="bg-[#082f49] rounded-2xl overflow-hidden border border-white/10 aspect-video flex">
                    <!-- Sidebar Mock -->
                    <div class="w-1/5 border-r border-white/5 p-4 flex flex-col space-y-4">
                        <div class="h-2 w-12 bg-white/20 rounded"></div>
                        <div class="h-8 w-full bg-[#F37335]/20 border-l-2 border-[#F37335] rounded-r"></div>
                        <div class="h-4 w-full bg-white/5 rounded"></div>
                        <div class="h-4 w-full bg-white/5 rounded"></div>
                        <div class="h-4 w-full bg-white/5 rounded"></div>
                    </div>
                    <!-- Content Mock -->
                    <div class="flex-1 p-8 text-left">
                        <div class="flex justify-between items-center mb-10">
                            <div class="h-8 w-48 bg-white/10 rounded"></div>
                            <div class="h-10 w-10 bg-[#F37335] rounded-full"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-6 mb-10">
                            <div class="h-32 bg-white/5 rounded-2xl border border-white/5 p-4">
                                <div class="h-4 w-16 bg-white/20 rounded mb-4"></div>
                                <div class="h-8 w-24 bg-white/40 rounded"></div>
                            </div>
                            <div class="h-32 bg-white/5 rounded-2xl border border-white/5 p-4">
                                <div class="h-4 w-16 bg-white/20 rounded mb-4"></div>
                                <div class="h-8 w-24 bg-white/40 rounded"></div>
                            </div>
                            <div class="h-32 bg-white/5 rounded-2xl border border-white/5 p-4">
                                <div class="h-4 w-16 bg-white/20 rounded mb-4"></div>
                                <div class="h-8 w-24 bg-[#F37335] rounded"></div>
                            </div>
                        </div>
                        <div class="h-64 bg-white/5 rounded-2xl border border-white/5"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-20 text-center text-blue-100/30">
        &copy; 2026 Transit Plus. Conçu pour les leaders de la logistique.
    </footer>
</body>
</html>
