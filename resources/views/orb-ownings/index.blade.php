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
            <div class="col-md-3">
                <label for="account_id">Pilih Account</label>
                <select id="account_id" name="account_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Account --</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ $selectedAccountId == $account->id ? 'selected' : '' }}>{{ $account->account_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="rarity_id">Pilih Rarity</label>
                <select id="rarity_id" name="rarity_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Rarity --</option>
                    @foreach($rarities as $rarity)
                        <option value="{{ $rarity->id }}" {{ $selectedRarityId == $rarity->id ? 'selected' : '' }}>{{ $rarity->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="owned_status">Status Owned</label>
                <select id="owned_status" name="owned_status" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="owned" {{ $selectedOwnershipStatus === 'owned' ? 'selected' : '' }}>Sudah Dimiliki</option>
                    <option value="not_owned" {{ $selectedOwnershipStatus === 'not_owned' ? 'selected' : '' }}>Belum Dimiliki</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="account_one_owned_status">Kepemilikan Account ID 1</label>
                <select id="account_one_owned_status" name="account_one_owned_status" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="owned" {{ $selectedAccountOneOwnershipStatus === 'owned' ? 'selected' : '' }}>Sudah Dimiliki</option>
                    <option value="not_owned" {{ $selectedAccountOneOwnershipStatus === 'not_owned' ? 'selected' : '' }}>Belum Dimiliki</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="is_rescue">Is Rescue</label>
                <select id="is_rescue" name="is_rescue" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Rescue Status --</option>
                    <option value="yes" {{ $selectedRescueStatus === 'yes' ? 'selected' : '' }}>Rescue</option>
                    <option value="no" {{ $selectedRescueStatus === 'no' ? 'selected' : '' }}>Bukan Rescue</option>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dragons as $dragon)
                                <tr
                                    data-owned="{{ $dragon->owned ? 1 : 0 }}"
                                    data-orb-to-summon="{{ $dragon->orb_to_summon ?? 0 }}"
                                    data-jumlah-orb="{{ $dragon->jumlah_orb }}"
                                >
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
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary add-dragon-owning"
                                            data-dragon-id="{{ $dragon->id }}"
                                            {{ !$selectedAccountId || $dragon->owned ? 'disabled' : '' }}
                                            title="Tambahkan dragon ke account terpilih"
                                        >+</button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-info check-dragon-owners"
                                            data-dragon-id="{{ $dragon->id }}"
                                            title="Cek account lain yang memiliki dragon ini"
                                        >Cek</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No dragons found.</td>
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
        #orb-ownings-table tbody tr.orb-status-owned > td { background-color: #06D6A0 !important; }
        #orb-ownings-table tbody tr.orb-status-over-20 > td { background-color: #FFD166 !important; }
        #orb-ownings-table tbody tr.orb-status-under-20 > td { background-color: #118AB2 !important; }
        #orb-ownings-table tbody tr.orb-status-empty > td { background-color: #FF7F50 !important; }
    </style>
@endpush

<div class="modal fade" id="dragon-owners-modal" tabindex="-1" role="dialog" aria-labelledby="dragon-owners-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dragon-owners-modal-title">Account Pemilik Dragon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="dragon-owners-modal-body"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        const orbUpsertUrl = "{{ route('orb-ownings.upsert') }}";
        const addDragonOwningUrl = "{{ url('/dragon-owning-details') }}";
        const dragonOwnersUrl = "{{ url('/orb-ownings/dragon') }}";
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

        function applyOrbRowColor(row) {
            const $row = $(row);
            const owned = $row.data('owned') === 1 || $row.data('owned') === '1';
            const orbToSummon = Number($row.data('orb-to-summon')) || 0;
            const jumlahOrb = Number($row.find('.jumlah-orb-input').val() ?? $row.data('jumlah-orb')) || 0;
            const remainingOrb = orbToSummon - jumlahOrb;

            $row.removeClass('orb-status-owned orb-status-over-20 orb-status-under-20 orb-status-empty');

            if (owned) {
                $row.addClass('orb-status-owned');
            } else if (jumlahOrb === 0) {
                $row.addClass('orb-status-empty');
            } else if (remainingOrb > 20) {
                $row.addClass('orb-status-over-20');
            } else {
                $row.addClass('orb-status-under-20');
            }
        }

        $(document).ready(function () {
            const orbTable = $('#orb-ownings-table').DataTable({
                responsive: true,
                autoWidth: false,
                columnDefs: [
                    { orderable: true, targets: 3 }
                ],
                createdRow(row) {
                    applyOrbRowColor(row);
                }
            });

            function highlightOrbRows() {
                $('#orb-ownings-table tbody tr').each(function () {
                    applyOrbRowColor(this);
                });
            }

            highlightOrbRows();
            orbTable.on('draw', highlightOrbRows);

            refreshInputState();

            $(document).on('click', '.check-dragon-owners', function () {
                const dragonId = $(this).data('dragon-id');
                const modalBody = $('#dragon-owners-modal-body');

                modalBody.html('<p class="text-muted mb-0">Memuat data account...</p>');
                $('#dragon-owners-modal').modal('show');

                $.getJSON(dragonOwnersUrl + '/' + dragonId + '/owners', {
                    exclude_account_id: selectedAccountId
                }).done(function (response) {
                    $('#dragon-owners-modal-title').text('Account Pemilik: ' + response.dragon_name);

                    if (!response.owners.length) {
                        modalBody.html('<p class="text-muted mb-0">Dragon belum dimiliki account lain.</p>');
                        return;
                    }

                    const owners = $('<ul class="list-group"></ul>');
                    response.owners.forEach(function (owner) {
                        owners.append('<li class="list-group-item">Account #' + owner.id + ' - ' + $('<div>').text(owner.account_name).html() + '</li>');
                    });
                    modalBody.empty().append(owners);
                }).fail(function () {
                    modalBody.html('<p class="text-danger mb-0">Gagal memuat account pemilik dragon.</p>');
                });
            });

            $(document).on('click', '.add-dragon-owning:not(:disabled)', function () {
                const button = $(this);
                const dragonId = button.data('dragon-id');

                if (!selectedAccountId) {
                    showToast('Silakan pilih account terlebih dahulu.', false);
                    return;
                }

                button.prop('disabled', true);

                $.ajax({
                    url: addDragonOwningUrl + '/' + selectedAccountId,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    data: {
                        dragon_id: dragonId
                    },
                    success(response) {
                        if (response.success) {
                            const row = button.closest('tr');
                            row.attr('data-owned', '1').data('owned', 1);
                            applyOrbRowColor(row);
                            showToast(response.message);
                        } else {
                            button.prop('disabled', false);
                            showToast(response.message || 'Gagal menambahkan dragon.', false);
                        }
                    },
                    error(xhr) {
                        button.prop('disabled', false);
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menambahkan dragon.';
                        showToast(message, false);
                    }
                });
            });

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
                            input.closest('tr').attr('data-jumlah-orb', response.data.jumlah_orb);
                            applyOrbRowColor(input.closest('tr'));
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
