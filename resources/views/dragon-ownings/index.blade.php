@extends('layouts.app')

@section('title', 'Dragon Owning')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Dragon Owning</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">InGame Data</a></li>
                    <li class="breadcrumb-item active">Dragon Owning</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    </div>
@endif

@if(session('info'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info">{{ session('info') }}</div>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning">{{ session('warning') }}</div>
        </div>
    </div>
@endif

<div class="row">
    @forelse($accounts as $account)
        <div class="col-xl-3 col-md-6">
            <div class="card prod-p-card bg-c-yellow">
                <div class="card-body">
                    <div class="row align-items-center m-b-25">
                        <div class="col">
                            <h6 class="m-b-5 text-white">{{ $account->account_name }}</h6>
                            <h3 class="m-b-0 text-white">
                                <a href="{{ route('dragon-ownings.show', $account) }}" class="text-white text-decoration-none">
                                    {{ $account->dragonOwningDetails->count() }}
                                </a>
                            </h3>
                            <small class="text-white">Dragons</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crown text-c-yellow f-18"></i>
                        </div>
                    </div>
                    <a href="{{ route('dragon-ownings.create', $account) }}" class="btn btn-sm btn-light btn-block">Add Dragon</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted">No accounts found.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
