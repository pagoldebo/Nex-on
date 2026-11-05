@props([
    'title' => null,
    'breadcrumb' => null,
])
@include('admin.layout', [
    'title' => $title,
    'breadcrumb' => $breadcrumb,
    'slot' => $slot,
])
