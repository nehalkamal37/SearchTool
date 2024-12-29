

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Search</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="author" content="colorlib.com">
        <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet" />
        <link href="{{asset('searchPage/css/main.css')}}" rel="stylesheet" />
      
</head>
<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        background-color: #f8f9fa; /* Optional background color */
    }

    form {
        padding: 20px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
</head>


<body>
    <h1>Search for Drugs</h1>
   {{-- old one 
    <form id="searchForm" method="post" action="{{ route('search')}}">
        @csrf
                <h1 class="text-center mb-4"></h1>

        <div class="mb-3 w-100">

        <label for="drugName">Drug Name:</label>
        <select id="drugName" name="drug_name">
            <option value="">-- Select Drug Name --</option>
            @foreach($drugNames as $drugName)
                <option value="{{ $drugName }}">{{ $drugName }}</option>
            @endforeach
        </select>
        </div>
        <div  class="mb-3 w-100"  id="relatedInputs" style="display: none;">
            <label for="insurance">Insurance:</label>
            <select id="insurance" name="insurance">
                <option value="">-- Select Insurance --</option>
            </select>
        </div>
        <div class="mb-3 w-100">

            <label for="ndc">NDC:</label>
            <select id="ndc" name="ndc">
                <option value="">-- Select NDC --</option>
            </select>
        </div>
        </div>
        <button type="submit" class="btn btn-primary">Search Drug</button>

    </form>
    --}}
  
    
    <form id="searchForm" method="post" action="{{ route('search')}}" class="d-flex flex-column align-items-center">
        @csrf
        
        <div class="mb-3 w-100">
            <label for="drugName" class="form-label"><h6>Drug Name:</h6></label>
            <select id="drugName" name="drug_name" class="form-select">
                <option value="">-- Select Drug Name --</option>
                @foreach($drugNames as $drugName)
                    <option value="{{ $drugName }}">{{ $drugName }}</option>
                @endforeach
            </select>
        </div>
    
        <div class="mb-3 w-100">
            <label for="insurance" class="form-label"><h6>Insurance:</h6></label>
            <select id="insurance" name="insurance" class="form-select">
                <option value="">-- Select Insurance --</option>
            </select>
        </div>
    
        <div class="mb-3 w-100">
            <label for="ndc" class="form-label"><h6>NDC:</h6></label>
            <select id="ndc" name="ndc" class="form-select">
                <option value="">-- Select NDC --</option>
              
            </select>
            <a href="{{ route('fndc', ['drug_name' => '']) }}">Get NDCs </a>

            <a href="{{route('fndc')}}" class="btn btn-primary w-100">show ndcs data</a>
       
        </div>
    
        <button type="submit" class="btn btn-primary w-100">Search Drug</button>
    </form>

<!-- Table to Display NDCs -->


    
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
                console.log(Updated ${selector} with options:, options); // Debug updated options
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

  //  console.log(Updated ${selector} with options:, options);
}
</script>
   


<script>
    /*
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
*/

    </script>
    

</body>
</html>
