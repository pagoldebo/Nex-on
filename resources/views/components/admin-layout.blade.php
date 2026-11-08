{{--
    ======================================================================
    REUSABLE BLADE COMPONENT: ADMIN LAYOUT
    ======================================================================
    File: resources/views/components/admin-layout.blade.php
    Component Tag: <x-admin-layout>
    Responsibility: Reusable UI component across the Nex-on application
    Features:
      - Encapsulated component logic, props validation, and slot injection
      - Accessible Tailwind CSS styling and smooth transition animations
--}}
@props([
    'title' => null,
    'breadcrumb' => null,
])
@include('admin.layout', [
    'title' => $title,
    'breadcrumb' => $breadcrumb,
    'slot' => $slot,
])
