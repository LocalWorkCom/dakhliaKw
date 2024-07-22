<option value="{{ $department->id }}">
    {{ str_repeat('» ', $level) }}{{ $department->name }}
</option>
@if ($department->children)
    @foreach ($department->children as $child)
        @include('departments.partials.department-option', ['department' => $child, 'level' => $level + 1])
    @endforeach
@endif