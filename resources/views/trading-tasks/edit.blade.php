@extends('layouts.app')

@section('title', 'Edit Trading Task')

@section('content')
<div class="page-header"><div class="page-block"><div class="row"><div class="col-md-12"><div class="page-header-title"><h5>Edit Trading Task</h5></div><ul class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('trading-tasks.index') }}">Trading Task</a></li><li class="breadcrumb-item active">Edit</li></ul></div></div></div></div>
<div class="row"><div class="col-md-8"><div class="card"><div class="card-body">
    <form action="{{ route('trading-tasks.update', $tradingTask) }}" method="POST">
        @csrf
        @method('PUT')
        @include('trading-tasks.form', ['task' => $tradingTask])
        <button type="submit" class="btn btn-warning">Update</button>
        <a href="{{ route('trading-tasks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div></div></div></div>
@endsection