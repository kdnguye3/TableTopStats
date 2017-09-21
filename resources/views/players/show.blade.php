@extends('layouts.app')

@section('content')
    <section class="hero is-dark is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    Plays
                </h1>
            </div>
        </div>
    </section>
    <playertable :id="{{$id}}"></playertable>
@endsection
