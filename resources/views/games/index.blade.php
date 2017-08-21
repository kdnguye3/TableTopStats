@extends('layouts.app')

@section('content')
    <div class="container">
        <div>
            <h3>
                Games List
            </h3>
            <div class="row">
                <div class="col-md-2">
                    <a href="{{ route('games.create') }}" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span>Add Game</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped small table-hover">
                <thead>
                    <th>Name</th>
                    <th>Plays</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach($games as $game)
                            <tr>
                                <td>{{$game->name}}</td>
                                <td>{{count($game->plays)}}</td>
                                <td>
                                    <a class="btn btn-default"  href="{{ route('games.show', ['game'=>$game]) }}"><span class="glyphicon glyphicon-eye-open"></span></a>
                                    <a class="btn btn-warning"  href="{{ route('games.edit', ['game'=>$game]) }}"><span class="glyphicon glyphicon-pencil"></span></a>
                                    <form style="display: inline;" action="{{ route('games.destroy', ['game'=>$game]) }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button data-toggle="tooltip" title="Delete Game" data-placement="top" class="btn btn-danger" type="submit" onclick="return confirm('Are you sure you\'d like to delete this game?')">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </button>
                                    </form>
                                </td>

                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
