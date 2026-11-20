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
    <div class="card-body">
        <div class="table-responsive">
            <table id="trading-tasks-table" class="table table-hover table-striped table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Dragon</th>
                        <th>Rarity</th>
                        <th>Rescue</th>
                        <th>Collection</th>
                        <th>Trader</th>
                        <th>Reciever</th>
                        <th>Jumlah Orb</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tradingTasks as $task)
                        <tr>
                            <td>{{ $task->dragon->dragon_name ?? '-' }}</td>
                            <td>{{ $task->dragon->rarity->name ?? '-' }}</td>
                            <td>{{ $task->dragon->is_rescue ? 'Ya' : 'Tidak' }}</td>
                            <td>{{ $task->dragon->is_collection ? 'Ya' : 'Tidak' }}</td>
                            <td>{{ $task->trader->account_name ?? '-' }}</td>
                            <td>{{ $task->reciever->account_name ?? '-' }}</td>
                            <td>{{ $task->jumlah_orb }}</td>
                            <td>
                                <span class="badge badge-{{ $task->status_trade === 'done' ? 'success' : ($task->status_trade === 'ready' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($task->status_trade) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('trading-tasks.add-orb', $task) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info" title="Tambahkan 1 orb ke trader">+ Orb</button>
                                </form>
                                <a href="{{ route('trading-tasks.edit', $task) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('trading-tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this trading task?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function () {
        $('#trading-tasks-table').DataTable({
            autoWidth: false,
            pageLength: 15,
            columnDefs: [
                { orderable: false, searchable: false, targets: 8 }
            ],
            language: {
                search: 'Cari:',
                searchPlaceholder: 'Cari trading task...',
                emptyTable: 'Belum ada trading task.',
                zeroRecords: 'Trading task yang dicari tidak ditemukan.'
            }
        });
    });
</script>
@endpush
