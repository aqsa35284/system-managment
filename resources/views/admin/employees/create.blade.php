@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-slate-800 mb-2">Add New Team Member</h1>
            <p class="text-slate-500">Create a new employee profile and grant system access</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-user-plus mr-3"></i>
                    Employee Information
                </h2>
            </div>

            <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                
                <!-- Personal Information Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-id-card mr-2 text-blue-600"></i>
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. Ali Ahmed" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="ali@office.com" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. 03001234567">
                        </div>
                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                            <input type="text" id="username" name="username" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. ali_dev">
                        </div>
                    </div>
                </div>

                <!-- Professional Information Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                        Professional Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="designation" class="block text-sm font-medium text-slate-700 mb-2">Designation</label>
                            <input type="text" id="designation" name="designation" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. Laravel Developer" required>
                        </div>
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-slate-700 mb-2">Department</label>
                            <select id="department_id" name="department_id" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="salary" class="block text-sm font-medium text-slate-700 mb-2">Monthly Salary (PKR)</label>
                            <input type="number" id="salary" name="salary" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="e.g. 75000" required>
                        </div>
                        <div>
                            <label for="join_date" class="block text-sm font-medium text-slate-700 mb-2">Joining Date</label>
                            <input type="date" id="join_date" name="join_date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900">
                        </div>
                    </div>
                </div>

                <!-- System Access Section -->
                <div class="mb-10">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-shield-alt mr-2 text-blue-600"></i>
                        System Access
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="role" class="block text-sm font-medium text-slate-700 mb-2">Access Role</label>
                            <select id="role" name="role" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" required>
                                <option value="employee" selected>Employee (Standard Access)</option>
                                <option value="admin">Admin (Full Control)</option>
                            </select>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-slate-900" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>

                <!-- Profile Image Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center">
                        <i class="fas fa-camera mr-2 text-blue-600"></i>
                        Profile Image
                    </h3>
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            <img id="preview" class="h-24 w-24 object-cover rounded-full bg-slate-200" src="https://picsum.photos/seed/employee/200/200.jpg" alt="Profile preview">
                        </div>
                        <label class="block">
                            <span class="sr-only">Choose profile photo</span>
                            <input type="file" name="profile_image" id="profile_image" class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                cursor-pointer" required>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-check mr-2"></i>
                        Create Employee Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Profile image preview
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