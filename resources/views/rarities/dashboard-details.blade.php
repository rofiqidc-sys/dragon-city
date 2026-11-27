@extends('layouts.app')

@section('title', $rarity->name . ' - ' . $statusLabels[$status])

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>{{ $rarity->name }} - {{ $statusLabels[$status] }}</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ $rarity->name }} - {{ $statusLabels[$status] }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $dragons->count() }} Dragon</h5>
                <a href="{{ route('home') }}" class="btn btn-sm btn-secondary">Kembali ke Dashboard</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="rarity-dragons-table" class="table table-hover" style="table-layout: fixed; width: 100%;">
                        <colgroup>
                            @if($status === 'ongoing_dragons')
                                <col style="width: 18%;">
                                <col style="width: 10%;">
                                <col style="width: 12%;">
                                <col style="width: 45%;">
                                <col style="width: 15%;">
                            @else
                                <col style="width: 30%;">
                                <col style="width: 20%;">
                                <col style="width: 20%;">
                                <col style="width: 30%;">
                            @endif
                        </colgroup>
                        <thead>
                            <tr>
                                <th>Nama Dragon</th>
                                <th>Dragon Book</th>
                                <th>Orb Account 1</th>
                                @if($status === 'ongoing_dragons')
                                    <th>Account Pemilik</th>
                                @endif
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dragons as $dragon)
                        @php
                            $accountOneOrbTotal = (int) ($dragon->account_one_orb_total ?? 0);
                            $orbToSummon = (int) $dragon->orb_to_summon;
                            $orbBackgroundColor = $accountOneOrbTotal === 0
                                ? '#F7D6D0'
                                : ($accountOneOrbTotal < $orbToSummon ? '#FFEDB9' : '#C4F7CA');
                        @endphp
                                <tr style="background-color: {{ $orbBackgroundColor }};">
                                    <td>{{ $dragon->dragon_name }}</td>
                                    <td>{{ $dragon->dragon_book ?: '-' }}</td>
                                    <td data-order="{{ $accountOneOrbTotal }}">
                                        <span class="orb-total">{{ $accountOneOrbTotal }} / {{ $orbToSummon }}</span>
                                    </td>
                                    @if($status === 'ongoing_dragons')
                                        <td>
                                            <div class="d-flex flex-wrap" style="gap: .35rem; max-width: 100%;">
                                            @foreach($dragon->dragonOwningDetails as $owningDetail)
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-info update-orb-button"
                                                    data-dragon-id="{{ $dragon->id }}"
                                                    data-dragon-name="{{ $dragon->dragon_name }}"
                                                    data-account-id="{{ $owningDetail->account_id }}"
                                                    data-account-name="{{ $owningDetail->account->account_name }}"
                                                    data-jumlah-orb="{{ $owningDetail->owner_orb_total }}"
                                                    data-orb-to-summon="{{ $orbToSummon }}"
                                                    style="max-width: 100%; white-space: normal; text-align: left;">
                                                    {{ $owningDetail->account->account_name }}: {{ $owningDetail->owner_orb_total }} orb
                                                </button>
                                            @endforeach
                                            </div>
                                        </td>
                                    @endif
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary update-orb-button"
                                            data-dragon-id="{{ $dragon->id }}"
                                            data-dragon-name="{{ $dragon->dragon_name }}"
                                            data-account-id="1"
                                            data-account-name="Account 1"
                                            data-jumlah-orb="{{ $accountOneOrbTotal }}"
                                            data-orb-to-summon="{{ $orbToSummon }}"
                                            style="width: 100%; min-width: 0; white-space: normal; padding: .25rem;">
                                            Update Orb
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateOrbModal" tabindex="-1" role="dialog" aria-labelledby="updateOrbModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="updateOrbForm" method="POST" action="{{ route('orb-ownings.upsert') }}">
                @csrf
                <input type="hidden" name="account_id" value="1" id="updateOrbAccountId">
                <input type="hidden" name="dragon_id" id="updateOrbDragonId">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateOrbModalLabel">Update Orb</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="updateOrbDragonName" class="font-weight-bold"></p>
                    <div class="form-group">
                        <label for="updateOrbAmount">Jumlah Orb</label>
                        <input type="number" class="form-control" id="updateOrbAmount" name="jumlah_orb" min="0" required>
                    </div>
                    <div id="updateOrbError" class="alert alert-danger d-none mb-0" role="alert"></div>
                    <div id="updateOrbSuccess" class="alert alert-success d-none mb-0" role="status"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="saveOrbButton">Simpan Orb</button>
                </div>
            </form>
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
        $('#rarity-dragons-table').DataTable({
            autoWidth: false,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: 'Cari:',
                searchPlaceholder: 'Cari dragon atau account...',
                lengthMenu: 'Tampilkan _MENU_ dragon',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ dragon',
                emptyTable: 'Tidak ada dragon untuk status ini.',
                zeroRecords: 'Dragon yang dicari tidak ditemukan.'
            }
        });

        $(document).on('click', '.update-orb-button', function () {
            const $button = $(this);

            $('#updateOrbDragonId').val($button.data('dragon-id'));
            $('#updateOrbAccountId').val($button.data('account-id'));
            $('#updateOrbModalLabel').text('Update Orb - ' + $button.data('account-name'));
            $('#updateOrbDragonName').text($button.data('dragon-name'));
            $('#updateOrbAmount').val($button.data('jumlah-orb'));
            $('#updateOrbError').addClass('d-none').text('');
            $('#updateOrbSuccess').addClass('d-none').text('');
            $('#updateOrbModal').modal('show');
        });

        $('#updateOrbForm').on('submit', function (event) {
            event.preventDefault();

            const $form = $(this);
            const $saveButton = $('#saveOrbButton');
            const dragonId = $('#updateOrbDragonId').val();
            const accountId = $('#updateOrbAccountId').val();
            const $rowButton = $('.update-orb-button').filter(function () {
                return String($(this).data('dragon-id')) === String(dragonId)
                    && String($(this).data('account-id')) === String(accountId);
            });
            const orbToSummon = Number($rowButton.data('orb-to-summon')) || 0;

            $saveButton.prop('disabled', true);
            $('#updateOrbError').addClass('d-none').text('');
            $('#updateOrbSuccess').addClass('d-none').text('');

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize()
            }).done(function (response) {
                const $row = $rowButton.closest('tr');
                const updatedOrbTotal = Number(response.data.jumlah_orb);

                $rowButton
                    .attr('data-jumlah-orb', updatedOrbTotal)
                    .data('jumlah-orb', updatedOrbTotal);

                if (String(accountId) === '1') {
                    const $orbCell = $row.find('td').eq(2);
                    const backgroundColor = updatedOrbTotal === 0
                        ? '#F7D6D0'
                        : (updatedOrbTotal < orbToSummon ? '#FFEDB9' : '#C4F7CA');

                    $orbCell.attr('data-order', updatedOrbTotal);
                    $orbCell.find('.orb-total').text(updatedOrbTotal + ' / ' + orbToSummon);
                    $row.css('background-color', backgroundColor);
                } else {
                    $rowButton.text($rowButton.data('account-name') + ': ' + updatedOrbTotal + ' orb');
                }

                $('#rarity-dragons-table').DataTable().row($row).invalidate().draw(false);
                $('#updateOrbSuccess')
                    .text(response.message || 'Jumlah orb berhasil diperbarui.')
                    .removeClass('d-none');
            }).fail(function (xhr) {
                let message = 'Gagal memperbarui jumlah orb.';

                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.jumlah_orb) {
                    message = xhr.responseJSON.errors.jumlah_orb[0];
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                $('#updateOrbError').text(message).removeClass('d-none');
            }).always(function () {
                $saveButton.prop('disabled', false);
            });
        });
    });
</script>
@endpush
