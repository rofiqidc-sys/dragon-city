@extends('layouts.app')

@section('title', 'Accounts')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Accounts</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Master Data</a></li>
                    <li class="breadcrumb-item active">Accounts</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Account List</h5>
                <a href="{{ route('accounts.create') }}" class="btn btn-primary float-right">Add Account</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Account Name</th>
                                <th>FB Mail</th>
                                <th>Gmail</th>
                                <th>MS Mail</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $index => $account)
                                <tr class="{{ $index % 2 === 0 ? 'table-active' : '' }} {{ $account->account_status === 'active' ? 'table-success' : 'table-danger' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $account->account_name }}</td>
                                    <td>{{ $account->fb_mail }}</td>
                                    <td>{{ $account->gmail }}</td>
                                    <td>{{ $account->ms_mail }}</td>
                                    <td>
                                        <span class="badge {{ $account->account_status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($account->account_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('accounts.destroy', $account) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this account?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="table-info">
                                    <td colspan="7" class="text-center">No accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
