<div class="flex flex-col gap-4">
    @php
        $file = $getRecord()->scanned_file;
        $extension = $file ? pathinfo($file, PATHINFO_EXTENSION) : null;
        $isPdf = strtolower($extension) === 'pdf';
        $url = asset('storage/' . $file);
    @endphp

    @if($file)
        @if($isPdf)
            <div class="w-full h-[600px] border rounded-lg shadow-sm overflow-hidden bg-gray-100">
                <iframe src="{{ $url }}" class="w-full h-full" frameborder="0"></iframe>
            </div>
        @else
            <div class="w-full border rounded-lg shadow-sm overflow-hidden bg-gray-100 p-2 flex justify-center">
                <img src="{{ $url }}" alt="Scan preview" class="max-w-full h-auto rounded shadow-sm">
            </div>
        @endif
    @else
        <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed rounded-lg bg-gray-50 text-gray-400">
            <x-heroicon-o-document-minus class="w-12 h-12 mb-2" />
            <p>Aucun document numérisé pour ce courrier.</p>
        </div>
    @endif
</div>
