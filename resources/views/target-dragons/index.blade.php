@extends('layouts.app')

@section('title', 'Target Dragon')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Target Dragon</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item active">Target Dragon</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0" style="background: #ffffff;">
            <div class="card-header" style="background: linear-gradient(145deg, #73EC8B 0%, #06D001 100%); border-radius: .5rem; color: #ffffff; border: none;">
                <h5 class="mb-1" style="font-weight: 700;">Target Dragon</h5>
                <p class="mb-0" style="opacity: .9;">Daftar dragon yang tidak dimiliki account ID 1.</p>
            </div>
            <div class="card-body" style="background: #ffffff; color: #1f2d20;">
                <form method="GET" action="{{ route('target-dragons.index') }}" class="row gx-2 align-items-end mb-4">
                    <div class="col-md-4">
                        <label for="account" class="form-label">Filter Account</label>
                        <select id="account" name="account" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Account --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ (string) $selectedAccount === (string) $account->id ? 'selected' : '' }}>{{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="rarity" class="form-label">Filter Rarity</label>
                        <select id="rarity" name="rarity" class="form-control" onchange="this.form.submit()" aria-label="Filter rarity">
                            <option value="">-- Semua Rarity --</option>
                            @foreach($rarities as $rarity)
                                <option value="{{ $rarity->id }}" {{ (string) $selectedRarity === (string) $rarity->id ? 'selected' : '' }}>{{ $rarity->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="is_rescue" class="form-label">Filter Rescue</label>
                        <select id="is_rescue" name="is_rescue" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            <option value="1" {{ (string) $selectedIsRescue === '1' ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ (string) $selectedIsRescue === '0' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="is_collection" class="form-label">Filter Collection</label>
                        <select id="is_collection" name="is_collection" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            <option value="1" {{ (string) $selectedIsCollection === '1' ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ (string) $selectedIsCollection === '0' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                </form>

                <div class="table-responsive">
                    <table id="target-dragons-table" class="table table-hover table-striped table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Dragon</th>
                                <th>Rarity</th>
                                <th>Rescue</th>
                                <th>Collection</th>
                                <th>Pemilik</th>
                                <th>Jumlah Orb</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dragons as $dragon)
                                <tr>
                                    <td>{{ $dragon->dragon_name }}</td>
                                    <td>{{ $dragon->rarity->name ?? '-' }}</td>
                                    <td>{{ $dragon->is_rescue ? 'Ya' : 'Tidak' }}</td>
                                    <td>{{ $dragon->is_collection ? 'Ya' : 'Tidak' }}</td>
                                    <td>{{ $dragon->target_owners ?: '-' }}</td>
                                    <td>{{ $dragon->target_jumlah_orb }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
        $('#target-dragons-table').DataTable({
            autoWidth: false,
            pageLength: 15,
            language: {
                search: 'Cari:',
                searchPlaceholder: 'Cari dragon...',
                emptyTable: 'Tidak ada dragon target yang cocok dengan filter saat ini.',
                zeroRecords: 'Dragon yang dicari tidak ditemukan.'
            }
        });
    });
</script>
@endpush
