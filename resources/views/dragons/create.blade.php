@extends('layouts.app')

@section('title', 'Create Dragon')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Create Dragon</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dragons.index') }}">Dragons</a></li>
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
                <h5>New Dragon</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dragons.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="dragon_name">Dragon Name <span class="text-danger">*</span></label>
                        <input type="text" name="dragon_name" id="dragon_name" class="form-control" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="rarity_id">Rarity</label>
                            <select name="rarity_id" id="rarity_id" class="form-control">
                                <option value="">No rarity</option>
                                <option value="">-- Select Rarity --</option>
                                @foreach($rarities as $rarity)
                                    <option value="{{ $rarity->id }}">{{ $rarity->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="element_1_id">Element 1 <span class="text-danger">*</span></label>
                            <select name="element_1_id" id="element_1_id" class="form-control" required>
                                <option value="">-- Select Element --</option>
                                @foreach($elements as $element)
                                    <option value="{{ $element->id }}">{{ $element->name }}</option>
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
                                    <option value="{{ $element->id }}">{{ $element->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="element_3_id">Element 3</label>
                            <select name="element_3_id" id="element_3_id" class="form-control">
                                <option value="">-- Select Element --</option>
                                @foreach($elements as $element)
                                    <option value="{{ $element->id }}">{{ $element->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="element_4_id">Element 4</label>
                        <select name="element_4_id" id="element_4_id" class="form-control">
                            <option value="">-- Select Element --</option>
                            @foreach($elements as $element)
                                <option value="{{ $element->id }}">{{ $element->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="summon_time">Summon Time (integer)</label>
                            <input type="number" name="summon_time" id="summon_time" class="form-control" step="1">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="orb_to_summon">Orb To Summon <span class="text-danger">*</span></label>
                            <input type="number" name="orb_to_summon" id="orb_to_summon" class="form-control" value="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="hatching_time">Hatching Time (seconds) <span class="text-danger">*</span></label>
                        <input type="number" name="hatching_time" id="hatching_time" class="form-control" value="0" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_collection" name="is_collection" value="1">
                                <label class="custom-control-label" for="is_collection">
                                    Is Collection
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_rescue" name="is_rescue" value="1">
                                <label class="custom-control-label" for="is_rescue">
                                    Is Rescue
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('dragons.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
