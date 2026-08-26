@extends('layouts.app')

@section('title', 'Collections')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Collections</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Master Data</a></li>
                    <li class="breadcrumb-item active">Collections</li>
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

@if(session('error'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger">{{ session('error') }}</div>
        </div>
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-12">
        <div class="d-flex flex-wrap align-items-center">
            <a href="{{ route('collections.create') }}" class="btn btn-success mr-2 mb-2" style="background: #2e8b57; border-color: #2e8b57;">Add Collection</a>
            <button type="button" class="btn btn-info mr-2 mb-2" data-toggle="modal" data-target="#dragonRewardsModal">
                <i class="fas fa-search"></i> Check Dragon
            </button>
        </div>
    </div>
</div>

<div class="row">
    @forelse($collections as $collection)
        <div class="col-xl-3 col-md-6">
            <div class="card prod-p-card bg-c-blue" style="cursor: pointer;" onclick="window.location.href='{{ route('collections.show', $collection) }}'">
                <div class="card-body">
                    <div class="row align-items-center m-b-25">
                        <div class="col">
                            <h6 class="m-b-5 text-white">{{ $collection->collection_name }}</h6>
                            <h3 class="m-b-0 text-white">
                                {{ $collection->total_member }}
                            </h3>
                            <small class="text-white">Dragons</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-th text-c-blue f-18"></i>
                        </div>
                    </div>
                    <div class="row m-b-15">
                        <div class="col-12">
                            <small class="text-white">
                                {{ $collection->gem_reward }} Gems
                            </small>
                            @if($collection->dragonReward)
                                <div class="mt-2">
                                    <small class="text-white">
                                        {{ $collection->dragonReward->dragon_name }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('collections.edit', $collection) }}" class="btn btn-sm btn-light btn-block" onclick="event.stopPropagation();">Edit</a>
                        <form action="{{ route('collections.destroy', $collection) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure?'); event.stopPropagation();">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-block">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted">No collections found.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Dragon Rewards Modal -->
<div class="modal fade" id="dragonRewardsModal" tabindex="-1" role="dialog" aria-labelledby="dragonRewardsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dragonRewardsModalLabel">Collection Dragon Rewards</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="filterMemberCollection">Member Collection:</label>
                        <select id="filterMemberCollection" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="filterStatus">Status:</label>
                        <select id="filterStatus" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="owned">Owned</option>
                            <option value="not-owned">Not Owned</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Dragon Book</th>
                                <th>Dragon Name</th>
                                <th>Rarity</th>
                                <th>Collection</th>
                                <th>Member Collection</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="dragonRewardsTableBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let dragonRewardsData = [];

    function renderTable(data) {
        const tbody = $('#dragonRewardsTableBody');
        tbody.empty();

        if (data.length === 0) {
            tbody.append('<tr><td colspan="6" class="text-center text-muted">No dragon rewards found</td></tr>');
            return;
        }

        data.forEach(function(dragon) {
            const statusBadge = dragon.is_owned 
                ? '<span class="badge badge-success">Owned</span>' 
                : '<span class="badge badge-danger">Not Owned</span>';
            
            const memberBadge = dragon.is_member
                ? '<span class="badge badge-info">Yes</span>'
                : '<span class="badge badge-secondary">No</span>';
            
            const row = $('<tr>')
                .css('background-color', dragon.is_owned ? '#d4edda' : '#f8d7da');
            
            row.append('<td>' + (dragon.dragon_book || '-') + '</td>');
            row.append('<td>' + dragon.dragon_name + '</td>');
            row.append('<td>' + dragon.rarity + '</td>');
            row.append('<td>' + dragon.collection_name + '</td>');
            row.append('<td>' + memberBadge + '</td>');
            row.append('<td>' + statusBadge + '</td>');
            
            tbody.append(row);
        });
    }

    function applyFilters() {
        const memberFilter = $('#filterMemberCollection').val();
        const statusFilter = $('#filterStatus').val();

        let filteredData = dragonRewardsData;

        if (memberFilter) {
            filteredData = filteredData.filter(function(dragon) {
                return memberFilter === 'yes' ? dragon.is_member : !dragon.is_member;
            });
        }

        if (statusFilter) {
            filteredData = filteredData.filter(function(dragon) {
                return statusFilter === 'owned' ? dragon.is_owned : !dragon.is_owned;
            });
        }

        renderTable(filteredData);
    }

    $('#dragonRewardsModal').on('show.bs.modal', function() {
        $.ajax({
            url: '{{ route('collections.dragon-rewards') }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                dragonRewardsData = response.data;
                
                // Reset filters
                $('#filterMemberCollection').val('');
                $('#filterStatus').val('');
                
                renderTable(dragonRewardsData);
            },
            error: function() {
                $('#dragonRewardsTableBody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    });

    // Filter change event listeners
    $('#filterMemberCollection, #filterStatus').on('change', function() {
        applyFilters();
    });
</script>
@endpush
@endsection
