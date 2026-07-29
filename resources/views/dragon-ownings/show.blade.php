@extends('layouts.app')

@section('title', 'Owned Dragons')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Owned Dragons</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dragon-ownings.index') }}">Dragon Owning</a></li>
                    <li class="breadcrumb-item active">{{ $account->account_name }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="alert alert-success">{{ session('success') }}</div>
        </div>
    </div>
@endif

@if(session('warning'))
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="alert alert-warning">{{ session('warning') }}</div>
        </div>
    </div>
@endif

<div class="row mb-3">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form id="add-dragon-form" action="{{ route('dragon-owning-details.store', $account) }}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-9">
                            <label for="dragon_search">Tambah Dragon</label>
                            <input id="dragon_search" type="text" class="form-control" placeholder="Cari dragon berdasarkan nama atau book..." autocomplete="off" {{ $dragons->isEmpty() ? 'disabled' : '' }}>
                            <input id="dragon_id" type="hidden" name="dragon_id" required>
                        </div>
                        <div class="col-md-3">
                            <button id="submit-add-dragon" type="submit" class="btn btn-primary btn-block" {{ $dragons->isEmpty() ? 'disabled' : '' }}>Add Dragon</button>
                        </div>
                    </div>
                </form>
                <div id="dragon-ownings-toast-container"></div>
                @if($dragons->isEmpty())
                    <div class="mt-3 text-muted">Semua dragon sudah dimiliki oleh account ini.</div>
                @else
                    <div class="mt-2 text-muted">Ketik nama atau nomor book dragon, lalu pilih dari hasil autocomplete.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12 col-md-3" style="max-width: 20%;">
        <div class="card">
            <div class="card-body">
                <form id="filter-rarity-form">
                    <div class="row align-items-end">
                        <div class="col-12">
                            <label for="rarity_filter">Filter Owned Dragons by Rarity</label>
                            <select id="rarity_filter" class="form-control">
                                <option value="">-- Semua Rarity --</option>
                                @foreach($rarities as $rarity)
                                    <option value="{{ $rarity->name }}">{{ $rarity->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="owned-dragons-table" class="table table-hover table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dragon Book</th>
                                <th>Dragon Name</th>
                                <th>Rarity</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        function showToast(message, success = true) {
            const type = success ? 'success' : 'danger';
            const toast = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">'
                + message
                + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
                + '</div>');

            $('#dragon-ownings-toast-container').append(toast);
            setTimeout(() => toast.alert('close'), 3000);
        }

        $(document).ready(function () {
            const table = $('#owned-dragons-table').DataTable({
                ajax: '{{ route('dragon-ownings.data', $account) }}',
                columns: [
                    { data: 'dragon_id' },
                    { data: 'dragon_book' },
                    { data: 'dragon_name' },
                    { data: 'rarity' }
                ],
                responsive: true,
                autoWidth: false,
            });

            $('#dragon_search').autocomplete({
                source(request, response) {
                    $.getJSON('{{ route('dragon-ownings.search', $account) }}', { term: request.term }, response);
                },
                minLength: 2,
                select(event, ui) {
                    $('#dragon_id').val(ui.item.id);
                    $('#dragon_search').val(ui.item.value);
                    return false;
                }
            });

            $('#rarity_filter').on('change', function () {
                const value = $(this).val();
                table.column(3).search(value ? '^' + value + '$' : '', true, false).draw();
            });

            $('#add-dragon-form').on('submit', function (event) {
                event.preventDefault();
                const dragonId = $('#dragon_id').val();

                if (!dragonId) {
                    showToast('Pilih dragon dari hasil autocomplete terlebih dahulu.', false);
                    return;
                }

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        dragon_id: dragonId,
                        _token: '{{ csrf_token() }}'
                    },
                    success(response) {
                        if (response.success) {
                            table.row.add(response.data).draw(false);
                            $('#dragon_search').val('');
                            $('#dragon_id').val('');
                            showToast(response.message, true);
                        } else {
                            showToast(response.message, false);
                        }
                    },
                    error(xhr) {
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menambahkan dragon.';
                        showToast(message, false);
                    }
                });
            });
        });
    </script>
@endpush
@endsection