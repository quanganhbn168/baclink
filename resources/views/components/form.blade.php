@switch($type)
    @case('text')
    @case('email')
    @case('number')
    @case('date')
        <x-form.input :name="$name" :label="$label" :type="$type" :value="$value" :required="$required" />
        @break

    @case('select')
        <x-form.select :name="$name" :label="$label" :value="$value" :options="$options" :required="$required" />
        @break

    @case('textarea')
        <x-form.textarea :name="$name" :label="$label" :value="$value" :required="$required" />
        @break

    @case('summernote')
        <x-form.summernote :name="$name" :label="$label" :value="$value" :required="$required" />
        @break

    @case('switch')
    @case('checkbox')
        <x-form.switch :name="$name" :label="$label" :value="$value" />
        @break

    @case('filepond')
        <x-form.filepond :name="$name" :label="$label" :value="$value" :upload-url="$uploadUrl ?? null" />
        @break

    @default
        <x-form.input :name="$name" :label="$label" :type="$type" :value="$value" :required="$required" />
@endswitch
