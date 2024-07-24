@if ($department->parent_id == Auth::user()->department_id)
    <option value="{{ $department->id }}">
        {{ $level }}{{ $department->name }}
    </option>
    @if ($department->children)
        @foreach ($department->children as $child)
            @include('departments.partials.department-option', ['department' => $child, 'level' => $level + 1])
        @endforeach
    @endif
@endif