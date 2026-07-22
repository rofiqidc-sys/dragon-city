@extends('layouts.app')

@section('title', 'Edit Account')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Edit Account</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Accounts</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Edit Account</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('accounts.update', $account) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="account_name">Account Name</label>
                        <input type="text" name="account_name" id="account_name" class="form-control" value="{{ old('account_name', $account->account_name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="fb_mail">FB Mail</label>
                        <input type="email" name="fb_mail" id="fb_mail" class="form-control" value="{{ old('fb_mail', $account->fb_mail) }}">
                    </div>
                    <div class="form-group">
                        <label for="gmail">Gmail</label>
                        <input type="email" name="gmail" id="gmail" class="form-control" value="{{ old('gmail', $account->gmail) }}">
                    </div>
                    <div class="form-group">
                        <label for="ms_mail">MS Mail</label>
                        <input type="email" name="ms_mail" id="ms_mail" class="form-control" value="{{ old('ms_mail', $account->ms_mail) }}">
                    </div>
                    <div class="form-group">
                        <label for="account_status">Account Status</label>
                        <select name="account_status" id="account_status" class="form-control">
                            <option value="active" {{ old('account_status', $account->account_status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('account_status', $account->account_status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
