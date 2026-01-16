<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transit Plus - SaaS Logistique & Douane Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .hero-gradient {
            background: radial-gradient(circle at top right, #0A4D68 0%, #051923 100%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-dark {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-[#051923] text-white selection:bg-orange-500 selection:text-white">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-dark">
        <div class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3 group cursor-pointer">
                <div class="w-10 h-10 bg-gradient-to-tr from-orange-600 to-orange-400 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:rotate-6 transition">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-xl font-black tracking-tighter uppercase">Transit <span class="text-orange-500">Plus</span></span>
            </div>
            
            <div class="hidden md:flex items-center space-x-8 text-sm font-bold uppercase tracking-widest text-gray-400">
                <a href="#features" class="hover:text-orange-500 transition">Modules</a>
                <a href="#about" class="hover:text-orange-500 transition">À Propos</a>
                <a href="#contact" class="hover:text-orange-500 transition">Contact</a>
            </div>

            <div class="flex items-center space-x-4">
                <a href="/admin/login" class="text-sm font-bold hover:text-orange-500 transition">CONNEXION</a>
                <a href="#contact" class="bg-orange-600 hover:bg-orange-500 px-6 py-2 rounded-full text-sm font-bold transition shadow-lg shadow-orange-600/20">DEMANDER UN DEVIS</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-40 pb-20 overflow-hidden hero-gradient">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-orange-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-600/10 rounded-full blur-[100px]"></div>

        <div class="container mx-auto px-6 text-center relative z-10">
            <span class="inline-block px-4 py-1.5 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-500 text-xs font-black tracking-[0.2em] mb-6 uppercase">
                Intelligence Logistique • SaaS Edition
            </span>
            <h1 class="text-6xl md:text-8xl font-black mb-8 leading-[0.9] tracking-tighter">
                L'ÈRE DU TRANSIT <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-orange-300">INTELLIGENT.</span>
            </h1>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto mb-12 font-light leading-relaxed">
                Optimisez vos opérations douanières, gérez vos entrepôts MAD et suivez vos flux financiers avec une interface pensée pour la performance.
            </p>
            <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                <a href="/admin/login" class="w-full md:w-auto bg-white text-black px-10 py-4 rounded-2xl font-black text-lg hover:bg-orange-500 hover:text-white transition transform hover:-translate-y-1 shadow-2xl">
                    TESTER LA DÉMO
                </a>
                <a href="#features" class="w-full md:w-auto glass px-10 py-4 rounded-2xl font-black text-lg hover:bg-white/5 transition flex items-center justify-center gap-2">
                    DÉCOUVRIR LES MODULES
                </a>
            </div>

            <!-- Preview Dashboard Real Screenshot -->
            <div class="mt-24 max-w-6xl mx-auto relative group">
                <div class="absolute -top-20 -left-20 w-64 h-64 bg-orange-600/20 rounded-full blur-[100px] animate-pulse"></div>
                <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-blue-600/20 rounded-full blur-[100px] animate-pulse"></div>
                
                <div class="glass p-3 rounded-[40px] shadow-2xl relative z-10 transition duration-700 group-hover:scale-[1.01]">
                    <div class="rounded-[30px] border border-white/40 overflow-hidden shadow-inner flex">
                        <img src="{{ asset('images/dashboard-preview.png') }}" alt="Transit Plus Dashboard Preview" class="w-full h-auto object-cover">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-32 bg-[#051923]">
        <div class="container mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-black mb-4 tracking-tight uppercase">Modules Expertises</h2>
                <div class="w-24 h-1.5 bg-orange-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Transit -->
                <div class="glass p-10 rounded-[40px] hover:border-orange-500/50 transition duration-500 border border-white/5">
                    <div class="w-16 h-16 bg-orange-600/20 rounded-2xl flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">TRANSIT & DOUANE</h3>
                    <p class="text-gray-400 leading-relaxed">Gestion complète des dossiers, synchronisation Sydonia World, et suivi des déclarations en temps réel.</p>
                </div>

                <!-- Commerce -->
                <div class="glass p-10 rounded-[40px] hover:border-orange-500/50 transition duration-500 border border-white/5">
                    <div class="w-16 h-16 bg-orange-600/20 rounded-2xl flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">COMMERCE & STOCK</h3>
                    <p class="text-gray-400 leading-relaxed">Facturation intelligente, gestion des inventaires, et suivi des ventes pour distributeurs et importateurs.</p>
                </div>

                <!-- MAD -->
                <div class="glass p-10 rounded-[40px] hover:border-orange-500/50 transition duration-500 border border-white/5">
                    <div class="w-16 h-16 bg-orange-600/20 rounded-2xl flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">LOGISTIQUE MAD</h3>
                    <p class="text-gray-400 leading-relaxed">Pilotage des magasins et aires de dédouanement (MAD), gestion des entrées/sorties et colisage.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Info & Contact Section -->
    <section id="contact" class="py-32 bg-gray-950 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(#F37335_1px,transparent_1px)] [background-size:40px_40px]"></div>
        </div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-start">
                
                <!-- Left: Info -->
                <div class="space-y-12" id="about">
                    <div>
                        <span class="text-orange-500 font-black tracking-widest uppercase text-sm">NOUS TROUVER</span>
                        <h2 class="text-5xl font-black mt-4 tracking-tighter uppercase leading-[0.9]">NATTAAN <br><span class="text-orange-600">GROUP.</span></h2>
                    </div>

                    <div class="space-y-8">
                        <div class="flex items-start gap-6">
                            <div class="w-12 h-12 glass rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xl uppercase tracking-wider">Siège Social</h4>
                                <p class="text-gray-400 text-lg">Agoe Minamadou, Lomé, Togo</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="w-12 h-12 glass rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xl uppercase tracking-wider">Ligne Directe</h4>
                                <p class="text-gray-400 text-lg">+228 90 35 51 62</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="w-12 h-12 glass rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-xl uppercase tracking-wider">Email</h4>
                                <p class="text-gray-400 text-lg">contact@nataangroup.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="glass p-12 rounded-[50px] border border-white/5 relative shadow-2xl shadow-orange-500/5">
                    <h3 class="text-3xl font-black mb-8 uppercase tracking-widest leading-none">DEMANDER <br><span class="text-orange-500">UN DEVIS.</span></h3>
                    
                    <form action="#" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-500">Nom Complet</label>
                                <input type="text" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-orange-500 transition" placeholder="Jean Dupont">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-500">Entreprise</label>
                                <input type="text" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-orange-500 transition" placeholder="Sté Transit SARL">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-500">Email Professionnel</label>
                            <input type="email" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-orange-500 transition" placeholder="direction@entreprise.com">
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-500">Module Souhaité</label>
                            <select class="w-full bg-white border border-white/10 text-black rounded-2xl px-6 py-4 outline-none focus:border-orange-500 transition appearance-none">
                                <option>Transit & Douane Complete</option>
                                <option>Commerce & Distribution</option>
                                <option>Logistique MAD</option>
                                <option>Pack Full Entreprise</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-500">Détails du projet</label>
                            <textarea rows="4" class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-orange-500 transition" placeholder="Décrivez vos besoins..."></textarea>
                        </div>

                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 py-5 rounded-2xl font-black text-lg tracking-widest uppercase transition transform active:scale-95 shadow-xl shadow-orange-600/30">
                            ENVOYER MA DEMANDE
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 bg-gray-950 border-t border-white/5">
        <div class="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-white/5 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-sm font-black tracking-tighter uppercase tracking-[0.3em]">TRANSIT <span class="text-orange-500">PLUS</span></span>
            </div>
            
            <p class="text-sm text-gray-600 font-bold uppercase tracking-widest">
                © 2026 NATAAN GROUP • TOUS DROITS RÉSERVÉS
            </p>

            <div class="flex space-x-6">
                <a href="#" class="text-gray-600 hover:text-orange-500 transition"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                <a href="#" class="text-gray-600 hover:text-orange-500 transition"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg></a>
            </div>
        </div>
    </footer>

</body>
</html>
