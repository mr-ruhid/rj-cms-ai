@extends('admin.layouts.app')

@section('title', __('Orders'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Orders') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage and track customer orders.') }}</p>
        </div>
        <div class="flex gap-2">
            <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                {{ __('Export') }}
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="border-b border-gray-100 bg-gray-50/50">
            <nav class="flex gap-6 px-6 -mb-px overflow-x-auto">
                <a href="#" class="py-4 border-b-2 border-rj-purple text-rj-purple font-semibold text-sm whitespace-nowrap">{{ __('All Orders') }}</a>
                <a href="#" class="py-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm whitespace-nowrap">{{ __('Pending') }}</a>
                <a href="#" class="py-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm whitespace-nowrap">{{ __('Processing') }}</a>
                <a href="#" class="py-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm whitespace-nowrap">{{ __('Completed') }}</a>
                <a href="#" class="py-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm whitespace-nowrap">{{ __('Cancelled') }}</a>
            </nav>
        </div>

        <div class="p-4 border-b border-gray-100 flex items-center gap-4 bg-white">
            <div class="relative flex-1 max-w-md">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" placeholder="{{ __('Search by order ID, customer name or email...') }}" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
            </div>
            <select class="rounded-lg border-gray-300 focus:border-rj-purple focus:ring-rj-purple text-sm">
                <option value="">{{ __('All Dates') }}</option>
                <option value="today">{{ __('Today') }}</option>
                <option value="this_week">{{ __('This Week') }}</option>
                <option value="this_month">{{ __('This Month') }}</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 w-16">{{ __('Order') }}</th>
                        <th class="px-6 py-4">{{ __('Customer') }}</th>
                        <th class="px-6 py-4">{{ __('Date') }}</th>
                        <th class="px-6 py-4">{{ __('Status') }}</th>
                        <th class="px-6 py-4">{{ __('Total') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">{{ __('No orders found') }}</h3>
                            <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">{{ __('When customers place orders, they will appear here. You can manage their status and view details.') }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500">
            {{ __('Showing 0 orders') }}
        </div>
    </div>
</div>
@endsection
