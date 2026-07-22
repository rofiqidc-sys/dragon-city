@extends('layouts.app')

@section('title', 'Orb Owning')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Orb Owning</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">InGame Data</a></li>
                    <li class="breadcrumb-item active">Orb Owning</li>
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

<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h5>Orb Owning List</h5>
        <a href="{{ route('orb-ownings.create') }}" class="btn btn-success">Add Orb Owning</a>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Account</th>
                                <th>Dragon</th>
                                <th>Jumlah Orb</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orbOwnings as $orbOwning)
                                <tr>
                                    <td>{{ $orbOwning->id }}</td>
                                    <td>{{ $orbOwning->account->account_name }}</td>
                                    <td>{{ $orbOwning->dragon->dragon_name }}</td>
                                    <td>{{ $orbOwning->jumlah_orb }}</td>
                                    <td>
                                        <a href="{{ route('orb-ownings.edit', $orbOwning) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('orb-ownings.destroy', $orbOwning) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this orb owning entry?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No orb owning entries found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
