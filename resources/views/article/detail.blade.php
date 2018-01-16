@extends('layouts.app')
@section('content')
    <div class="container">
        <h1 class="h1">{{$info['header']}}</h1>
        <p >{{$info['detail']}}</p>
    </div>
@endsection