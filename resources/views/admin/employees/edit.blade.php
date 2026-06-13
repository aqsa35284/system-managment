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
            <h1 class="text-4xl font-bold text-[#eaeef2] mb-2">Edit Team Member</h1>
            <p class="text-[#eaeef2]/40">Update profile details and system access for <span class="text-[#d2a63d]">{{ $employee->name }}</span></p>
        </div>

        <div class="shadow-xl rounded-2xl overflow-hidden border border-[#d2a63d]/15" style="background: #010717;">
            <div class="px-8 py-6 border-b border-[#d2a63d]/15" style="background: linear-gradient(135deg, #0a1225 0%, #010717 100%);">
                <h2 class="text-2xl font-bold text-[#eaeef2] flex items-center">
                    <i class="fas fa-user-edit mr-3 text-[#d2a63d]"></i>
                    Employee Information
                </h2>
            </div>

            {{-- ⭐ GLOBAL ERROR DISPLAY (Yeh aapko batayega form kyu redirect nahi ho raha) --}}
            @if ($errors->any())
                <div class="mx-8 mt-6 bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl shadow-lg transition-all duration-500">
                    <h4 class="font-bold mb-2 flex items-center text-lg">
                        <i class="fa-solid fa-triangle-exclamation mr-2"></i> Form Submission Failed!
                    </h4>
                    <ul class="list-disc list-inside text-sm ml-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ⭐ SECURITY ERROR ALERT --}}
            @if(session('error'))
                <div id="flash-error" class="mx-8 mt-6 bg-red-500/10 border border-red-500/30 text-red-400 px-6 py-4 rounded-xl flex justify-between items-center shadow-lg transition-all duration-500">
                    <div class="flex items-center">
                        <i class="fa-solid fa-triangle-exclamation text-xl mr-3"></i>
                        <span class="font-bold">{{ session('error') }}</span>
                    </div>
                    <button onclick="document.getElementById('flash-error').style.display='none'" class="text-red-400/50 hover:text-red-400 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="p-8" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Updating...';">
                @csrf
                @method('PUT')
                
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/10 flex items-center">
                        <i class="fas fa-id-card mr-2 text-[#d2a63d]"></i>
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $employee->name) }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. Ali Ahmed" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="ali@office.com" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. 03001234567">
                        </div>
                        <div>
                            <label for="username" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Username</label>
                            <input type="text" id="username" name="username" value="{{ old('username', $employee->username) }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. ali_dev">
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/10 flex items-center">
                        <i class="fas fa-briefcase mr-2 text-[#d2a63d]"></i>
                        Professional Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="designation" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Designation</label>
                            <input type="text" id="designation" name="designation" value="{{ old('designation', $employee->designation) }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. Laravel Developer" required>
                        </div>
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Department</label>
                            <select id="department_id" name="department_id" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] bg-[#0a1225]" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="salary" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Monthly Salary (PKR)</label>
                            <input type="number" id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="e.g. 75000" required>
                        </div>
                        <div>
                            <label for="join_date" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Joining Date</label>
                            <input type="date" id="join_date" name="join_date" value="{{ old('join_date', $employee->join_date) }}" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] bg-[#0a1225]" style="color-scheme: dark;">
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/10 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-[#d2a63d]"></i>
                        System Access
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Access Role</label>
                            <select id="role" name="role" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] bg-[#0a1225]" required>
                                <option value="employee" {{ old('role', $employee->role) == 'employee' ? 'selected' : '' }}>Employee (Standard Access)</option>
                                <option value="admin" {{ old('role', $employee->role) == 'admin' ? 'selected' : '' }}>Admin (Full Control)</option>
                            </select>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-[#eaeef2]/70 mb-2">Password <span class="text-[10px] text-[#eaeef2]/30 font-normal uppercase tracking-wider ml-1">(Leave blank to keep current)</span></label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-[#d2a63d]/20 rounded-lg focus:ring-2 focus:ring-[#d2a63d]/30 focus:border-[#d2a63d]/50 outline-none transition text-[#eaeef2] placeholder-[#eaeef2]/20 bg-[#0a1225]" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-[#eaeef2] mb-4 pb-2 border-b border-[#d2a63d]/10 flex items-center">
                        <i class="fas fa-camera mr-2 text-[#d2a63d]"></i>
                        Profile Image
                    </h3>
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            @if($employee->profile_image)
                                <img id="preview" class="h-24 w-24 object-cover rounded-full border-2 border-[#d2a63d]/30 bg-[#0a1225]" src="{{ asset('storage/'.$employee->profile_image) }}" alt="Profile preview">
                            @else
                                <img id="preview" class="h-24 w-24 object-cover rounded-full border-2 border-[#d2a63d]/30 bg-[#0a1225]" src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=0a1225&color=d2a63d" alt="Profile preview">
                            @endif
                        </div>
                        <div class="block w-full">
                            <label class="block">
                                <span class="sr-only">Choose profile photo</span>
                                <input type="file" name="profile_image" id="profile_image" accept="image/png, image/jpeg, image/jpg" class="block w-full text-sm text-[#eaeef2]/40
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-[#d2a63d]/10 file:text-[#d2a63d]
                                    hover:file:bg-[#d2a63d] hover:file:text-[#010717]
                                    cursor-pointer transition-all">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-[#d2a63d]/10 pt-6">
                    <button type="submit" class="px-8 py-3 bg-[#d2a63d] hover:bg-[#d2a63d]/80 text-[#010717] font-bold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#d2a63d]/50 focus:ring-offset-2 focus:ring-offset-[#010717] transition-all transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    setTimeout(function() {
        let errorAlert = document.getElementById('flash-error');
        if(errorAlert) { 
            errorAlert.style.opacity = '0'; 
            setTimeout(() => errorAlert.style.display = 'none', 500); 
        }
    }, 5000);

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