@extends('app')

@section('content')
<a href="{{route('users.create')}}">Ajouter un utilisateur</a>
<users-list />
@endsection