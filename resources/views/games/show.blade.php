@extends('layouts.app')

@section('content')
    <gametable :id="{{$id}}" :initial-group="{{$group}}" :initial-season="{{$season}}"></gametable>
@endsection