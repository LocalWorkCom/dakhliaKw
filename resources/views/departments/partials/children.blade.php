@foreach ($children as $child)
    <tr class="child-row">
        <td>{{ $child->id }}</td>
        <td>{{ $child->name }}</td>
        <td>{{ $child->manager ? $child->manager->name : 'N/A' }}</td>
        <td>{{ $child->managerAssistant ? $child->managerAssistant->name : 'N/A' }}</td>
        <td>{{ $child->children_count }}</td>
        <td>{{ $child->iotelegrams_count }}</td>
        <td>{{ $child->outgoings_count }}</td>
        <td></td> <!-- Empty cell for the nested 'action' buttons -->
    </tr>
@endforeach
