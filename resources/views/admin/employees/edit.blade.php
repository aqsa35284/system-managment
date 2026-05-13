@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-slate-800 mb-2">Edit Team Member</h1>
            <p class="text-slate-500">Update profile details and system access for {{ $employee->name }}</p>
        </div>

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-user-edit mr-3"></i>
                    Employee Information
                </h2>
            </div>

            {{-- ⭐ ADVANCED: onsubmit loader & PUT Method preserved --}}
            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data" class="p-8" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Updating...';">
                @csrf
                @method('PUT')
                
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-id-card mr-2 text-blue-600"></i>
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $employee->name) }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. Ali Ahmed" required>
                            @error('name') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="ali@office.com" required>
                            @error('email') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">Phone Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. 03001234567">
                            @error('phone') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                            <input type="text" id="username" name="username" value="{{ old('username', $employee->username) }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. ali_dev">
                            @error('username') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                        Professional Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="designation" class="block text-sm font-medium text-slate-700 mb-2">Designation</label>
                            <input type="text" id="designation" name="designation" value="{{ old('designation', $employee->designation) }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. Laravel Developer" required>
                            @error('designation') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-slate-700 mb-2">Department</label>
                            <select id="department_id" name="department_id" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="salary" class="block text-sm font-medium text-slate-700 mb-2">Monthly Salary (PKR)</label>
                            <input type="number" id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. 75000" required>
                            @error('salary') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="join_date" class="block text-sm font-medium text-slate-700 mb-2">Joining Date</label>
                            <input type="date" id="join_date" name="join_date" value="{{ old('join_date', $employee->join_date) }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900">
                            @error('join_date') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-blue-600"></i>
                        System Access
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Access Role</label>
                            <select id="role" name="role" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" required>
                                <option value="employee" {{ old('role', $employee->role) == 'employee' ? 'selected' : '' }}>Employee (Standard Access)</option>
                                <option value="admin" {{ old('role', $employee->role) == 'admin' ? 'selected' : '' }}>Admin (Full Control)</option>
                            </select>
                            @error('role') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password <span class="text-xs text-slate-400 font-normal">(Leave blank to keep current)</span></label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="••••••••">
                            @error('password') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-camera mr-2 text-blue-600"></i>
                        Profile Image
                    </h3>
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            @if($employee->profile_image)
                                <img id="preview" class="h-24 w-24 object-cover rounded-full border-2 border-blue-100" src="{{ asset('storage/'.$employee->profile_image) }}" alt="Profile preview">
                            @else
                                <img id="preview" class="h-24 w-24 object-cover rounded-full border-2 border-blue-100 bg-slate-200" src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=2563eb&color=fff" alt="Profile preview">
                            @endif
                        </div>
                        <div class="block w-full">
                            <label class="block">
                                <span class="sr-only">Choose profile photo</span>
                                <input type="file" name="profile_image" id="profile_image" class="block w-full text-sm text-slate-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100
                                    cursor-pointer">
                            </label>
                            @error('profile_image') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-6">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:-translate-y-0.5 flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
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