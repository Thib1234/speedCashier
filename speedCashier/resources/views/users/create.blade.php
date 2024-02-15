@extends('app')

@section('content')

	<a href="{{route('users.index')}}">Liste des utilisateurs</a>
	<create-user></create-user>

@endsection