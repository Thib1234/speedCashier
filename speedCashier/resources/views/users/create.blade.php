@extends('app')

@section('content')
	<a href="{{route('users')}}">Liste des utilisateurs</a>
	<create-user />
@endsection