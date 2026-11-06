@php
    $isAdmin = $user->role === 'admin';
@endphp

@if($isAdmin)
    @extends('admin.layout')
    @section('content')
        <div class="space-y-8 max-w-4xl">
            @include('profile.partials.profile-content')
        </div>
    @endsection
@else
    <x-storefront-layout :theme="$theme ?? \App\Models\Template::active()" title="Account & Security Profile — {{ config('app.name', 'Nex-on') }}">
        <div class="py-10 lg:py-16 bg-[#F9F8F3] min-h-[calc(100vh-140px)]">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- Breadcrumbs -->
                <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-stone-500 font-medium">
                    <a href="{{ route('home') }}" class="hover:text-stone-900 transition">Home</a>
                    <span class="text-stone-300">/</span>
                    <a href="{{ route('account') }}" class="hover:text-stone-900 transition">My Account</a>
                    <span class="text-stone-300">/</span>
                    <span class="text-stone-800 font-semibold" aria-current="page">Security &amp; Profile</span>
                </nav>

                @include('profile.partials.profile-content')
            </div>
        </div>
    </x-storefront-layout>
@endif

