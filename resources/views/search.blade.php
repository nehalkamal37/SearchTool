
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
<a href="{{route('dash')}}" class="btn btn-secondary w-10">Dashboard</a>

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
    <div style="margin-to: -1333px">
    <h1>CDI Medication Guiding Tool 💊</h1>
    </div>
  
    
    <form id="searchForm" method="post" action="{{ route('search')}}" class="d-flex flex-column align-items-center">
        @csrf
        
        <div class="mb-3 w-100">
            <label for="drugName" class="form-label"><h6>Search for a Drug Name :</h6></label>
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
            <a href="#" id="submitNdcLink" class="btn btn-light w-100">related NDCs info</a>

       
        </div>
    
        <button type="submit" class="btn btn-primary w-100">Search Drug</button>
    </form>

<!-- to Display NDCs -->
<script>
$(document).ready(function () {
    $('#submitNdcLink').on('click', function (e) {
        e.preventDefault(); // Prevent default link behavior

        const selectedNdc = $('#ndc').val(); // Get the selected NDC value

        if (selectedNdc) {
            const url = `/process-ndc?ndc=${encodeURIComponent(selectedNdc)}`; // Construct the GET URL
            console.log('Redirecting to:', url);

            // Redirect to the constructed URL
            window.location.href = url;
        } else {
            alert('Please select an NDC before submitting.');
        }
    });
});
</script>
<!--   just shit  -->
<script>
    $(document).ready(function () {
        // Mapping for insurance short names to full names
        const insuranceMapping = {
            'AL': 'Aetna (AL)',
            'BW': 'Aetna (BW)',
            'AD': 'Aetna Medicare (AD)',
            'AF': 'Anthem BCBS (AF)',
            'DS': 'Blue Cross Blue Shield (DS)',
            'CA': 'Blue Shield Medicare (CA)',
            'FQ': 'Capital Rx (FQ)',
            'BF': 'Caremark (BF)',
            'ED': 'CatalystRx (ED)',
            'AM': 'Cigna (AM)',
            'BO': 'Default Claim Format (BO)',
            'AP': 'Envision Rx Options (AP)',
            'CG': 'Express Scripts (CG)',
            'BI': 'Horizon (BI)',
            'AJ': 'Humana Medicare (AJ)',
            'BP': 'informedRx (BP)',
            'AO': 'MEDCO HEALTH (AO)',
            'AC': 'MEDCO MEDICARE PART D (AC)',
            'AQ': 'MEDGR (AQ)',
            'CC': 'MY HEALTH LA (CC)',
            'AG': 'Navitus Health Solutions (AG)',
            'AH': 'OptumRx (AH)',
            'AS': 'PACIFICARE LIFE AND H (AS)',
            'FJ': 'Paramount Rx (FJ)',
            'X ': 'PF - DEFAULT (X )',
            'EA': 'Pharmacy Data Management (EA)',
            'DW': 'PHCS (DW)',
            'AX': 'PINNACLE (AX)',
            'BN': 'Prescription Solutions (BN)',
            'AA': 'Tri-Care Express Scripts (AA)',
            'AI': 'United Healthcare (AI)'
        };

        function updateOptions(selector, options, mapping = null) {
            let element = $(selector);
            element.empty();
            element.append('<option value="">-- Select --</option>');

            options.forEach(function (option) {
                // Use mapping if provided
                let displayText = mapping && mapping[option] ? mapping[option] : option;
                element.append('<option value="' + option + '">' + displayText + '</option>');
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

                        // Update dropdowns with the fetched data
                        updateOptions('#insurance', response.insurances, insuranceMapping); // Use mapping for insurances
                        updateOptions('#ndc', response.ndcs); // No mapping for NDCs
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




<!-- main codeee-->

</body>
</html>