@extends('layouts.admin')
@section('title', 'Live Pulses | Admin Pro')

@section('content')
<div class="w-full space-y-6">
    
    {{-- =========================================
         HEADER: TITLE, FILTER & PDF BUTTON
    ========================================== --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-6 html2pdf__ignore">
        <div>
            <h2 class="text-3xl font-light tracking-wide text-[#eaeef2]">Live <span class="font-bold text-blue-400">Pulses</span></h2>
            <p class="text-[#eaeef2]/50 text-sm mt-1">Monitor workforce activity and export reports.</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Data Filter Form (Weekly / Monthly) --}}
            <form method="GET" action="{{ route('admin.live.screenshots') }}" class="flex items-center gap-2">
                <select name="period" class="bg-[#0a1225] text-[#eaeef2] border border-blue-500/20 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-400 shadow-inner">
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>This Week</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>This Month</option>
                </select>
                <button type="submit" class="bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all border border-blue-500/20">
                    <i class="fas fa-filter"></i>
                </button>
            </form>

            {{-- 🚀 EXPORT TO PDF BUTTON --}}
            <button onclick="exportToPDF()" class="bg-[#d2a63d] text-[#010717] hover:bg-[#b88e2c] px-5 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-[0_0_15px_rgba(210,166,61,0.3)] hover:scale-105">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>

    {{-- =========================================
         PDF EXPORT CONTAINER
    ========================================== --}}
    <div id="pdf-content" class="w-full bg-[#010717] rounded-xl p-2">
        
        {{-- Hidden Header (Sirf PDF mein print hoga) --}}
        <div class="hidden pdf-header mb-8 pb-4 border-b border-gray-300 text-center text-black">
            <h1 class="text-3xl font-black uppercase tracking-widest">{{ auth()->user()->company->name ?? 'Company' }} Pulse Report</h1>
            <p class="text-sm mt-2 font-medium">Data Period: <span class="uppercase text-blue-600">{{ request('period', 'today') }}</span></p>
            <p class="text-xs text-gray-500">Generated on: {{ now()->format('d M Y, h:i A') }}</p>
        </div>

        {{-- Screenshots Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($shots as $shot)
                <div class="bg-[#0a1225] border border-blue-500/20 rounded-2xl overflow-hidden shadow-xl group shot-card break-inside-avoid">
                    {{-- Image Container --}}
                    <div class="relative h-48 w-full bg-black/50 overflow-hidden">
                        <img src="{{ asset('storage/' . $shot->image_path) }}" alt="Screenshot" crossorigin="anonymous" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-all duration-500">
                        
                        {{-- Time Badge --}}
                        <div class="absolute top-3 right-3 bg-[#010717]/80 backdrop-blur border border-[#eaeef2]/10 px-3 py-1 rounded-lg text-[10px] font-black tracking-widest text-[#eaeef2]">
                            {{ $shot->created_at->format('d M, h:i A') }}
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center font-bold text-sm border border-blue-500/20">
                                    {{ substr($shot->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#eaeef2] shot-user-name">{{ $shot->user->name ?? 'Unknown User' }}</h3>
                                    <p class="text-[10px] text-[#eaeef2]/50 tracking-widest uppercase">{{ $shot->user->department->name ?? 'Staff' }}</p>
                                </div>
                            </div>
                            
                            {{-- Delete Form (PDF mein ignore hoga) --}}
                            <form action="{{ route('admin.screenshots.destroy', $shot->id) }}" method="POST" class="html2pdf__ignore" onsubmit="return confirm('Delete this screenshot?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400/50 hover:text-red-400 transition-colors" title="Delete Screenshot">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                        <div class="mt-4 p-3 bg-[#010717] rounded-xl border border-[#eaeef2]/5">
                            <p class="text-xs text-[#eaeef2]/70 font-mono truncate shot-window" title="{{ $shot->window_title }}"><i class="fas fa-window-maximize text-blue-400/50 mr-2"></i>{{ $shot->window_title ?? 'Unknown Window' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center glass-effect rounded-3xl border border-blue-500/10">
                    <i class="fas fa-camera-slash text-4xl text-blue-500/30 mb-4 block"></i>
                    <h3 class="text-lg font-bold text-[#eaeef2]">No Pulses Found</h3>
                    <p class="text-sm text-[#eaeef2]/50">No data available for the selected period.</p>
                </div>
            @endforelse
        </div>
    </div>
    
    {{-- Pagination (PDF mein ignore hogi) --}}
    <div class="mt-6 html2pdf__ignore">
        {{ $shots->withQueryString()->links() }}
    </div>
</div>

{{-- =========================================
     PDF EXPORT SCRIPT (html2pdf.js)
========================================== --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function exportToPDF() {
        const element = document.getElementById('pdf-content');
        const header = element.querySelector('.pdf-header');
        
        // PDF Generate hone se pehle header show karein aur colors ko PDF-friendly banayein
        header.classList.remove('hidden');
        header.classList.add('block');
        
        // Dark theme cards ko light banayein sirf PDF ke liye taake ink kam lagay
        const cards = element.querySelectorAll('.shot-card');
        cards.forEach(card => {
            card.classList.replace('bg-[#0a1225]', 'bg-white');
            card.classList.replace('border-blue-500/20', 'border-gray-200');
        });

        const userNames = element.querySelectorAll('.shot-user-name');
        userNames.forEach(name => name.classList.replace('text-[#eaeef2]', 'text-black'));

        const windowTitles = element.querySelectorAll('.shot-window');
        windowTitles.forEach(title => title.classList.replace('text-[#eaeef2]/70', 'text-gray-700'));

        const opt = {
            margin:       [0.5, 0.5, 0.5, 0.5],
            filename:     'Pulse_Report_{{ date("Y_m_d") }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
        };

        // PDF download karne ka function
        html2pdf().set(opt).from(element).save().then(() => {
            // Download hone ke baad wapis original Dark Theme restore kar dein
            header.classList.remove('block');
            header.classList.add('hidden');
            
            cards.forEach(card => {
                card.classList.replace('bg-white', 'bg-[#0a1225]');
                card.classList.replace('border-gray-200', 'border-blue-500/20');
            });

            userNames.forEach(name => name.classList.replace('text-black', 'text-[#eaeef2]'));
            windowTitles.forEach(title => title.classList.replace('text-gray-700', 'text-[#eaeef2]/70'));
        });
    }
</script>
@endsection