<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Parent-Child Row Example</title>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <table id="example" class="display" style="width:100%">
        <thead>
            <tr>
                <th></th> <!-- For the expansion control -->
                <th>Name</th>
                {{-- <th>Position</th>
                    <th>Office</th>
                    <th>Extn.</th>
                    <th>Start date</th>
                    <th>Salary</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($group as $item)
                <tr>
                    <td class="details-control"></td>
                    <td>{{ $item->name }}</td>
                </tr>
            @endforeach

            <!-- Add more rows as needed -->
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            // DataTable initialization
            var table = $('#example').DataTable({
                "columnDefs": [{
                    "targets": [0], // Target the first column
                    "orderable": false, // Prevent ordering on this column
                    "className": 'details-control', // Add class for styling
                    "data": null,
                    "defaultContent": '' // Leave empty for the child control
                }]
            });

            // Add event listener for opening and closing child rows
            $('#example tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                if (row.child.isShown()) {
                    // Close the child row
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open the child row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });

            // Format function for row details
            function format(d) {
                // `d` is the original data object for the row
                return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                    '<tr>' +
                    '<td>Name:</td>' +
                    '<td>' + d[1] + '</td>' +
                    '</tr>' +

                    '<tr>' +
                    '<td>Office:</td>' +
                    '<td>' + d[3] + '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Extension number:</td>' +
                    '<td>' + d[4] + '</td>' +
                    '</tr>' +
                    '</table>';
            }
        });
    </script>
</body>

</html>
