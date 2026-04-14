<div class="space-y-8">
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-emerald-100 overflow-hidden">
        <div class="p-6 bg-emerald-900 flex justify-between items-center">
            <h3 class="text-xs font-black uppercase italic tracking-widest text-emerald-400">Technical Review Terminal</h3>
            <span class="px-3 py-1 bg-emerald-800 rounded-full text-[8px] font-black text-white uppercase italic">Commander Mode</span>
        </div>

        <form action="{{ route('admin.feedback.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $task->user_id }}">
            <input type="hidden" name="task_id" value="{{ $task->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 mb-2 block">Performance Score (1-10)</label>
                    <input type="number" name="rating_score" min="1" max="10" required 
                           class="w-full bg-emerald-50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-emerald-500 outline-none text-emerald-900">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2 mb-2 block">Efficiency Status</label>
                    <select name="status" class="w-full bg-emerald-50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-emerald-500 outline-none text-emerald-900">
                        <option value="excellent">Excellent Execution</option>
                        <option value="good">Standard Met</option>
                        <option value="needs_improvement">Needs Optimization</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2 mb-2 block">Technical Review Note</label>
                <textarea name="review_note" rows="3" required
                          class="w-full bg-emerald-50 border-none rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-emerald-500 outline-none text-emerald-900"
                          placeholder="Log technical analysis..."></textarea>
            </div>

            <button type="submit" class="w-full bg-emerald-900 hover:bg-black text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-[0.2em] transition-all shadow-lg active:scale-95">
                Transmit Official Review
            </button>
        </form>
    </div>

    <div class="space-y-4">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-6">Operational Log (Employee Updates)</p>
        @foreach($task->employeeUpdates as $update)
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex gap-4">
            <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center font-black text-white text-xs italic">{{ substr($update->user->name, 0, 1) }}</div>
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <h4 class="text-xs font-black uppercase italic text-slate-800">{{ $update->user->name }}</h4>
                    <span class="text-[8px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">{{ $update->completion_percentage }}% COMPLETE</span>
                </div>
                <p class="text-xs text-slate-600 mt-2 font-medium">{{ $update->update_note }}</p>
                @if($update->blockers)
                <div class="mt-2 text-[8px] font-bold text-rose-500 uppercase italic">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Blocker: {{ $update->blockers }}
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>