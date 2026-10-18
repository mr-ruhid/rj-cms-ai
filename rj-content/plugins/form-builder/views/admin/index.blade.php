@extends('admin.layouts.app')

@section('title', __('Forms'))

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Forms') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage your forms and submissions.') }}</p>
        </div>

        <a href="{{ route('admin.plugin.forms.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Create Form') }}
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        @if ($forms->isEmpty())
            <div class="p-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="mt-4 text-sm text-gray-500">{{ __('No forms created yet.') }}</p>
                <a href="{{ route('admin.plugin.forms.create') }}"
                   class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-lg bg-gradient-to-r from-rj-purple to-rj-pink text-white text-sm font-medium hover:opacity-90 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    {{ __('Create First Form') }}
                </a>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left font-medium text-gray-600 px-4 py-3">{{ __('Name') }}</th>
                        <th class="text-left font-medium text-gray-600 px-4 py-3 hidden md:table-cell">{{ __('Slug') }}</th>
                        <th class="text-center font-medium text-gray-600 px-4 py-3">{{ __('Submissions') }}</th>
                        <th class="text-center font-medium text-gray-600 px-4 py-3">{{ __('Status') }}</th>
                        <th class="text-right font-medium text-gray-600 px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($forms as $form)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $form->name }}</div>
                                @if($form->description)
                                    <div class="text-xs text-gray-500 mt-0.5 truncate max-w-xs">{{ $form->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <code class="text-xs text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded">{{ $form->slug }}</code>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('admin.plugin.forms.submissions.index', $form) }}"
                                   class="inline-flex items-center justify-center min-w-[32px] px-2 py-0.5 rounded-full text-xs font-medium {{ $form->submissions_count > 0 ? 'bg-rj-purple/10 text-rj-purple hover:bg-rj-purple/20' : 'bg-gray-100 text-gray-500' }} transition">
                                    {{ $form->submissions_count }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($form->status === 'active')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        {{ __('Inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.plugin.forms.submissions.index', $form) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition" title="{{ __('View Submissions') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </a>

                                    <a href="{{ route('admin.plugin.forms.edit', $form) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-rj-purple/10 hover:text-rj-purple transition" title="{{ __('Edit') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.plugin.forms.destroy', $form) }}"
                                          onsubmit="return confirm('{{ __('Delete this form and all its submissions?') }}')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" title="{{ __('Delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection
