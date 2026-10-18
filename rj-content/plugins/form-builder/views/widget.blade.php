@php
    $layoutClass = match($layout) {
        'grid-2' => 'rj-form-grid-2',
        'inline' => 'rj-form-inline',
        default => 'rj-form-stack',
    };
@endphp

<div class="rj-form-wrap">
    <form method="POST" action="{{ route('plugin.form.submit', $form->slug) }}" class="rj-form {{ $layoutClass }}">
        @csrf

        @foreach($form->fields as $field)
            @php
                $required = $field->is_required ? 'required' : '';
                $reqMark = $field->is_required ? '<span class="rj-form-req">*</span>' : '';
                $fieldStyle = "background-color: {$styles['field_bg']}; border-color: {$styles['field_border']}; border-radius: {$styles['radius']};";
            @endphp

            @if($field->type === 'textarea')
                <div class="rj-form-field rj-form-field-textarea">
                    <label class="rj-form-label">{{ $field->label }} {!! $reqMark !!}</label>
                    <textarea name="{{ $field->name }}"
                              placeholder="{{ $field->placeholder }}"
                              style="{{ $fieldStyle }}"
                              {{ $required }}></textarea>
                    @if($field->help_text)
                        <div class="rj-form-help">{{ $field->help_text }}</div>
                    @endif
                </div>

            @elseif($field->type === 'select')
                <div class="rj-form-field">
                    <label class="rj-form-label">{{ $field->label }} {!! $reqMark !!}</label>
                    <select name="{{ $field->name }}" style="{{ $fieldStyle }}" {{ $required }}>
                        <option value="">{{ $field->placeholder ?: __('Select an option') }}</option>
                        @if(is_array($field->options))
                            @foreach($field->options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if($field->help_text)
                        <div class="rj-form-help">{{ $field->help_text }}</div>
                    @endif
                </div>

            @elseif($field->type === 'checkbox')
                <div class="rj-form-field">
                    <label class="rj-form-label">{{ $field->label }} {!! $reqMark !!}</label>
                    <div class="rj-form-checkbox-group">
                        @if(is_array($field->options))
                            @foreach($field->options as $option)
                                <label class="rj-form-checkbox">
                                    <input type="checkbox" name="{{ $field->name }}[]" value="{{ $option }}" {{ $required }}>
                                    <span>{{ $option }}</span>
                                </label>
                            @endforeach
                        @else
                            <label class="rj-form-checkbox">
                                <input type="checkbox" name="{{ $field->name }}" value="1" {{ $required }}>
                                <span>{{ $field->placeholder ?: $field->label }}</span>
                            </label>
                        @endif
                    </div>
                    @if($field->help_text)
                        <div class="rj-form-help">{{ $field->help_text }}</div>
                    @endif
                </div>

            @else
                @php
                    $type = in_array($field->type, ['text', 'email', 'tel', 'url', 'number', 'date', 'time']) ? $field->type : 'text';
                @endphp
                <div class="rj-form-field {{ $field->type === 'textarea' ? 'rj-form-field-full' : '' }}">
                    <label class="rj-form-label">{{ $field->label }} {!! $reqMark !!}</label>
                    <input type="{{ $type }}"
                           name="{{ $field->name }}"
                           placeholder="{{ $field->placeholder }}"
                           class="rj-form-input"
                           style="{{ $fieldStyle }}"
                           {{ $required }}>
                    @if($field->help_text)
                        <div class="rj-form-help">{{ $field->help_text }}</div>
                    @endif
                </div>
            @endif
        @endforeach

        <div class="rj-form-actions {{ $layoutClass === 'rj-form-inline' ? 'rj-form-field-full' : '' }}">
            <button type="submit" class="rj-form-submit"
                    style="background-color: {{ $styles['button_bg'] }}; color: {{ $styles['button_text'] }}; border-radius: {{ $styles['radius'] }};">
                {{ __('Submit') }}
            </button>
        </div>

        <div class="rj-form-message" style="display: none;"></div>
    </form>
</div>
