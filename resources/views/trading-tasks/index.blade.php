@extends('layouts.app')

@section('title', 'Trading Task')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title"><h5>Trading Task</h5></div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item active">Trading Task</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h5>Trading Task List</h5>
        <a href="{{ route('trading-tasks.create') }}" class="btn btn-success">Add Trading Task</a>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Dragon</th>
                    <th>Trader</th>
                    <th>Reciever</th>
                    <th>Jumlah Orb</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tradingTasks as $task)
                    <tr>
                        <td>{{ $task->dragon->dragon_name ?? '-' }}</td>
                        <td>{{ $task->trader->account_name ?? '-' }}</td>
                        <td>{{ $task->reciever->account_name ?? '-' }}</td>
                        <td>{{ $task->jumlah_orb }}</td>
                        <td>
                            <span class="badge badge-{{ $task->status_trade === 'done' ? 'success' : ($task->status_trade === 'ready' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($task->status_trade) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('trading-tasks.edit', $task) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('trading-tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this trading task?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No trading tasks found.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $tradingTasks->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection