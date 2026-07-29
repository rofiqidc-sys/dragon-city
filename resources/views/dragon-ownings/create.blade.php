@extends('layouts.app')

@section('title', 'Add Dragon to Account')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Add Dragon to Account</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dragon-ownings.index') }}">Dragon Owning</a></li>
                    <li class="breadcrumb-item active">Add Dragon</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@php
    $availableDragons = $dragons->filter(function ($dragon) use ($account) {
        return !$account->dragonOwningDetails->contains('dragon_id', $dragon->id);
    })->values();

    $dragonData = $availableDragons->map(function ($dragon) {
        return [
            'id' => $dragon->id,
            'name' => $dragon->dragon_name,
            'dragon_book' => $dragon->dragon_book,
            'label' => $dragon->dragon_name . ' (' . ($dragon->rarity->name ?? '-') . ')',
        ];
    })->values()->all();
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Add Dragon to {{ $account->account_name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('dragon-owning-details.store', $account) }}" method="POST">
                    @csrf
                    <div class="form-group position-relative">
                        <label for="dragon_name">Select Dragon <span class="text-danger">*</span></label>
                        <input type="hidden" id="dragon_id" name="dragon_id" value="{{ old('dragon_id') }}">
                        <input type="text" id="dragon_name" name="dragon_name" class="form-control" placeholder="Type at least 3 characters..." value="{{ old('dragon_name', optional($availableDragons->firstWhere('id', old('dragon_id')))->dragon_name) }}" autocomplete="off" required>
                        <div id="dragon_suggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display: none; max-height: 220px; overflow-y: auto;"></div>
                        <small class="form-text text-muted">Type 3 or more letters to see matching dragons.</small>
                        @error('dragon_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Dragon</button>
                        <a href="{{ route('dragon-ownings.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Account Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Account Name:</strong> {{ $account->account_name }}</p>
                <p><strong>Email:</strong> {{ $account->gmail }}</p>
                <p><strong>Status:</strong> {{ $account->account_status }}</p>
                <p><strong>Current Dragons:</strong> {{ $account->dragonOwningDetails->count() }}</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dragonNameInput = document.getElementById('dragon_name');
        const dragonIdInput = document.getElementById('dragon_id');
        const suggestions = document.getElementById('dragon_suggestions');
        const form = dragonNameInput.closest('form');
        const dragonData = @json($dragonData);

        function clearSuggestions() {
            suggestions.innerHTML = '';
            suggestions.style.display = 'none';
        }

        function fillSuggestions(items) {
            if (items.length === 0) {
                suggestions.innerHTML = '<div class="list-group-item text-muted">No dragons found.</div>';
            } else {
                suggestions.innerHTML = items.map(item =>
                    `<button type="button" class="list-group-item list-group-item-action text-left" data-id="${item.id}" data-name="${item.name}">${item.label}</button>`
                ).join('');
            }
            suggestions.style.display = 'block';
        }

        function updateSuggestions() {
            const query = dragonNameInput.value.trim().toLowerCase();
            if (query.length < 3) {
                dragonIdInput.value = '';
                return clearSuggestions();
            }

            const matches = dragonData
                .filter(item => {
                    const haystack = `${item.name} ${item.label}`.toLowerCase();
                    return haystack.includes(query);
                })
                .sort((a, b) => {
                    const bookA = String(a.dragon_book ?? '').trim();
                    const bookB = String(b.dragon_book ?? '').trim();

                    const normalizedA = bookA ? bookA.replace(/^0+/, '') || '0' : '999999';
                    const normalizedB = bookB ? bookB.replace(/^0+/, '') || '0' : '999999';

                    return Number(normalizedA) - Number(normalizedB);
                })
                .slice(0, 10);

            fillSuggestions(matches);
        }

        dragonNameInput.addEventListener('input', updateSuggestions);

        suggestions.addEventListener('click', function (event) {
            const button = event.target.closest('button[data-id]');
            if (!button) return;
            dragonNameInput.value = button.getAttribute('data-name');
            dragonIdInput.value = button.getAttribute('data-id');
            clearSuggestions();
            dragonNameInput.focus();
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('#dragon_name') && !event.target.closest('#dragon_suggestions')) {
                clearSuggestions();
            }
        });

        if (form) {
            form.addEventListener('submit', function () {
                const query = dragonNameInput.value.trim().toLowerCase();
                const selected = dragonData.find(item => item.name.toLowerCase() === query);
                dragonIdInput.value = selected ? selected.id : '';
            });
        }
    });
</script>
@endsection
