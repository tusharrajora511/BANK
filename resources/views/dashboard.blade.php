@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Dashboard</h3>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
                <div class="card-body">
                    <h4>Welcome, {{ Auth::user()->name }}!</h4>
                    <p>This is your banking dashboard.</p>
                    
                    <div class="mt-4">
                        <h5>Your Information:</h5>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Email:</strong> {{ Auth::user()->email }}</li>
                            <li class="list-group-item"><strong>Phone:</strong> {{ Auth::user()->phone }}</li>
                            <li class="list-group-item"><strong>Address:</strong> {{ Auth::user()->address }}</li>
                            <li class="list-group-item"><strong>City:</strong> {{ Auth::user()->city }}</li>
                            <li class="list-group-item"><strong>State:</strong> {{ Auth::user()->state }}</li>
                            <li class="list-group-item"><strong>Country:</strong> {{ Auth::user()->country }}</li>
                            <li class="list-group-item"><strong>Postal Code:</strong> {{ Auth::user()->postal_code }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection