@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Create Account</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accounts.index') }}">Accounts</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>New Account</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('accounts.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="account_name">Account Name</label>
                        <input type="text" name="account_name" id="account_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="fb_mail">FB Mail</label>
                        <input type="email" name="fb_mail" id="fb_mail" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="gmail">Gmail</label>
                        <input type="email" name="gmail" id="gmail" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="ms_mail">MS Mail</label>
                        <input type="email" name="ms_mail" id="ms_mail" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="account_status">Account Status</label>
                        <select name="account_status" id="account_status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
