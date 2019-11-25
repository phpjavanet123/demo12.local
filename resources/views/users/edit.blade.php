@extends('layouts.app')

@section('content')
<style>
  .uper {
    margin-top: 40px;
  }
</style>
<div class="card uper">
  <div class="card-header">Edit User</div>
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
      <form method="post" action="{{ route('users.update', $user->id) }}">
        @method('PATCH')
        @csrf
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" class="form-control" name="name" value="{{ $user->name }}" />
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="password" class="form-control" name="email" value="{{ $user->email }}" />
        </div>
          <div class="form-group">
              <label for="password">Password:</label>
              <input type="text" class="form-control" name="password" value="" />
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
                      @if ($user->wallet()->first()->currency_id == $currency->id)
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
