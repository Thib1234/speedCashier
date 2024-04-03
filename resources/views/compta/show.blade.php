{{-- @if($sales)
    <show :data="{{ $startDate }}" :start-date="'{{ $startDate }}'" :end-date="'{{ $endDate }}'"></show-facture>
@else
    <!-- Votre autre code si nécessaire -->
@endif --}}

@extends('app')

@section('content')
<show 
    :data="{{ json_encode($sales) }}" 
    :total_sales="{{ $total_sales }}" 
    :start-date="'{{ $startDate }}'" 
    :end-date="'{{ $endDate }}'" 
    :total-sales-htva="{{ $totalSalesHtva }}"
    :total-htva="{{ $totalHtva }}"
></show>

@endsection