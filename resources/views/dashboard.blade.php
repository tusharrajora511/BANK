@extends('layouts.app')
@section('content')
<div class="alert alert-success" role="alert">
    Welcome to the Bank Management System Dashboard!
    Hello{{ Auth::user()->name }}, you are logged.!
</div>
@endsection