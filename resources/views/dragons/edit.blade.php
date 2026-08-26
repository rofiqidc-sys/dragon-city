@extends('layouts.app')

@section('title', 'Edit Dragon')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Edit Dragon</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dragons.index') }}">Dragons</a></li>
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
                <h5>Edit Dragon</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dragons.update', $dragon) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="dragon_name">Dragon Name <span class="text-danger">*</span></label>
                        <input type="text" name="dragon_name" id="dragon_name" class="form-control" value="{{ old('dragon_name', $dragon->dragon_name) }}" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="rarity_id">Rarity</label>
                            <select name="rarity_id" id="rarity_id" class="form-control">
                                <option value="">No rarity</option>
                                <option value="">-- Select Rarity --</option>
                                @foreach($rarities as $rarity)
                                    <option value="{{ $rarity->id }}" @if($dragon->rarity_id == $rarity->id) selected @endif>{{ $rarity->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="element_1_id">Element 1 <span class="text-danger">*</span></label>
                            <select name="element_1_id" id="element_1_id" class="form-control" required>
                                <option value="">-- Select Element --</option>
                                @foreach($elements as $element)
                                    <option value="{{ $element->id }}" @if($dragon->element_1_id == $element->id) selected @endif>{{ $element->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="element_2_id">Element 2</label>
                            <select name="element_2_id" id="element_2_id" class="form-control">
                                <option value="">-- Select Element --</option>
                                @foreach($elements as $element)
                                    <option value="{{ $element->id }}" @if($dragon->element_2_id == $element->id) selected @endif>{{ $element->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="element_3_id">Element 3</label>
                            <select name="element_3_id" id="element_3_id" class="form-control">
                                <option value="">-- Select Element --</option>
                                @foreach($elements as $element)
                                    <option value="{{ $element->id }}" @if($dragon->element_3_id == $element->id) selected @endif>{{ $element->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="element_4_id">Element 4</label>
                        <select name="element_4_id" id="element_4_id" class="form-control">
                            <option value="">-- Select Element --</option>
                            @foreach($elements as $element)
                                <option value="{{ $element->id }}" @if($dragon->element_4_id == $element->id) selected @endif>{{ $element->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="summon_time">Summon Time (integer)</label>
                            <input type="number" name="summon_time" id="summon_time" class="form-control" value="{{ old('summon_time', $dragon->summon_time) }}" step="1">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="orb_to_summon">Orb To Summon <span class="text-danger">*</span></label>
                            <input type="number" name="orb_to_summon" id="orb_to_summon" class="form-control" value="{{ old('orb_to_summon', $dragon->orb_to_summon) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="hatching_time">Hatching Time (seconds) <span class="text-danger">*</span></label>
                        <input type="number" name="hatching_time" id="hatching_time" class="form-control" value="{{ old('hatching_time', $dragon->hatching_time) }}" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_collection" name="is_collection" value="1" @if(old('is_collection', $dragon->is_collection)) checked @endif>
                                <label class="custom-control-label" for="is_collection">
                                    Is Collection
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_rescue" name="is_rescue" value="1" @if(old('is_rescue', $dragon->is_rescue)) checked @endif>
                                <label class="custom-control-label" for="is_rescue">
                                    Is Rescue
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('dragons.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
