@php
    $layoutClass = match($layout) {
        'grid-2' => 'rj-form-grid-2',
        'inline' => 'rj-form-inline',
        default => 'rj-form-stack',
    };
@endphp

<style>
    .rj-form-preset-default .rj-form-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
    .rj-form-preset-default .rj-form-input, .rj-form-preset-default textarea, .rj-form-preset-default select { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: #fff; color: #111827; outline: none; transition: all .15s; }
    .rj-form-preset-default .rj-form-input:focus, .rj-form-preset-default textarea:focus, .rj-form-preset-default select:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.1); }
    .rj-form-preset-default .rj-form-submit { padding: 12px 24px; background: #7c3aed; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all .15s; }
    .rj-form-preset-default .rj-form-submit:hover { background: #6d28d9; }

    .rj-form-preset-modern .rj-form-field { margin-bottom: 20px; }
    .rj-form-preset-modern .rj-form-label { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 8px; display: block; }
    .rj-form-preset-modern .rj-form-input, .rj-form-preset-modern textarea, .rj-form-preset-modern select { width: 100%; padding: 14px 16px; border: 1px solid transparent; border-radius: 12px; font-size: 15px; background: #f3f4f6; color: #111827; outline: none; transition: all .2s; box-shadow: inset 0 1px 2px rgba(0,0,0,.02); }
    .rj-form-preset-modern .rj-form-input:focus, .rj-form-preset-modern textarea:focus, .rj-form-preset-modern select:focus { background: #fff; border-color: #c4b5fd; box-shadow: 0 0 0 4px rgba(196,181,253,.3); }
    .rj-form-preset-modern .rj-form-submit { width: 100%; padding: 16px; background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%); color: #fff; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; transition: transform .15s, box-shadow .15s; box-shadow: 0 4px 12px rgba(124,58,237,.3); }
    .rj-form-preset-modern .rj-form-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(124,58,237,.4); }

    .rj-form-preset-minimal .rj-form-field { margin-bottom: 24px; }
    .rj-form-preset-minimal .rj-form-label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin-bottom: 4px; display: block; }
    .rj-form-preset-minimal .rj-form-input, .rj-form-preset-minimal textarea, .rj-form-preset-minimal select { width: 100%; padding: 8px 0; border: none; border-bottom: 2px solid #e5e7eb; border-radius: 0; font-size: 16px; background: transparent; color: #111827; outline: none; transition: border-color .2s; }
    .rj-form-preset-minimal .rj-form-input:focus, .rj-form-preset-minimal textarea:focus, .rj-form-preset-minimal select:focus { border-bottom-color: #7c3aed; }
    .rj-form-preset-minimal .rj-form-submit { padding: 10px 0; background: transparent; color: #7c3aed; border: none; border-bottom: 2px solid #7c3aed; border-radius: 0; font-weight: 700; font-size: 15px; cursor: pointer; transition: opacity .15s; }
    .rj-form-preset-minimal .rj-form-submit:hover { opacity: .7; }

    .rj-form-preset-dark { background: #1e1b4b; padding: 32px; border-radius: 16px; box-shadow: 0 24px 48px -12px rgba(0,0,0,.3); }
    .rj-form-preset-dark .rj-form-field { margin-bottom: 20px; }
    .rj-form-preset-dark .rj-form-label { font-size: 13px; font-weight: 600; color: #e5e7eb; margin-bottom: 8px; display: block; }
    .rj-form-preset-dark .rj-form-input, .rj-form-preset-dark textarea, .rj-form-preset-dark select { width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,.1); border-radius: 8px; font-size: 14px; background: rgba(255,255,255,.05); color: #fff; outline: none; backdrop-filter: blur(10px); transition: all .2s; }
    .rj-form-preset-dark .rj-form-input:focus, .rj-form-preset-dark textarea:focus, .rj-form-preset-dark select:focus { border-color: rgba(255,255,255,.3); background: rgba(255,255,255,.1); }
    .rj-form-preset-dark .rj-form-submit { width: 100%; padding: 14px; background: rgba(255,255,255,.1); color: #fff; border: 1px solid rgba(255,255,255,.2); border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; backdrop-filter: blur(10px); transition: background .15s; }
    .rj-form-preset-dark .rj-form-submit:hover { background: rgba(255,255,255,.2); }
</style>

<div class="rj-form-wrap rj-form-preset-{{ $designPreset }}">
    <form method="POST" action="{{ route('plugin.form.submit', $form->slug) }}" class="rj-form {{ $layoutClass }}">
        @csrf

        @foreach($form->fields as $field)
            @php
                $required = $field->is_required ? 'required' : '';
                $reqMark = $field->is_required ? '<span class="rj-form-req" style="color:#ef4444;">*</span>' : '';
            @endphp

            @if($field->type === 'textarea')
                <div class="rj-form-field rj-form-field-textarea">
                    <label class="rj-form-label">{{ $field->label }} {!! $reqMark !!}</label>
                    <textarea name="{{ $field->name }}" placeholder="{{ $field->placeholder }}" {{ $required }} rows="5"></textarea>
                    @if($field->help_text)
                        <div class="rj-form-help" style="font-size:11px; color:#6b7280; margin-top:4px;">{{ $field->help_text }}</div>
                    @endif
                </div>

            @elseif($field->type === 'select')
                <div class="rj-form-field">
                    <label class="rj-form-label">{{ $field->label }} {!! $reqMark !!}</label>
                    <select name="{{ $field->name }}" {{ $required }}>
                        <option value="">{{ $field->placeholder ?: __('Select an option') }}</option>
                        @if(is_array($field->options))
                            @foreach($field->options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if($field->help_text)
                        <div class="rj-form-help" style="font-size:11px; color:#6b7280; margin-top:4px;">{{ $field->help_text }}</div>
                    @endif
                </div>

            @elseif($field->type === 'checkbox')
                <div class="rj-form-field">
                    <label class="rj-form-label">{{ $field->label }} {!! $reqMark !!}</label>
                    <div class="rj-form-checkbox-group" style="display:flex; flex-direction:column; gap:8px;">
                        @if(is_array($field->options))
                            @foreach($field->options as $option)
                                <label class="rj-form-checkbox" style="display:inline-flex; align-items:center; gap:8px; font-size:14px; cursor:pointer;">
                                    <input type="checkbox" name="{{ $field->name }}[]" value="{{ $option }}" {{ $required }} style="accent-color:#7c3aed;">
                                    <span>{{ $option }}</span>
                                </label>
                            @endforeach
                        @else
                            <label class="rj-form-checkbox" style="display:inline-flex; align-items:center; gap:8px; font-size:14px; cursor:pointer;">
                                <input type="checkbox" name="{{ $field->name }}" value="1" {{ $required }} style="accent-color:#7c3aed;">
                                <span>{{ $field->placeholder ?: $field->label }}</span>
                            </label>
                        @endif
                    </div>
                    @if($field->help_text)
                        <div class="rj-form-help" style="font-size:11px; color:#6b7280; margin-top:4px;">{{ $field->help_text }}</div>
                    @endif
                </div>

            @else
                @php
                    $type = in_array($field->type, ['text', 'email', 'tel', 'url', 'number', 'date', 'time']) ? $field->type : 'text';
                @endphp
                <div class="rj-form-field {{ $field->type === 'textarea' ? 'rj-form-field-full' : '' }}">
                    <label class="rj-form-label">{{ $field->label }} {!! $reqMark !!}</label>
                    <input type="{{ $type }}" name="{{ $field->name }}" placeholder="{{ $field->placeholder }}" class="rj-form-input" {{ $required }}>
                    @if($field->help_text)
                        <div class="rj-form-help" style="font-size:11px; color:#6b7280; margin-top:4px;">{{ $field->help_text }}</div>
                    @endif
                </div>
            @endif
        @endforeach

        <div class="rj-form-actions {{ $layoutClass === 'rj-form-inline' ? 'rj-form-field-full' : '' }}">
            <button type="submit" class="rj-form-submit">
                {{ __('Submit') }}
            </button>
        </div>

        <div class="rj-form-message" style="display: none; padding: 12px; border-radius: 8px; font-size: 14px; margin-top: 16px;"></div>
    </form>
</div>