<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 p-4">
            <div class="flex items-center gap-4">
                <div class="p-4 bg-orange-100 dark:bg-orange-900/30 rounded-2xl">
                    <x-heroicon-o-arrow-path class="w-8 h-8 text-orange-600" />
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">Synchronisation Sydonia World</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Importez vos fichiers SAD pour automatiser la création des dossiers.</p>
                </div>
            </div>
            
            <div class="flex flex-col items-end gap-2">
                <a href="{{ \App\Filament\Resources\DeclarationResource::getUrl('index') }}?import=1" 
                   class="inline-flex items-center justify-center px-6 py-3 font-bold text-white transition-all bg-orange-600 rounded-xl hover:bg-orange-700 shadow-lg shadow-orange-500/30">
                    <x-heroicon-m-cloud-arrow-up class="w-5 h-5 mr-2" />
                    Lancer l'importation XML
                </a>
                <span class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Propulsé par Transit Plus • Nataan Group</span>
            </div>
        </div>
    </x-filament-widgets::widget>
</x-filament::section>
