<!DOCTYPE html>
<html>
<head>
    <title>SMART Method</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>SMART Method Application</h1>
        <hr>

        <h2>Add Criteria</h2>
        <form action="/criteria" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Criteria Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="weight">Weight:</label>
                <input type="number" step="0.01" class="form-control" id="weight" name="weight" required>
            </div>
            <div class="form-group">
                <label for="utility">Utility:</label>
                <select class="form-control" id="utility" name="utility" required>
                    <option value="benefit">Benefit</option>
                    <option value="cost">Cost</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Criteria</button>
        </form>

        <h2 class="mt-5">Add Alternative</h2>
        <form action="/alternatives" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Alternative Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Alternative</button>
        </form>

        <h2 class="mt-5">Add Alternative Values</h2>
        <form action="/values" method="POST">
            @csrf
            <div class="form-group">
                <label for="alternative_id">Alternative:</label>
                <select class="form-control" id="alternative_id" name="alternative_id" required>
                    @foreach($alternatives as $alternative)
                        <option value="{{ $alternative->id }}">{{ $alternative->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="criteria_id">Criteria:</label>
                <select class="form-control" id="criteria_id" name="criteria_id" required>
                    @foreach($criteria as $criterion)
                        <option value="{{ $criterion->id }}">{{ $criterion->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="value">Value:</label>
                <input type="number" step="0.01" class="form-control" id="value" name="value" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Value</button>
        </form>
    </div>
</body>
</html>
