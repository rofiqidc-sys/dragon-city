@extends('layouts.app')

@section('title', 'Create Rarity')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Create Rarity</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('rarities.index') }}">Rarities</a></li>
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
                <h5>New Rarity</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('rarities.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="alias">Alias</label>
                        <input type="text" name="alias" id="alias" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="key_need_to_summon">Key Need To Summon</label>
                        <input type="text" name="key_need_to_summon" id="key_need_to_summon" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('rarities.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
