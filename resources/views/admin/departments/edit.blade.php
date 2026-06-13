@extends('layouts.admin')

@section('content')

 <link rel="icon" type="image/jpeg" href="{{ asset('storage/logo/Favicon.png') }}">
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8" style="background-color: #010717;">
    <div class="max-w-3xl mx-auto">

        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('admin.departments.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-lg font-medium transition-all border text-sm" style="border-color: #d2a63d; color: #d2a63d; background-color: transparent;" onmouseover="this.style.backgroundColor='#d2a63d'; this.style.color='#010717';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#d2a63d';">
                <i class="fas fa-arrow-left mr-2"></i> Back to Departments
            </a>
        </div>

        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold mb-2" style="color: #eaeef2;">Edit Department</h1>
            <p style="color: #eaeef2; opacity: 0.5;">Modify configuration for {{ $department->name }}</p>
        </div>

        <div class="shadow-xl rounded-2xl overflow-hidden border" style="background-color: #0d1b2e; border-color: #d2a63d22;">
            <div class="px-8 py-6" style="background: linear-gradient(135deg, #d2a63d, #b8892e);">
                <h2 class="text-xl font-bold flex items-center" style="color: #010717;">
                    <i class="fas fa-edit mr-3"></i>
                    Update Node Configuration
                </h2>
            </div>

            <form action="{{ route('admin.departments.update', $department->id) }}" method="POST" class="p-8 space-y-6" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Saving Changes...';">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #eaeef2;">Department Name <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $department->name) }}" 
                           class="w-full px-4 py-3 rounded-lg font-medium transition"
                           style="background-color: #010717; border: 1px solid #d2a63d40; color: #eaeef2; outline: none;"
                           onfocus="this.style.borderColor='#d2a63d'; this.style.boxShadow='0 0 0 2px #d2a63d30';"
                           onblur="this.style.borderColor='#d2a63d40'; this.style.boxShadow='none';"
                           placeholder="e.g. Information Technology" required>
                    @error('name') <p class="text-xs mt-1 font-semibold" style="color: #ef4444;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #eaeef2;">Node Code ID <span class="text-xs font-normal" style="color: #eaeef2; opacity: 0.4;">(Optional)</span></label>
                    <input type="text" name="department_code" value="{{ old('department_code', $department->department_code) }}" 
                           class="w-full px-4 py-3 rounded-lg font-medium font-mono transition"
                           style="background-color: #010717; border: 1px solid #d2a63d40; color: #eaeef2; outline: none;"
                           onfocus="this.style.borderColor='#d2a63d'; this.style.boxShadow='0 0 0 2px #d2a63d30';"
                           onblur="this.style.borderColor='#d2a63d40'; this.style.boxShadow='none';"
                           placeholder="e.g. IT-01">
                    @error('department_code') <p class="text-xs mt-1 font-semibold" style="color: #ef4444;">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end pt-6 mt-8" style="border-top: 1px solid #d2a63d20;">
                    <a href="{{ route('admin.departments.index') }}" class="px-6 py-3 font-medium transition-colors mr-4" style="color: #eaeef2; opacity: 0.5;" onmouseover="this.style.opacity='1';" onmouseout="this.style.opacity='0.5';">Cancel</a>
                    <button type="submit" class="px-8 py-3 font-bold rounded-lg shadow-md flex items-center transition-all" style="background-color: #d2a63d; color: #010717;" onmouseover="this.style.opacity='0.88'; this.style.transform='translateY(-1px)';" onmouseout="this.style.opacity='1'; this.style.transform='translateY(0)';">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection