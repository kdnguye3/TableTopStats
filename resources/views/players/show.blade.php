@extends('layouts.app')

@section('content')

    <playertable :id="{{$id}}" :initial-group="{{$group}}" :initial-season="{{$season}}"></playertable>
@endsection
