<!DOCTYPE html>
<html>
<head>
    <title>SMART Method Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>SMART Method Results</h1>
        <hr>
        <h2>Rankings</h2>
        @if($rankings->isEmpty())
            <p>No rankings available.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Alternative</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rankings as $index => $ranking)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ranking->name }}</td>
                            <td>{{ number_format($ranking->score, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
