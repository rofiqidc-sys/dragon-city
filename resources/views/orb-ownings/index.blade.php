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

<div class="row mb-3">
    <div class="col-md-8">
        <form method="GET" action="{{ route('orb-ownings.index') }}" class="row gx-2">
            <div class="col-md-4">
                <label for="account_id">Pilih Account</label>
                <select id="account_id" name="account_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Account --</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ $selectedAccountId == $account->id ? 'selected' : '' }}>{{ $account->account_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="rarity_id">Pilih Rarity</label>
                <select id="rarity_id" name="rarity_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Rarity --</option>
                    @foreach($rarities as $rarity)
                        <option value="{{ $rarity->id }}" {{ $selectedRarityId == $rarity->id ? 'selected' : '' }}>{{ $rarity->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="owned_status">Status Owned</label>
                <select id="owned_status" name="owned_status" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="owned" {{ $selectedOwnershipStatus === 'owned' ? 'selected' : '' }}>Sudah Dimiliki</option>
                    <option value="not_owned" {{ $selectedOwnershipStatus === 'not_owned' ? 'selected' : '' }}>Belum Dimiliki</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div id="orb-ownings-toast-container"></div>
                    <table id="orb-ownings-table" class="table table-hover table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dragon</th>
                                <th>Summon Time</th>
                                <th>Rarity</th>
                                <th>Jumlah Orb</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dragons as $dragon)
                                <tr data-owned="{{ $dragon->owned ? 1 : 0 }}">
                                    <td>{{ $dragon->id }}</td>
                                    <td>{{ $dragon->dragon_name }}</td>
                                    <td>{{ $dragon->summon_time ?? '-' }}</td>
                                    <td>{{ $dragon->rarity_name ?? '-' }}</td>
                                    <td data-order="{{ $dragon->jumlah_orb }}">
                                        <input
                                            type="number"
                                            min="0"
                                            class="form-control jumlah-orb-input"
                                            data-dragon-id="{{ $dragon->id }}"
                                            value="{{ $dragon->jumlah_orb }}"
                                            {{ $selectedAccountId ? '' : 'disabled' }}
                                        >
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No dragons found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        #orb-ownings-table tbody tr[data-owned="1"] > td {
            background-color: #a1f4b4 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        const orbUpsertUrl = "{{ route('orb-ownings.upsert') }}";
        const selectedAccountId = "{{ $selectedAccountId ?? '' }}";

        function refreshInputState() {
            $('.jumlah-orb-input').prop('disabled', !selectedAccountId);
        }

        function showToast(message, success = true) {
            const type = success ? 'success' : 'danger';
            const toast = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">'
                + message
                + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                + '</div>');

            $('#orb-ownings-toast-container').append(toast);
            setTimeout(() => toast.alert('close'), 3000);
        }

        $(document).ready(function () {
            const orbTable = $('#orb-ownings-table').DataTable({
                responsive: true,
                autoWidth: false,
                columnDefs: [
                    { orderable: true, targets: 3 }
                ],
                createdRow(row) {
                    const owned = $(row).data('owned');
                    if (owned === 1 || owned === '1') {
                        $(row).addClass('table-success');
                        $(row).find('td').css('background-color', '#d4edda');
                    }
                }
            });

            function highlightOwnedRows() {
                $('#orb-ownings-table tbody tr').each(function () {
                    const owned = $(this).data('owned');
                    if (owned === 1 || owned === '1') {
                        $(this).addClass('table-success');
                        $(this).find('td').css('background-color', '#d4edda');
                    }
                });
            }

            highlightOwnedRows();
            orbTable.on('draw', highlightOwnedRows);

            refreshInputState();

            $(document).on('change', '.jumlah-orb-input', function () {
                if (!selectedAccountId) {
                    showToast('Silakan pilih account terlebih dahulu.', false);
                    $(this).val($(this).data('original-value') || 0);
                    return;
                }

                const dragonId = $(this).data('dragon-id');
                const jumlahOrb = parseInt($(this).val(), 10);
                const input = $(this);

                if (isNaN(jumlahOrb) || jumlahOrb < 0) {
                    showToast('Jumlah orb harus berupa angka 0 atau lebih.', false);
                    input.val(input.data('original-value') || 0);
                    return;
                }

                $.ajax({
                    url: orbUpsertUrl,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        account_id: selectedAccountId,
                        dragon_id: dragonId,
                        jumlah_orb: jumlahOrb,
                    },
                    success(response) {
                        if (response.success) {
                            input.data('original-value', response.data.jumlah_orb);
                            showToast('Jumlah orb tersimpan.');
                        } else {
                            showToast('Gagal menyimpan orb.', false);
                        }
                    },
                    error(xhr) {
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan.';
                        showToast(message, false);
                    }
                });
            });
        });
    </script>
@endpush
@endsection
