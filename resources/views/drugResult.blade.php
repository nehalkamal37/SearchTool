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
    @if($data->isEmpty())
    <div class="alert alert-warning" role="alert">
        No insurance data available for {{$request->drug_name}} with NDC {{$request->ndc}}
    </div>

    @endif
<a href="{{route('searchPage')}}"><button > Go Back</button></a>
<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            @if($data && count($data) > 0)
                <th>Drug Name</th>
                <th>Ins Results</th>
                <th>NDC Results</th>
                <th>Class</th>
                <th>Date</th>
                <th>Script</th>
                <th>Net Profit</th>
                <th>ACQ</th>
                <th>QTY</th>
                <th>INS PAY</th>
                <th>pat_pay</th>

            @elseif(isset($drug_data) && count($drug_data) > 0)
                <th>Drug Name</th>
                <th>NDC</th>
                <th>Form</th>
                <th>Strength</th>
                <th>Manufacturer</th>
                <th>Acquisition Cost</th>
            @else
                <th colspan="5" class="text-center">No Data Available</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if($data && count($data) > 0)
            @foreach ($data as $item)
                <tr>
                    <td>{{ $request->drug_name }}</td>
                    <td>{{ $request->insurance }}</td>
                    <td>{{ $request->ndc }}</td>
                    <td>{{ $class }}</td>
                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->Date)->format('m/d/Y') }}</td>
                    <td>{{ $item->Script }}</td>
                    <td>{{ $item->Net_Profit }}</td>
                    <td>{{ $item->ACQ }}</td>
                    <td>{{ $item->Qty }}</td>
                    <td>{{ $item->Ins_Pay }}</td>
                    <td>{{ $item->Pat_Pay }}</td>


                </tr>
            @endforeach
        @elseif(isset($drug_data) && count($drug_data) > 0)
            @foreach ($drug_data as $item)
                <tr>
                    <td>{{ $request->drug_name }}</td>
                    <td>{{ $request->ndc }}</td>
                    <td>{{ $item->form }}</td>
                    <td>{{ $item->strength }}</td>
                    <td>{{ $item->mfg }}</td>
                    <td>{{ $item->acq }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" class="text-center">No Data Available</td>
            </tr>
        @endif
    </tbody>
</table>


{{--  main one--}
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Drug Name</th>
                <th>Ins Results</th>
                <th>NDC Results</th>
                <th>class </th>
                @if(($data ))
                <th>Date </th>
                <th>Script </th>
                <th>Net_Profit </th>
@elseif(isset($drug_data))
<th>form </th>
<th>strength </th>
<th>mfg </th>
<th>acq </th>
@else
@endif

            </tr>
        </thead>
        <tbody>
                <tr>
                    <!-- Drug Name -->
                    <td>{{ $request->drug_name }}</td>
                    <td>{{ $request->insurance }}</td>
                    <td>{{ $request->ndc }}</td>
                    <td>{{ $class }}</td>
                    @if($data)
                      @foreach ($data as $item)
                      <td>{{\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->Date)->format('m/d/Y')
                     }}</td>

                    <td>{{$item->Script }}</td>
                    <td>{{$item->Net_Profit }}</td>

                    @endforeach
                    @endif

@if(isset($drug_data))
@foreach ($drug_data as $item)
<td>{{$item->form }}</td>
<td>{{$item->strength }}</td>
<td>{{$item->mfg }}</td>
<td>{{$item->acq }}</td>

@endforeach
@endif
                </tr>
            
        </tbody>
    </table>
--}}
    
    <h3>Alternative Drugs in Same Insurance</h3>
<p>Found {{ $script->count()  }} alternatives in the same class.</p>

<form id="filterForm" method="post" action="{{ route('searchDrug') }}">
    @csrf
    <input type="hidden" name="drug_name" value="{{ $request->drug_name }}">
    <input type="hidden" name="ndc" value="{{ $request->ndc }}">
    <input type="hidden" name="insurance" value="{{ $request->insurance }}">

    <label for="sort_by">Sort Alternatives By:</label>
    <select name="sort_by" id="sort_by" class="form-select" onchange="this.form.submit()">
        <option value="">-- Select --</option>
        <option value="net_profit_desc" {{ request('sort_by') === 'net_profit_desc' ? 'selected' : '' }}>
            Highest Net Profit
        </option>
       <option value="awp_asc" {{ request('sort_by') === 'awp_asc' ? 'selected' : '' }}>
            Lowest AWP
        </option>
        
    </select>
