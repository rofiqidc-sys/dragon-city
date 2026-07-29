@extends('layouts.app')

@section('title', 'Elements')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Elements</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Master Data</a></li>
                    <li class="breadcrumb-item active">Elements</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Element List</h5>
                <div class="d-flex gap-2">
                    <form action="{{ route('elements.update-seeder') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Update ElementSeeder file based on current database data?')">Update Seeder</button>
                    </form>
                    <a href="{{ route('elements.create') }}" class="btn btn-primary">Add Element</a>
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($elements as $index => $element)
                                <tr class="{{ $index % 2 === 0 ? 'table-active' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $element->name }}</td>
                                    <td>{{ $element->alias }}</td>
                                    <td>
                                        <a href="{{ route('elements.edit', $element) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('elements.destroy', $element) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this element?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="table-info">
                                    <td colspan="4" class="text-center">No elements found.</td>
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
