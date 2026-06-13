<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISI GESTION - Système d'Information Académique</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen font-sans selection:bg-emerald-500 selection:text-gray-900">

    <div id="app" v-cloak class="flex flex-col min-h-screen justify-between">
        
        <header class="max-w-7xl w-full mx-auto px-6 py-6 flex justify-between items-center border-b border-gray-800/60">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
                <span class="text-xl font-black tracking-wider text-white">ISI <span class="text-emerald-400">GESTION</span></span>
            </div>

            <div>
                <a href="/login" class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-white border border-gray-700 font-semibold text-sm px-5 py-2.5 rounded-xl transition-all shadow-md">
                    Espace Connexion
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l4-4m0 0l-4-4m4 4H3m13-4V7a3 3 0 00-3-3H6a3 3 0 00-3 3v10a3 3 0 003 3h7a3 3 0 003-3v-4" />
                    </svg>
                </a>
            </div>
        </header>

        <div class="space-y-24 py-16">
            
            <main class="max-w-4xl w-full mx-auto px-6 text-center flex flex-col items-center">
                <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase tracking-widest mb-6">
                    Système d'Information Académique Intégré
                </span>
                
                <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-tight max-w-3xl">
                    Pilotez votre établissement en toute <span class="bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">simplicité</span>.
                </h1>
                
                <p class="mt-6 text-base sm:text-lg text-gray-400 max-w-2xl leading-relaxed">
                    Une plateforme web robuste et cloisonnée conçue pour interconnecter l'administration, le secrétariat, la comptabilité et le suivi transparent des parents d'élèves.
                </p>

                <div class="mt-8">
                    <a href="/login" class="px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-gray-900 font-bold rounded-xl transition-all shadow-xl shadow-emerald-500/10 transform hover:-translate-y-0.5 inline-block">
                        Accéder aux Tableaux de Bord
                    </a>
                </div>

                <div class="mt-16 w-full grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6">
                    <div class="bg-gray-800/30 p-6 rounded-2xl border border-gray-800/80 backdrop-blur-sm text-center">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Effectif Global</p>
                        <div class="flex items-baseline justify-center gap-1 mt-2">
                            <p class="text-4xl font-black text-white tracking-tight">@{{ totalEleves }}</p>
                            <span class="text-xs font-bold text-emerald-400">Élèves</span>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">Inscrits pour l'année en cours</p>
                    </div>

                    <div class="bg-gray-800/30 p-6 rounded-2xl border border-gray-800/80 backdrop-blur-sm text-center">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Filières & Classes</p>
                        <div class="flex items-baseline justify-center gap-1 mt-2">
                            <p class="text-4xl font-black text-indigo-400 tracking-tight">@{{ totalClasses }}</p>
                            <span class="text-xs font-bold text-indigo-300">Actives</span>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">Structures pédagogiques</p>
                    </div>

                    <div class="bg-gray-800/30 p-6 rounded-2xl border border-gray-800/80 backdrop-blur-sm text-center">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Suivi Financier</p>
                        <div class="flex items-baseline justify-center gap-1 mt-2">
                            <p class="text-4xl font-black text-teal-400 tracking-tight">100%</p>
                            <span class="text-xs font-bold text-teal-300">Transparent</span>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">Visibilité parentale instantanée</p>
                    </div>
                </div>
            </main>

            <section class="max-w-7xl w-full mx-auto px-6">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white">Une plateforme, quatre espaces métiers</h2>
                    <p class="text-sm text-gray-400 mt-2">Chaque utilisateur dispose d'un environnement hermétique adapté à ses prérogatives quotidiennes.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gray-800/40 p-6 rounded-2xl border border-gray-800 flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center mb-4 border border-purple-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-white">Super Administration</h3>
                            <p class="text-xs text-gray-400 mt-2 leading-relaxed">Configuration de l'ossature de l'école : création des classes, assignation des grilles tarifaires et provisionnement sécurisé des comptes du personnel.</p>
                        </div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-purple-400 mt-4 block">Espace Pilotage</span>
                    </div>

                    <div class="bg-gray-800/40 p-6 rounded-2xl border border-gray-800 flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center mb-4 border border-blue-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-white">Scolarité & Inscriptions</h3>
                            <p class="text-xs text-gray-400 mt-2 leading-relaxed">Prise en charge des nouvelles inscriptions, orientation des élèves dans les vagues d'étude et diffusion instantanée des communiqués officiels sur le fil d'actualité.</p>
                        </div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-blue-400 mt-4 block">Espace Administratif</span>
                    </div>

                    <div class="bg-gray-800/40 p-6 rounded-2xl border border-gray-800 flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-4 border border-emerald-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 11h.01M12 7h.01M15 10h.01M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-white">Gestion Comptable</h3>
                            <p class="text-xs text-gray-400 mt-2 leading-relaxed">Enregistrement rigoureux des encaissements de mensualités, traçabilité des dépenses opérationnelles et filtrage multicritères du grand livre de caisse.</p>
                        </div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-400 mt-4 block">Espace Trésorerie</span>
                    </div>

                    <div class="bg-gray-800/40 p-6 rounded-2xl border border-gray-800 flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center mb-4 border border-amber-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-white">Portail Parent</h3>
                            <p class="text-xs text-gray-400 mt-2 leading-relaxed">Suivi transparent de la fratrie. Visualisation claire des montants versés, solde restant dû et indicateurs colorés (payé / impayé) pour chaque mensualité.</p>
                        </div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-amber-400 mt-4 block">Espace Consultation</span>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl w-full mx-auto px-6 border-t border-gray-800/60 pt-16">
                <div class="bg-gradient-to-r from-gray-800/40 to-gray-800/10 p-8 rounded-3xl border border-gray-800 flex flex-col lg:flex-row gap-8 items-center justify-between">
                    <div class="max-w-xl">
                        <span class="text-[10px] font-bold bg-indigo-500/10 text-indigo-400 px-3 py-1 rounded-full border border-indigo-500/20 uppercase tracking-wider">Garanties du Système</span>
                        <h3 class="text-xl sm:text-2xl font-bold text-white mt-3">Cloisonnement strict et sécurité des données</h3>
                        <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                            Développée sur une architecture découplée, l'application s'appuie sur des politiques d'authentification par jetons sécurisés (**Laravel Sanctum**) et des filtres d'accès applicatifs (**Middlewares**). Aucune passerelle n'est permise entre la saisie comptable et la consultation parentale, assurant l'intégrité absolue des flux financiers de l'établissement.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3 max-w-sm justify-center lg:justify-end">
                        <span class="bg-gray-900 border border-gray-700 text-xs px-4 py-2 rounded-xl font-mono text-gray-300">Laravel REST API</span>
                        <span class="bg-gray-900 border border-gray-700 text-xs px-4 py-2 rounded-xl font-mono text-gray-300">Vue.js SPA</span>
                        <span class="bg-gray-900 border border-gray-700 text-xs px-4 py-2 rounded-xl font-mono text-gray-300">Role-Based Protection</span>
                        <span class="bg-gray-900 border border-gray-700 text-xs px-4 py-2 rounded-xl font-mono text-gray-300">Tailwind UX</span>
                    </div>
                </div>
            </section>

        </div>

        <footer class="border-t border-gray-800 bg-gray-950/40 py-6 mt-12">
            <div class="max-w-7xl w-full mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-500 font-medium">
                <p>© 2026 ISI GESTION. Tous droits réservés.</p>
                <p>Mémoire de fin d'études réalisé pour la validation de la <span class="text-gray-400 font-bold">Licence 3</span></p>
            </div>
        </footer>
    </div>

    <script>
        const { createApp, ref, onMounted } = Vue;

        createApp({
            setup() {
                const totalEleves = ref(0);
                const totalClasses = ref(0);

                const chargerDonneesAccueil = async () => {
                    try {
                        const response = await axios.get('/api/v1/dashboard/stats'); 
                        if (response.data && response.data.kpis) {
                            totalEleves.value = response.data.kpis.totalEtudiants || 0;
                            totalClasses.value = response.data.kpis.totalClasses || 0;
                        }
                    } catch (err) {
                        // Données par défaut si pas connecté ou route non publique
                        totalEleves.value = 142; 
                        totalClasses.value = 12;
                    }
                };

                onMounted(() => {
                    chargerDonneesAccueil();
                });

                return {
                    totalEleves,
                    totalClasses
                };
            }
        }).mount('#app');
    </script>

    <style>
        [v-cloak] { display: none; }
    </style>
</body>
</html>