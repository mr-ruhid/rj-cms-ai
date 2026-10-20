@extends('admin.layouts.app')

@section('title', __('AI Usage'))

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-rj-purple" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                {{ __('AI Center') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage models, prompts, and monitor AI usage across your site.') }}</p>
        </div>
    </div>

    <div class="border-b border-gray-200">
        <nav class="flex gap-4 overflow-x-auto">
            <a href="{{ route('admin.ai.index') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                {{ __('Overview') }}
            </a>
            <a href="{{ route('admin.ai.models') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                {{ __('Models') }}
            </a>
            <a href="{{ route('admin.ai.prompts') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                {{ __('Prompts') }}
            </a>
            <a href="{{ route('admin.ai.usage') }}" class="px-4 py-3 border-b-2 border-rj-purple text-rj-purple font-semibold text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ __('Usage') }}
            </a>
            <a href="{{ route('admin.ai.tools') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm flex items-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ __('Tools') }}
            </a>
        </nav>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-900">{{ __('Usage History') }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('Detailed log of all AI requests, token consumption, and associated costs.') }}</p>
            </div>

            <form method="GET" action="{{ route('admin.ai.usage') }}" class="flex items-center gap-3 flex-wrap">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search model or task...') }}" class="w-64 pl-9 pr-3 py-2 rounded-xl border border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                </div>
                
                <select name="task" class="rounded-xl border-gray-300 focus:border-rj-purple focus:ring-2 focus:ring-rj-purple/20 text-sm">
                    <option value="">{{ __('All Tasks') }}</option>
                    @foreach($tasks as $task)
                        <option value="{{ $task }}" {{ request('task') === $task ? 'selected' : '' }}>{{ ucfirst($task) }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition">
                    {{ __('Filter') }}
                </button>

                @if(request()->hasAny(['q', 'task']))
                    <a href="{{ route('admin.ai.usage') }}" class="text-sm text-gray-500 hover:text-gray-900">{{ __('Clear') }}</a>
                @endif
            </form>
        </div>

        @if($logs->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="mt-4 text-sm font-medium text-gray-900">{{ __('No AI usage records found.') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ __('Start using AI features to see logs here.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Date') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('User') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Task') }}</th>
                            <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Model') }}</th>
                            <th class="text-right font-medium text-gray-600 px-4 py-3">{{ __('Tokens') }}</th>
                            <th class="text-right font-medium text-gray-600 px-4 py-3">{{ __('Cost') }}</th>
                            <th class="text-center font-medium text-gray-600 px-4 py-3">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($logs as $log)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-rj-purple to-rj-pink flex items-center justify-center text-white text-[10px] font-bold">
                                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-gray-900">{{ $log->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">{{ __('System') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 capitalize">
                                        {{ $log->task }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-xs text-gray-600">
                                    {{ $log->model }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($log->total_tokens) }}</div>
                                    <div class="text-[10px] text-gray-400">P: {{ number_format($log->prompt_tokens) }} / C: {{ number_format($log->completion_tokens) }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-mono text-xs text-gray-600">
                                    ${{ number_format($log->cost, 6) }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    @if($log->status === 'success')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ __('Success') }}
                                        </span>
                                    @else
                                        <div class="group relative inline-block">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 text-red-700 text-xs font-medium border border-red-200 cursor-help">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                {{ __('Error') }}
                                            </span>
                                            @if($log->error_message)
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-2 bg-gray-900 text-white text-xs rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition z-10 text-left break-words">
                                                    {{ $log->error_message }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection