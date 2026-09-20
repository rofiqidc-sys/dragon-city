@extends('layouts.app')

@section('title', 'Dragon Collection Belum Dimiliki')

@section('content')
<style>
    #unowned-members-table tbody tr.account-comparison-owned > td {
        background-color: #49A4BB !important;
    }
</style>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Dragon Collection Belum Dimiliki</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('collections.index') }}">Collections</a></li>
                    <li class="breadcrumb-item active">Unowned Members</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mb-2 mb-md-0">Member Collection yang Belum Dimiliki Account #1</h5>
                <a href="{{ route('collections.index') }}" class="btn btn-secondary btn-sm">Kembali ke Collections</a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('collections.unowned-members') }}" class="row align-items-end mb-4">
                    <div class="col-md-4">
                        <label for="rarity" class="form-label">Filter Rarity</label>
                        <select id="rarity" name="rarity" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Rarity --</option>
                            @foreach($rarities as $rarity)
                                <option value="{{ $rarity->id }}" {{ (string) $selectedRarity === (string) $rarity->id ? 'selected' : '' }}>{{ $rarity->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="account_id" class="form-label">Bandingkan Account</label>
                        <select id="account_id" name="account_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Pilih Account --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ (string) $selectedAccountId === (string) $account->id ? 'selected' : '' }}>{{ $account->account_name }} (ID {{ $account->id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="is_rescue" class="form-label">Filter Is Rescue</label>
                        <select id="is_rescue" name="is_rescue" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Rescue Status --</option>
                            <option value="1" {{ (string) $selectedIsRescue === '1' ? 'selected' : '' }}>Rescue</option>
                            <option value="0" {{ (string) $selectedIsRescue === '0' ? 'selected' : '' }}>Bukan Rescue</option>
                        </select>
                    </div>
                </form>

                <div class="table-responsive">
                    <table id="unowned-members-table" class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Dragon Name</th>
                                <th>Dragon Book</th>
                                <th>Rarity</th>
                                <th>Orb Account #1</th>
                                <th>Orb Account Pembanding</th>
                                <th>Collection</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dragons as $index => $dragon)
                                <tr class="{{ $dragon->is_owned_by_selected_account ? 'account-comparison-owned' : '' }}">
                                    <td data-order="{{ $dragons->firstItem() + $index }}">{{ $dragons->firstItem() + $index }}</td>
                                    <td>{{ $dragon->dragon_name }}</td>
                                    <td>{{ $dragon->dragon_book ?? '-' }}</td>
                                    <td>{{ $dragon->rarity->name ?? '-' }}</td>
                                    <td data-order="{{ $dragon->jumlah_orb_account_one }}" style="min-width: 150px;">
                                        <input
                                            type="number"
                                            class="form-control form-control-sm jumlah-orb-account-one"
                                            value="{{ $dragon->jumlah_orb_account_one }}"
                                            data-dragon-id="{{ $dragon->id }}"
                                            data-original-value="{{ $dragon->jumlah_orb_account_one }}"
                                            min="0"
                                            step="1"
                                            aria-label="Jumlah orb {{ $dragon->dragon_name }}"
                                        >
                                        <small class="orb-save-status text-muted d-block"></small>
                                    </td>
                                    <td data-order="{{ $dragon->jumlah_orb_selected_account }}" style="min-width: 150px;">
                                        <input
                                            type="number"
                                            class="form-control form-control-sm jumlah-orb-account-selected"
                                            value="{{ $dragon->jumlah_orb_selected_account }}"
                                            data-dragon-id="{{ $dragon->id }}"
                                            data-account-id="{{ $selectedAccountId }}"
                                            data-original-value="{{ $dragon->jumlah_orb_selected_account }}"
                                            min="0"
                                            step="1"
                                            aria-label="Jumlah orb pembanding {{ $dragon->dragon_name }}"
                                            {{ !$selectedAccountId || (int) $selectedAccountId === 1 ? 'disabled' : '' }}
                                        >
                                        <small class="orb-save-status text-muted d-block"></small>
                                    </td>
                                    <td>{{ $dragon->collections->pluck('collection_name')->join(', ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Tidak ada dragon yang sesuai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $dragons->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function () {
        $('#unowned-members-table').DataTable({
            paging: false,
            searching: false,
            info: false,
            autoWidth: false,
            order: [[1, 'asc']],
            columnDefs: [
                { type: 'num', targets: [0, 4, 5] }
            ],
            language: {
                emptyTable: 'Tidak ada dragon yang sesuai.'
            }
        });
    });

    $(document).on('change', '.jumlah-orb-account-one, .jumlah-orb-account-selected', function () {
        const input = $(this);
        const status = input.siblings('.orb-save-status');
        const jumlahOrb = Number.parseInt(input.val(), 10);

        if (!Number.isInteger(jumlahOrb) || jumlahOrb < 0) {
            input.val(input.data('original-value') || 0);
            status.text('Nilai tidak valid.').removeClass('text-success').addClass('text-danger');
            return;
        }

        input.prop('disabled', true);
        status.text('Menyimpan...').removeClass('text-danger text-success').addClass('text-muted');

        $.ajax({
            url: '{{ route('orb-ownings.upsert') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            data: {
                account_id: input.hasClass('jumlah-orb-account-one') ? 1 : input.data('account-id'),
                dragon_id: input.data('dragon-id'),
                jumlah_orb: jumlahOrb
            }
        }).done(function (response) {
            const savedValue = response.data?.jumlah_orb ?? jumlahOrb;
            input.val(savedValue).data('original-value', savedValue);
            status.text('Tersimpan.').removeClass('text-muted text-danger').addClass('text-success');
        }).fail(function () {
            input.val(input.data('original-value') || 0);
            status.text('Gagal menyimpan.').removeClass('text-muted text-success').addClass('text-danger');
        }).always(function () {
            input.prop('disabled', false);
        });
    });
</script>
@endpush
