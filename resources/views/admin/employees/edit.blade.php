@extends('layouts.admin')

@section('content')
<div class="p-8 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden">
        <div class="p-10 bg-blue-600 text-white">
            <h2 class="text-3xl font-black uppercase italic tracking-tighter">Modify <span class="opacity-50">Identity</span></h2>
            <p class="text-[10px] font-bold uppercase tracking-[0.3em] mt-2 italic">Member: {{ $employee->name }}</p>
        </div>

        <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-6">
            @csrf 
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Full Name</label>
                    <input type="text" name="name" value="{{ $employee->name }}" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-blue-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Username</label>
    <input type="text" name="username" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-blue-500 outline-none" placeholder="e.g. ali_dev">
</div>
     <div class="space-y-2">
    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Phone Number</label>
    <input type="text" name="phone" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-blue-500 outline-none" placeholder="e.g. 03001234567">
</div>


                

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Official Email</label>
                    <input type="email" name="email" value="{{ $employee->email }}" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-blue-500 outline-none transition-all">
                </div>
                          <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Assign Node (Dept)</label>
                    <select name="department_id" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-blue-500 outline-none cursor-pointer">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $employee->department_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Designation</label>
                    <input type="text" name="designation" value="{{ $employee->designation }}" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-blue-500 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-blue-600 italic ml-2">System Access Role</label>
                    <select name="role" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-blue-500 outline-none cursor-pointer">
                        <option value="employee" {{ $employee->role == 'employee' ? 'selected' : '' }}>Employee</option>
                        <option value="admin" {{ $employee->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-emerald-500 italic ml-2">Monthly Salary (PKR)</label>
                    <input type="number" name="salary" value="{{ $employee->salary }}" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-emerald-500 outline-none transition-all">
                </div>

      

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Joining Date</label>
                    <input type="date" name="join_date" value="{{ $employee->join_date }}" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-blue-500 outline-none transition-all">
                </div>
           


                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" class="w-full p-4 bg-slate-50 rounded-2xl font-bold border-2 border-transparent focus:border-blue-500 outline-none transition-all" placeholder="••••••••">
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 italic ml-2">Update Identity Image</label>
                    <div class="flex items-center gap-6 p-4 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <div class="w-20 h-20 bg-blue-100 rounded-xl overflow-hidden shadow-inner">
                            @if($employee->profile_image)
                                <img src="{{ asset('storage/'.$employee->profile_image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full font-black text-blue-400 italic">?</div>
                            @endif
                        </div>
                        <input type="file" name="profile_image" class="text-[10px] font-bold text-slate-400 uppercase">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-6 rounded-3xl font-black uppercase italic shadow-2xl shadow-blue-100 hover:bg-blue-700 transition transform hover:-translate-y-1">
                Establish Updated Config <i class="fas fa-save ml-2"></i>
            </button>
        </form>
    </div>
</div>
@endsection