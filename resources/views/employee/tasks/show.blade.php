<div class="space-y-8">
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 overflow-hidden">
        <div class="p-6 bg-slate-900 flex justify-between items-center">
            <h3 class="text-xs font-black uppercase italic tracking-widest text-slate-400">Task Progress Update</h3>
            <span class="px-3 py-1 bg-slate-800 rounded-full text-[8px] font-black text-white uppercase italic">Fleet Member Mode</span>
        </div>

        <form action="{{ route('employee.feedback.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <input type="hidden" name="task_id" value="{{ $task->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 mb-2 block">Completion %</label>
                    <input type="number" name="completion_percentage" min="0" max="100" required 
                           class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-slate-500 outline-none text-slate-900" placeholder="0-100">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 mb-2 block">Active Blockers</label>
                    <input type="text" name="blockers" 
                           class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-slate-500 outline-none text-slate-900" placeholder="e.g. Bug in API">
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2 mb-2 block">Operational Update Note</label>
                <textarea name="update_note" rows="3" required
                          class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-slate-500 outline-none text-slate-900"
                          placeholder="What did you achieve?"></textarea>
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all shadow-lg active:scale-95">
                Transmit Status Update
            </button>
        </form>
    </div>

    <div class="space-y-4">
        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.3em] ml-6">Commander's Evaluation (Admin Reviews)</p>
        @foreach($task->adminFeedbacks as $review)
        <div class="bg-emerald-900 p-6 rounded-[2rem] shadow-xl border border-emerald-800 flex gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-700 flex items-center justify-center font-black text-emerald-400 text-xs italic">A</div>
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <h4 class="text-xs font-black uppercase italic text-white">Technical Review</h4>
                    <span class="text-[8px] font-black text-emerald-900 bg-emerald-400 px-2 py-1 rounded-full uppercase tracking-widest">Score: {{ $review->rating_score }}/10</span>
                </div>
                <p class="text-xs text-emerald-100 mt-2 font-medium leading-relaxed">{{ $review->review_note }}</p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-[7px] font-black uppercase text-emerald-400 border border-emerald-400/30 px-2 py-0.5 rounded italic">{{ $review->status }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>