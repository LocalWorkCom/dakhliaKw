<!DOCTYPE html>
<html>
<head>
    <title>Inspector Missions</title>
</head>
<body>
    <h1>Inspector Missions</h1>

    @if ($results->isEmpty())
        <p>No data found.</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>Group ID</th>
                    <th>Group Team ID</th>
                    <th>Inspector ID</th>
                    <th>Grouped IDs Group Point</th>
                    <th>Number of Points</th>
                    <th>Available Group Point IDs</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results as $result)
                    <tr>
                        <td>{{ $result->group_id }}</td>
                        <td>{{ $result->group_team_id }}</td>
                        <td>{{ $result->inspector_id }}</td>
                        <td>{{ $result->grouped_ids_group_point }}</td>
                        <td>{{ $result->num_points }}</td>
                        <td>
                            @if (isset($availableGroupPoints[$result->group_team_id]))
                                @foreach ($availableGroupPoints[$result->group_team_id] as $groupPointId)
                                    <p>Group Point ID: {{ $groupPointId }}</p>
                                @endforeach
                            @else
                                <p>No available group points</p>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
