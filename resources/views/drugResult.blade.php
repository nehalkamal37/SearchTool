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
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Drug Name</th>
                <th>Ins Results</th>
                <th>NDC Results</th>
                <th>class </th>
                <th>Date </th>
                <th>Script </th>
                <th>Net_Profit </th>


            </tr>
        </thead>
        <tbody>
                <tr>
                    <!-- Drug Name -->
                    <td>{{ $request->drug_name }}</td>
                    <td>{{ $request->insurance }}</td>
                    <td>{{ $request->ndc }}</td>
                    <td>{{ $class }}</td>
                    @foreach ($data as $item)
                    <td>{{ \Carbon\Carbon::parse($item->Date)->format('m/d/Y')  }}</td>
                    <td>{{$item->Script }}</td>
                    <td>{{$item->Net_Profit }}</td>

                    @endforeach

@if(isset($drug_data))
@foreach ($drug_data as $item)
<td>{{$item->form }}</td>
<td>{{$item->length }}</td>
<td>{{$item->mfg }}</td>

@endforeach
@endif
                 
                    
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
    <h2 class="mb-4">Alternatives Results</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>class Name</th>
                <th>drug Name</th>
                <th>drug NDC</th>
                <th>Inurance </th>
                <th>Script </th>
                <th>Date </th>

                <th>RxCui </th>
                <th>Net_Profit </th>

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
                    <td>{{ \Carbon\Carbon::createFromFormat('d/m/Y H:i', $i->Date)->format('m/d/Y') }}</td>

                    <td>{{ $i->RxCui}}</td>
                    <td>{{ $i->Net_Profit}}</td>

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

    @if(isset($drugs))
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>class Name</th>
                <th>drug Name</th>
                <th>drug NDC</th>
                <th> </th>
                <th>Script </th>
                <th>RxCui </th>
                <th>Net_Profit </th>

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
            

                    </tr>
                        @endif
                    @endforeach

                 
                    
                 
                </tr>
            
        </tbody>
    </table>
    @endif
</div>
</body>
</html>