</form>


    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>class Name</th>
                <th>drug Name</th>
                <th>drug NDC</th>
                <th>Insurance </th>
                <th>Script </th>
                <th>Date </th>
                <th>RxCui </th>
                <th>Net_Profit </th>
                <th>ACQ</th>
                <th>QTY</th>
                <th>INS PAY</th>
                <th>pat_pay</th>
            </tr>
        </thead>
        <tbody>
            
                <tr>
                    <!-- Drug Name -->
                  
                    @foreach ($script as $i)
                  
                    
                    <tr>
                     @if($i->Drug_Name == $request->drug_name && $i->NDC == $request->ndc && $i->Ins == $request->insurance)
                        
                        @else
                    <td>{{ $i->Class }}</td>
                    <td>{{ $i->Drug_Name }}</td>
                    <td>{{ $i->NDC }}</td>
                    <td>{{ $i->Ins}}</td>
                    <td>{{ $i->Script}}</td>
                    <td>
                      {{  \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $i->Date)->format('m/d/Y')}}
                    </td>
                    <td>{{ $i->RxCui}}</td>
                    <td>{{ $i->Net_Profit}}</td>
                    <td>{{ $i->ACQ }}</td>
                    <td>{{ $i->Qty }}</td>
                    <td>{{ $i->Ins_Pay }}</td>
                    <td>{{ $i->Pat_Pay }}</td>

                    </tr>
                        @endif
                    @endforeach

                 
                    
                    <!-- Ins Results -->
             {{--       <td>
                        @if (isset($data->drug_name) && $data->drug_name->count() > 0)
                            <ul class="list-unstyled mb-0">
                                @foreach ($allIns[$drug->drug_name] as $ins)
                                    <li>{{ $ins->column_name }}</li> <!-- Replace `column_name` with your actual column -->
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">No results found</span>
                        @endif
                    </td>
                    
                    <!-- NDC Results -->
                    <td>
                        @if (isset($allNdc[$drug->drug_name]) && $allNdc[$drug->drug_name]->count() > 0)
                            <ul class="list-unstyled mb-0">
                                @foreach ($allNdc[$drug->drug_name] as $ndc)
                                    <li>{{ $ndc->column_name }}</li> <!-- Replace `column_name` with your actual column -->
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">No results found</span>
                        @endif
                    </td>
                    --}}
                </tr>
            
        </tbody>
    </table>

    @if(($drugs))
    <h5>Alternative Drugs with no insurance Data</h5>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>class Name</th>
                <th>drug Name</th>
                <th>drug NDC</th>
                <th>form </th>
                <th>strength </th>
                <th>awp </th>
                <th>mfg </th>

            </tr>
        </thead>
        <tbody>
                <tr>
                    <!-- Drug Name -->
                  
                    @foreach ($drugs as $drug)
                  
                    
                    <tr>
                     @if($drug->drug_name == $request->drug_name && $drug->ndc == $request->ndc )
                        
                        @else
                    <td>{{ $drug->drug_class }}</td>
                    <td>{{ $drug->drug_name }}</td>
                    <td>{{ $drug->ndc }}</td>
                    <td>{{ $drug->form}}</td>
                    <td>{{ $drug->strength}}</td>
                    <td>{{ $drug->awp}}</td>
                    <td>{{ $drug->mfg }}</td>


                    </tr>
                        @endif
                    @endforeach

                 
                    
                 
                </tr>
            
        </tbody>
    </table>
    @endif
</div>




<!--- newwwwwwwwwwwwww    testing        -->


{{-- Selected Drug Data --

@if($data->isNotEmpty())
    <h2>Selected Drug</h2>
    @foreach($data as $drug)
        <p>{{ $drug->Drug_Name }} - {{ $drug->Ins }} - {{ $drug->NDC }} ({{ $drug->Date }})</p>
    @endforeach
@endif

{{-- Alternatives from Scripts --}
@if($script->isNotEmpty())
    <h2>Alternatives (Scripts)</h2>
    @foreach($script as $alt)
        <p>{{ $alt->Drug_Name }} - {{ $alt->Ins }} - {{ $alt->NDC }} ({{ $alt->Date }})</p>
    @endforeach
@endif

{{-- Alternatives from Drugs --}
@if($drugs->isNotEmpty())
    <h2>Alternatives (Drugs)</h2>
    @foreach($drugs as $drug)
        <p>{{ $drug->drug_name }} - {{ $drug->drug_class }} - {{ $drug->ndc }}</p>
    @endforeach
@endif
--}}
</body>
</html>
