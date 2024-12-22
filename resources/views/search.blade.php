<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Search</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Search for Drugs</h1>

    <form id="searchForm" method="post" action="{{ route('search')}}">
        @csrf
        <label for="drugName">Drug Name:</label>
        <select id="drugName" name="drug_name">
            <option value="">-- Select Drug Name --</option>
            @foreach($drugNames as $drugName)
                <option value="{{ $drugName }}">{{ $drugName }}</option>
            @endforeach
        </select>

        <div id="relatedInputs" style="display: none;">
            <label for="insurance">Insurance:</label>
            <select id="insurance" name="insurance">
                <option value="">-- Select Insurance --</option>
            </select>

            <label for="ndc">NDC:</label>
            <select id="ndc" name="ndc">
                <option value="">-- Select NDC --</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Primary</button>

    </form>

    <script>
        // main code
        $(document).ready(function () {
            function updateOptions(selector, options) {
                let element = $(selector);
                element.empty();
                element.append('<option value="">-- Select --</option>');

                // Convert options to an array if it is an object
                if (!Array.isArray(options)) {
                    options = Object.values(options);
                }

                options.forEach(function (option) {
                    element.append('<option value="' + option + '">' + option + '</option>');
                });
                console.log(`Updated ${selector} with options:`, options); // Debug updated options
            }

            $('#drugName').on('change', function () {
                let drugName = $(this).val();
                console.log('Selected Drug Name:', drugName);

                if (drugName) {
                    $.ajax({
                        url: '/filter-data',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            drug_name: drugName,
                        },
                        success: function (response) {
                            console.log('Response:', response);

                            if (response.insurances && response.ndcs) {
                                $('#relatedInputs').show();
                            } else {
                                $('#relatedInputs').hide();
                            }

                            updateOptions('#insurance', response.insurances);
                            updateOptions('#ndc', response.ndcs);
                        },
                        error: function (error) {
                            console.error('AJAX Error:', error);
                        }
                    });
                } else {
                    $('#relatedInputs').hide();
                }
            });
        });
    </script>
    <script>
function updateOptions(selector, options) {
    let element = $(selector);
    element.empty();
    element.append('<option value="">-- Select --</option>');

    // Append each option without filtering
    options.forEach(function (option) {
        element.append('<option value="' + option + '">' + option + '</option>');
    });

    console.log(`Updated ${selector} with options:`, options);
}
</script>
    <script>
        $('#drugName').on('change', function () {
    let drugName = $(this).val().trim(); // Trim spaces from the input
    console.log('Selected Drug Name (trimmed):', drugName);

    if (drugName) {
        $.ajax({
            url: '/filter-data',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                drug_name: drugName,
            },
            success: function (response) {
                console.log('Response:', response);

                if (response.insurances && response.ndcs) {
                    $('#relatedInputs').show();
                } else {
                    $('#relatedInputs').hide();
                }

                updateOptions('#insurance', response.insurances);
                updateOptions('#ndc', response.ndcs);
            },
            error: function (error) {
                console.error('AJAX Error:', error);
            }
        });
    } else {
        $('#relatedInputs').hide();
    }
});

        </script>
</body>
</html>
