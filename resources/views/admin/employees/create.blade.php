@extends('layouts.admin')

@section('content')

<link rel="icon" type="image/png" href="{{ asset('storage/logo/Favicon.png') }}">

<div class="min-h-screen bg-[#010717] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-10">
            {{-- ⭐ BACK BUTTON --}}
            <div class="flex justify-start mb-4">
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#d2a63d]/10 border border-[#d2a63d]/30 rounded-xl text-[#d2a63d] hover:bg-[#d2a63d] hover:text-[#010717] text-[10px] font-black uppercase tracking-widest transition-all">
                    <i class="fas fa-arrow-left text-xs"></i> Back
                </a>
            </div>
            <h1 class="text-4xl font-bold text-[#eaeef2] mb-2">Add New Team Member</h1>
            <p class="text-[#eaeef2]/40">Create a new employee profile and grant system access</p>
        </div>

        <div class="shadow-xl rounded-2xl overflow-hidden border border-[#d2a63d]/15" style="background: #010717;">
            <div class="px-8 py-6 border-b border-[#d2a63d]/15" style="background: linear-gradient(135deg, #0a1225 0%, #010717 100%);">
                <h2 class="text-2xl font-bold text-[#eaeef2] flex items-center">
                    <i class="fas fa-user-plus mr-3 text-[#d2a63d]"></i>
                    Employee Information
                </h2>
            </div>

            {{-- ⭐ ADVANCED: Added onsubmit loader to prevent double duplicate submissions --}}
            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data" class="p-8" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Processing...';">
                @csrf
                
                {{-- Personal Information --}}
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/10 flex items-center">
                        <i class="fas fa-id-card mr-2 text-[#d2a63d]"></i>
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. Ali Ahmed" required>
                            @error('name') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="ali@office.com" required>
                            @error('email') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. 03001234567">
                            @error('phone') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="username" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Username</label>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. ali_dev">
                            @error('username') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Professional Information --}}
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/10 flex items-center">
                        <i class="fas fa-briefcase mr-2 text-[#d2a63d]"></i>
                        Professional Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="designation" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Designation</label>
                            <input type="text" id="designation" name="designation" value="{{ old('designation') }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. Laravel Developer" required>
                            @error('designation') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Department</label>
                            <select id="department_id" name="department_id" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] bg-[#0a1225]" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="salary" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Monthly Salary (PKR)</label>
                            <input type="number" id="salary" name="salary" value="{{ old('salary') }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. 75000" required>
                            @error('salary') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="join_date" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Joining Date</label>
                            <input type="date" id="join_date" name="join_date" value="{{ old('join_date', date('Y-m-d')) }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] bg-[#0a1225]">
                            @error('join_date') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- System Access --}}
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/10 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-[#d2a63d]"></i>
                        System Access
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Access Role</label>
                            <select id="role" name="role" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] bg-[#0a1225]" required>
                                <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee (Standard Access)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Full Control)</option>
                            </select>
                            @error('role') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Password</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] bg-[#0a1225]" placeholder="••••••••" required>
                            @error('password') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Profile Image --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/10 flex items-center">
                        <i class="fas fa-camera mr-2 text-[#d2a63d]"></i>
                        Profile Image
                    </h3>
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            <img id="preview" class="h-24 w-24 object-cover rounded-full border-2 border-[#d2a63d]/30 bg-[#0a1225]" src="https://picsum.photos/seed/employee/200/200.jpg" alt="Profile preview">
                        </div>
                        <div class="block w-full">
                            <label class="block">
                                <span class="sr-only">Choose profile photo</span>
                                <input type="file" name="profile_image" id="profile_image" class="block w-full text-sm text-[#eaeef2]/40
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-[#d2a63d]/10 file:text-[#d2a63d]
                                    hover:file:bg-[#d2a63d] hover:file:text-[#010717]
                                    cursor-pointer transition-all" required>
                            </label>
                            @error('profile_image') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-[#d2a63d]/10 pt-6">
                    <button type="submit" class="px-8 py-3 bg-[#d2a63d] hover:bg-[#d2a63d]/80 text-[#010717] font-bold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#d2a63d]/50 focus:ring-offset-2 focus:ring-offset-[#010717] transition-all transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        Create Employee Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Profile image preview logic (Unchanged, completely intact)
    document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection