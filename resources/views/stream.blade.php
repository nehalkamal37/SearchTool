@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Enhanced Medication Guiding Tool 💊</h1>

    <!-- Search Form -->
    <form method="GET" action="{{ route('drugs.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="drug_name">Drug Name</label>
                <select name="drug_name" id="drug_name" class="form-control">
                    <option value="">Select a drug...</option>
                    @foreach($df->pluck('Drug Name')->unique() as $drug)
                        <option value="{{ $drug }}" {{ $drugName === $drug ? 'selected' : '' }}>{{ $drug }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="insurance">Insurance</label>
                <select name="insurance" id="insurance" class="form-control">
                    <option value="">Select insurance...</option>
                    @foreach($df->pluck('Ins Full Name')->unique() as $insurance)
                        <option value="{{ $insurance }}" {{ $insuranceFullName === $insurance ? 'selected' : '' }}>{{ $insurance }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="ndc">NDC</label>
                <select name="ndc" id="ndc" class="form-control">
                    <option value="">Select an NDC...</option>
                    @foreach($df->pluck('NDC')->unique() as $ndcOption)
                        <option value="{{ $ndcOption }}" {{ $ndc === $ndcOption ? 'selected' : '' }}>{{ $ndcOption }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Search</button>
    </form>

    <!-- Filtered Results -->
    @if($df->isNotEmpty())
        <h3>Filtered Results</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Drug Name</th>
                    <th>NDC</th>
                    <th>Class</th>
                    <th>Insurance</th>
                    <th>Net Profit</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($df as $drug)
                    <tr>
                        <td>{{ $drug['Drug Name'] }}</td>
                        <td>{{ $drug['NDC'] }}</td>
                        <td>{{ $drug['class'] }}</td>
                        <td>{{ $drug['Ins Full Name'] }}</td>
                        <td>{{ $drug['Net Profit'] }}</td>
                        <td>{{ $drug['Date']->format('m/d/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No results found.</p>
    @endif
</div>
@endsection
