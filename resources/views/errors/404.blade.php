<!DOCTYPE html>
<html lang="fr" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable — MMI'e</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-['Plus_Jakarta_Sans',sans-serif]">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6">
        <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.08)] w-full max-w-sm p-10 text-center border border-slate-100">

            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-slate-100 shadow-sm relative overflow-hidden">
                <svg class="w-8 h-8 text-slate-400 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-900 mb-2 tracking-tight">Page introuvable</h1>

            <p class="text-slate-500 text-sm font-medium leading-relaxed">
                Le lien que vous avez suivi est peut-être rompu, ou la page a été retirée.
            </p>

            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col items-center gap-4">
                <a href="/" class="text-sm font-bold text-[#003189] hover:text-[#002266] transition-colors">
                    Retour à l'accueil
                </a>
                <p class="text-slate-400 text-[0.7rem] font-bold tracking-widest uppercase">
                    MMI'e
                </p>
            </div>

        </div>
    </div>
</body>
</html>
