@extends('layouts.app')

@section('title', 'Unowned Dragon')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Unowned Dragon</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item active">Unowned Dragon</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1">Dragon yang Belum Dimiliki Akun Manapun</h5>
                <p class="mb-0 text-muted">Daftar ini berasal dari tabel unowned_dragons dan diperbarui saat kepemilikan dragon berubah.</p>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('unowned-dragons.index') }}" class="row align-items-end mb-4">
                    <div class="col-md-4 mb-3">
                        <label for="rarity" class="form-label">Filter Rarity</label>
                        <select id="rarity" name="rarity" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Rarity --</option>
                            @foreach($rarities as $rarity)
                                <option value="{{ $rarity->id }}" {{ (string) $selectedRarity === (string) $rarity->id ? 'selected' : '' }}>{{ $rarity->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="is_rescue" class="form-label">Filter Is Rescue</label>
                        <select id="is_rescue" name="is_rescue" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            <option value="1" {{ (string) $selectedIsRescue === '1' ? 'selected' : '' }}>Rescue</option>
                            <option value="0" {{ (string) $selectedIsRescue === '0' ? 'selected' : '' }}>Bukan Rescue</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="is_collection" class="form-label">Filter Is Collection</label>
                        <select id="is_collection" name="is_collection" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua --</option>
                            <option value="1" {{ (string) $selectedIsCollection === '1' ? 'selected' : '' }}>Collection</option>
                            <option value="0" {{ (string) $selectedIsCollection === '0' ? 'selected' : '' }}>Bukan Collection</option>
                        </select>
                    </div>
                </form>

                <p class="text-muted">Total: {{ $unownedDragons->total() }} dragon</p>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dragon Name</th>
                                <th>Dragon Book</th>
                                <th>Rarity</th>
                                <th>Elements</th>
                                <th>Is Rescue</th>
                                <th>Is Collection</th>
                                <th style="width: 60px;">Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unownedDragons as $index => $unownedDragon)
                                @php($dragon = $unownedDragon->dragon)
                                <tr>
                                    <td>{{ $unownedDragons->firstItem() + $index }}</td>
                                    <td>{{ $dragon->dragon_name }}</td>
                                    <td>{{ $dragon->dragon_book ?? '-' }}</td>
                                    <td>{{ $dragon->rarity->name ?? '-' }}</td>
                                    <td>{{ collect([$dragon->element1, $dragon->element2, $dragon->element3, $dragon->element4])->filter()->pluck('name')->join(' / ') ?: '-' }}</td>
                                    <td>{{ $dragon->is_rescue ? 'Ya' : 'Tidak' }}</td>
                                    <td>{{ $dragon->is_collection ? 'Ya' : 'Tidak' }}</td>
                                    <td class="text-center align-middle">
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary btn-sm info-orb-button"
                                            data-toggle="modal"
                                            data-target="#orbInfoModal"
                                            data-dragon-name="{{ $dragon->dragon_name }}"
                                            data-orb-summary='@json($dragon->orb_details_by_account ?? [])'
                                            aria-label="Info jumlah orb {{ $dragon->dragon_name }}"
                                        >
                                            <strong>i</strong>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Tidak ada dragon yang sesuai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $unownedDragons->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="orbInfoModal" tabindex="-1" role="dialog" aria-labelledby="orbInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orbInfoModalLabel">Info Orb</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="orbInfoModalContent" class="text-left"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function renderOrbSummaryModal(summary, dragonName) {
        let rows = [];

        if (Array.isArray(summary) && summary.length) {
            rows = summary.map(function (item) {
                return '<div class="d-flex justify-content-between py-1 border-bottom"><span>' + (item.account_name || 'Account') + '</span><strong>' + (item.jumlah_orb ?? 0) + '</strong></div>';
            });
        }

        if (!rows.length) {
            rows.push('<div class="text-muted">Belum ada data orb.</div>');
        }

        $('#orbInfoModalLabel').text('Orb ' + dragonName);
        $('#orbInfoModalContent').html('<div style="min-width: 220px;">' + rows.join('') + '</div>');
    }

    $(function () {
        $('.info-orb-button').on('click', function () {
            const dragonName = $(this).data('dragon-name') || 'Dragon';
            const rawSummary = $(this).data('orb-summary');
            let summary = [];

            if (typeof rawSummary === 'string') {
                try {
                    summary = JSON.parse(rawSummary);
                } catch (error) {
                    summary = [];
                }
            } else if (Array.isArray(rawSummary)) {
                summary = rawSummary;
            }

            renderOrbSummaryModal(summary, dragonName);
        });
    });
</script>
@endpush
