<!DOCTYPE html>
<html>
<head>
    <title>SMART Method</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .form-section {
            margin-bottom: 2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SMART Method Application</h1>
        <hr>

        <!-- Add Criteria Button -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#criteriaModal">Add Criteria</button>
        <!-- Add Alternative Button -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#alternativeModal">Add Alternative</button>
        <!-- Add Values Button -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#valuesModal">Add Values</button>

        <!-- Existing Criteria -->
        <h2 class="mt-5">Existing Criteria</h2>
        <ul class="list-group">
            @foreach($criteria as $criterion)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $criterion->name }}
                    <form action="/criteria/{{ $criterion->id }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>

        <!-- Existing Alternatives -->
        <h2 class="mt-5">Existing Alternatives</h2>
        <ul class="list-group">
            @foreach($alternatives as $alternative)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $alternative->name }}
                    <form action="/alternatives/{{ $alternative->id }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>

        <!-- Criteria Modal -->
        <div class="modal fade" id="criteriaModal" tabindex="-1" role="dialog" aria-labelledby="criteriaModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="criteriaModalLabel">Add Criteria</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <form id="criteriaForm" action="/criteria" method="POST">
    @csrf
    <div id="criteria-container">
        <div class="form-section">
            <div class="form-group">
                <label for="criteria[0][name]">Criteria Name:</label>
                <input type="text" class="form-control" id="criteria[0][name]" name="criteria[0][name]" required>
            </div>
            <div class="form-group">
                <label for="criteria[0][weight]">Weight:</label>
                <input type="number" step="0.01" class="form-control" id="criteria[0][weight]" name="criteria[0][weight]" required>
            </div>
            <div class="form-group">
                <label for="criteria[0][utility]">Utility:</label>
                <select class="form-control" id="criteria[0][utility]" name="criteria[0][utility]" required>
                    <option value="benefit">Benefit</option>
                    <option value="cost">Cost</option>
                </select>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-secondary" onclick="addCriteria()">Add Another Criteria</button>
    <button type="submit" class="btn btn-primary">Save Criteria</button>
</form>

                    </div>
                </div>
            </div>
        </div>

        <!-- Alternative Modal -->
        <div class="modal fade" id="alternativeModal" tabindex="-1" role="dialog" aria-labelledby="alternativeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="alternativeModalLabel">Add Alternative</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="alternativeForm" action="/alternatives" method="POST">
                            @csrf
                            <div id="alternatives-container">
                                <div class="form-section">
                                    <div class="form-group">
                                        <label for="alternatives[0][name]">Alternative Name:</label>
                                        <input type="text" class="form-control" id="alternatives[0][name]" name="alternatives[0][name]" required>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="addAlternative()">Add Another Alternative</button>
                            <button type="submit" class="btn btn-primary">Save Alternatives</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Modal -->
       <!-- Values Modal -->
<!-- Values Modal -->
<div class="modal fade" id="valuesModal" tabindex="-1" role="dialog" aria-labelledby="valuesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="valuesModalLabel">Add Alternative Values</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="valuesForm" action="{{ route('calculate') }}" method="POST">
                    @csrf
                    <div id="values-container">
                        @foreach($alternatives as $alternative)
                            <div class="form-section">
                                <h3>{{ $alternative->name }}</h3>
                                @foreach($criteria as $criterion)
                                    <div class="form-group">
                                        <label for="values[{{ $alternative->id }}][{{ $criterion->id }}][value]">{{ $criterion->name }}:</label>
                                        <input type="number" step="0.01" class="form-control" id="values[{{ $alternative->id }}][{{ $criterion->id }}][value]" name="values[{{ $alternative->id }}][{{ $criterion->id }}][value]" required>
                                        <input type="hidden" name="values[{{ $alternative->id }}][{{ $criterion->id }}][alternative_id]" value="{{ $alternative->id }}">
                                        <input type="hidden" name="values[{{ $alternative->id }}][{{ $criterion->id }}][criteria_id]" value="{{ $criterion->id }}">
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary">Calculate</button>
                </form>
            </div>
        </div>
    </div>
</div>


        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script>
            let criteriaCount = 1;
            let alternativesCount = 1;

            function addCriteria() {
                const container = document.getElementById('criteria-container');
                const section = document.createElement('div');
                section.classList.add('form-section');

                section.innerHTML = `
                    <div class="form-group">
                        <label for="criteria[${criteriaCount}][name]">Criteria Name:</label>
                        <input type="text" class="form-control" id="criteria[${criteriaCount}][name]" name="criteria[${criteriaCount}][name]" required>
                    </div>
                    <div class="form-group">
                        <label for="criteria[${criteriaCount}][weight]">Weight:</label>
                        <input type="number" step="0.01" class="form-control" id="criteria[${criteriaCount}][weight]" name="criteria[${criteriaCount}][weight]" required>
                    </div>
                    <div class="form-group">
                        <label for="criteria[${criteriaCount}][utility]">Utility:</label>
                        <select class="form-control" id="criteria[${criteriaCount}][utility]" name="criteria[${criteriaCount}][utility]" required>
                            <option value="benefit">Benefit</option>
                            <option value="cost">Cost</option>
                        </select>
                    </div>
                `;
                container.appendChild(section);
                criteriaCount++;
            }

            function addAlternative() {
                const container = document.getElementById('alternatives-container');
                const section = document.createElement('div');
                section.classList.add('form-section');

                section.innerHTML = `
                    <div class="form-group">
                        <label for="alternatives[${alternativesCount}][name]">Alternative Name:</label>
                        <input type="text" class="form-control" id="alternatives[${alternativesCount}][name]" name="alternatives[${alternativesCount}][name]" required>
                    </div>
                `;
                container.appendChild(section);
                alternativesCount++;
            }
        </script>
    </div>
</body>
</html>
