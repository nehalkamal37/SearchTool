<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Search Results</h2>
    
<a href="{{route('searchPage')}}"><button > Go Back</button></a>
  

    {{--<h1>Details for NDC: {{ $selectedNdc }} and related data</h1>

    @if ($drugName)
        <p><strong>Drug Name:</strong> {{ $drugName }}</p>
    @else
        <p>No drug found for the provided NDC.</p>
    @endif
    --}}
    @if ($relatedRows->isNotEmpty())
        <h2>All NDCs Data Related to "{{ $drugName }}"</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <!-- Dynamically list table headers based on model fields -->
                    @foreach (array_keys($relatedRows->first()->toArray()) as $column)
                        <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($relatedRows as $row)
                    <tr>
                        <!-- Dynamically list table data -->
                        @foreach ($row->toArray() as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No related data found for this drug name.</p>
    @endif
    
    
</div>
</body>
</html>
