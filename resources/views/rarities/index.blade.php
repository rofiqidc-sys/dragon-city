@extends('layouts.app')

@section('title', 'Rarities')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Rarities</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Master Data</a></li>
                    <li class="breadcrumb-item active">Rarity</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Rarity List</h5>
                <div class="d-flex gap-2">
                    <form action="{{ route('rarities.update-seeder') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Update RaritySeeder file based on current database data?')">Update Seeder</button>
                    </form>
                    <a href="{{ route('rarities.create') }}" class="btn btn-primary">Add Rarity</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Alias</th>
                                <th>Key Need To Summon</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rarities as $index => $rarity)
                                <tr class="{{ $index % 2 === 0 ? 'table-active' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $rarity->name }}</td>
                                    <td>{{ $rarity->alias }}</td>
                                    <td>{{ $rarity->key_need_to_summon }}</td>
                                    <td>
                                        <a href="{{ route('rarities.edit', $rarity) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('rarities.destroy', $rarity) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this rarity?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="table-info">
                                    <td colspan="5" class="text-center">No rarities found.</td>
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
