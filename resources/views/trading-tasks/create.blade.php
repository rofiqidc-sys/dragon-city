@extends('layouts.app')

@section('title', 'Add Trading Task')

@section('content')
<div class="page-header"><div class="page-block"><div class="row"><div class="col-md-12"><div class="page-header-title"><h5>Add Trading Task</h5></div><ul class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('trading-tasks.index') }}">Trading Task</a></li><li class="breadcrumb-item active">Add</li></ul></div></div></div></div>
<div class="row"><div class="col-md-8"><div class="card"><div class="card-body">
    <form action="{{ route('trading-tasks.store') }}" method="POST">
        @csrf
        @include('trading-tasks.form')
        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('trading-tasks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div></div></div>
@endsection