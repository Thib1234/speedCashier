{{-- @if($sales)
    <show :data="{{ $startDate }}" :start-date="'{{ $startDate }}'" :end-date="'{{ $endDate }}'"></show-facture>
@else
    <!-- Votre autre code si nécessaire -->
@endif --}}

{{ $salesData->totalSalesHtva }}

@extends('app')

@section('content')
<show :data="{{ json_encode($sales) }}" :salesData="{{ json_encode($salesData) }}" :total_sales="{{ json_encode($total_sales) }}" :start-date="'{{ $startDate }}'" :end-date="'{{ $endDate }}'"></show>
@endsection