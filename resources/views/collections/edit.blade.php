@extends('layouts.app')

@section('title', 'Edit Collection')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Edit Collection</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('collections.index') }}">Collections</a></li>
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
                <h5>Edit Collection</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('collections.update', $collection) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="collection_name">Collection Name <span class="text-danger">*</span></label>
                        <input type="text" name="collection_name" id="collection_name" class="form-control @error('collection_name') is-invalid @enderror" value="{{ old('collection_name', $collection->collection_name) }}" required>
                        @error('collection_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="gem_reward">Gem Reward <span class="text-danger">*</span></label>
                            <input type="number" name="gem_reward" id="gem_reward" class="form-control @error('gem_reward') is-invalid @enderror" value="{{ old('gem_reward', $collection->gem_reward) }}" min="0" required>
                            @error('gem_reward')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="dragon_reward_id">Dragon Reward</label>
                            <select name="dragon_reward_id" id="dragon_reward_id" class="form-control @error('dragon_reward_id') is-invalid @enderror">
                                <option value="">-- Select Dragon --</option>
                                @foreach($allDragons as $dragon)
                                    <option value="{{ $dragon->id }}" @if(old('dragon_reward_id', $collection->dragon_reward_id) == $dragon->id) selected @endif>{{ $dragon->dragon_name }} ({{ $dragon->rarity->name ?? '-' }})</option>
                                @endforeach
                            </select>
                            @error('dragon_reward_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('collections.show', $collection) }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
