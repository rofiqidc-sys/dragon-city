@extends('layouts.app')

@section('title', 'Add Dragon to Account')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Add Dragon to Account</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dragon-ownings.index') }}">Dragon Owning</a></li>
                    <li class="breadcrumb-item active">Add Dragon</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Add Dragon to {{ $account->account_name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dragon-owning-details.store', $account) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="dragon_id">Select Dragon <span class="text-danger">*</span></label>
                        <select name="dragon_id" id="dragon_id" class="form-control" required>
                            <option value="">-- Choose a Dragon --</option>
                            @foreach($dragons as $dragon)
                                @if(!$account->dragonOwningDetails->contains('dragon_id', $dragon->id))
                                    <option value="{{ $dragon->id }}">
                                        {{ $dragon->dragon_name }} ({{ $dragon->rarity->name ?? '-' }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('dragon_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Dragon</button>
                        <a href="{{ route('dragon-ownings.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Account Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Account Name:</strong> {{ $account->account_name }}</p>
                <p><strong>Email:</strong> {{ $account->gmail }}</p>
                <p><strong>Status:</strong> {{ $account->account_status }}</p>
                <p><strong>Current Dragons:</strong> {{ $account->dragonOwningDetails->count() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
