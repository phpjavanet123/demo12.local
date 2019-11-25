@extends('layouts.app')

@section('content')
    <style>
        .uper {
            margin-top: 40px;
        }
    </style>
    <div class="card uper">
        <div class="card-header">Create User</div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div><br />
            @endif
            <form method="post" action="{{ route('users.store') }}">
                @method('POST')
                @csrf
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" name="name" value="{{old('name')}}" />
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" class="form-control"  name="email" value="{{old('email')}}" />
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" autocomplete="new-password" name="password"  value="{{old('password')}}" />
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select class="form-control" name="role">
                        <option value="2">User</option>
                        <option value="1">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="role">Currencies:</label>
                    <select class="form-control" name="currency_code">
                        @foreach($currencies as $currency)
                            @if (old('currency_code') == $currency->code)
                                <option value="{{$currency->code}}" selected>{{$currency->code}}</option>
                            @else
                                <option value="{{$currency->code}}">{{$currency->code}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection
