@extends('layouts.app')

@section('title', $collection->collection_name)

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Collection Members</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('collections.index') }}">Collections</a></li>
                    <li class="breadcrumb-item active">{{ $collection->collection_name }}</li>
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

<div class="row mb-3">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5>{{ $collection->collection_name }}</h5>
                    </div>
                    <div class="col-auto">
                        <small class="text-muted">
                            <i class="fas fa-gem"></i> {{ $collection->gem_reward }} Gems
                            @if($collection->dragonReward)
                                | <i class="fas fa-dragon"></i> {{ $collection->dragonReward->dragon_name }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form id="add-dragon-form" action="{{ route('collections.add-dragon', $collection) }}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <label for="dragon_search">Add Dragon</label>
                            <input id="dragon_search" type="text" class="form-control" placeholder="Search dragon by name or book..." autocomplete="off" {{ $allDragons->isEmpty() ? 'disabled' : '' }}>
                            <input id="dragon_id" type="hidden" name="dragon_id" required>
                        </div>
                        <div class="col-md-3">
                            <div class="mt-2" aria-label="Dragon number pad">
                                <label>Dragon Book Number Pad</label>
                                <div class="row no-gutters" style="max-width: 300px;">
                                    @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9] as $number)
                                        <div class="col-4 p-1">
                                            <button type="button" class="btn btn-outline-secondary btn-lg btn-block dragon-pad-key" data-key="{{ $number }}" aria-label="Enter {{ $number }}" {{ $allDragons->isEmpty() ? 'disabled' : '' }}>{{ $number }}</button>
                                        </div>
                                    @endforeach
                                    <div class="col-4 p-1">
                                        <button type="button" class="btn btn-outline-danger btn-lg btn-block dragon-pad-key" data-action="clear" aria-label="Clear dragon input" {{ $allDragons->isEmpty() ? 'disabled' : '' }}>C</button>
                                    </div>
                                    <div class="col-4 p-1">
                                        <button type="button" class="btn btn-outline-secondary btn-lg btn-block dragon-pad-key" data-key="0" aria-label="Enter 0" {{ $allDragons->isEmpty() ? 'disabled' : '' }}>0</button>
                                    </div>
                                    <div class="col-4 p-1">
                                        <button type="button" class="btn btn-outline-warning btn-lg btn-block dragon-pad-key" data-action="backspace" aria-label="Delete last digit" {{ $allDragons->isEmpty() ? 'disabled' : '' }}>Del</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button id="submit-add-dragon" type="submit" class="btn btn-primary btn-block" {{ $allDragons->isEmpty() ? 'disabled' : '' }}>Add Dragon</button>
                        </div>
                    </div>
                </form>
                <div id="collection-dragons-toast-container"></div>
                @if($allDragons->isEmpty())
                    <div class="mt-3 text-muted">All dragons are already in this collection.</div>
                @else
                    <div class="mt-2 text-muted">Type dragon name or book number, then select from autocomplete results.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="collection-dragons-table" class="table table-hover table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dragon Book</th>
                                <th>Dragon Name</th>
                                <th>Rarity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-12">
        <a href="{{ route('collections.index') }}" class="btn btn-secondary">Back to Collections</a>
        <a href="{{ route('collections.edit', $collection) }}" class="btn btn-primary">Edit Collection</a>
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

            $('#collection-dragons-toast-container').append(toast);
            setTimeout(() => toast.alert('close'), 3000);
        }

        $(document).ready(function () {
            const table = $('#collection-dragons-table').DataTable({
                ajax: '{{ route('collections.data', $collection) }}',
                columns: [
                    { data: 'dragon_id' },
                    { data: 'dragon_book' },
                    { data: 'dragon_name' },
                    { data: 'rarity' },
                    { 
                        data: null,
                        render: function(data, type, row) {
                            return '<form action="/collections/{{ $collection->id }}/remove-dragon/' + row.dragon_id + '" method="POST" style="display:inline;" onsubmit="return confirm(\'Remove this dragon?\');">' +
                                '@csrf @method("DELETE")' +
                                '<button type="submit" class="btn btn-sm btn-danger">Remove</button>' +
                                '</form>';
                        }
                    }
                ],
                responsive: true,
                autoWidth: false,
            });

            $('#dragon_search').autocomplete({
                source(request, response) {
                    $.getJSON('{{ route('dragons.search') }}', { term: request.term, exclude_collection: '{{ $collection->id }}' }, response);
                },
                minLength: 2,
                select(event, ui) {
                    $('#dragon_id').val(ui.item.id);
                    $('#dragon_search').val(ui.item.value);
                    return false;
                },
            }).autocomplete('instance')._renderItem = function(ul, item) {
                return $('<li>')
                    .append('<div><strong>' + item.label + '</strong><br/><small>Book: ' + (item.book || '-') + '</small></div>')
                    .appendTo(ul);
            };

            $('#dragon_search').on('input', function () {
                $('#dragon_id').val('');
            });

            $('.dragon-pad-key').on('click', function () {
                const input = $('#dragon_search');
                const action = $(this).data('action');
                let value = input.val();

                if (action === 'clear') {
                    value = '';
                } else if (action === 'backspace') {
                    value = value.slice(0, -1);
                } else {
                    value += String($(this).data('key'));
                }

                input.val(value).trigger('input').focus();
                input.autocomplete('search', value);
            });

            $('#add-dragon-form').on('submit', function(e) {
                e.preventDefault();
                
                if (!$('#dragon_id').val()) {
                    showToast('Please select a dragon', false);
                    return;
                }

                $.post(this.action, $(this).serialize(), function() {
                    table.ajax.reload();
                    $('#dragon_search').val('');
                    $('#dragon_id').val('');
                    showToast('Dragon added successfully');
                }).fail(function() {
                    showToast('Error adding dragon', false);
                });
            });
        });
    </script>
@endpush
@endsection
