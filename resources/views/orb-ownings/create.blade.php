@extends('layouts.app')

@section('title', 'Add Orb Owning')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Add Orb Owning</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orb-ownings.index') }}">Orb Owning</a></li>
                    <li class="breadcrumb-item active">Add Orb Owning</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Add Orb Owning</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('orb-ownings.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="account_id">Account <span class="text-danger">*</span></label>
                        <select id="account_id" name="account_id" class="form-control" required>
                            <option value="">-- Select Account --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group position-relative">
                        <label for="dragon_name">Dragon <span class="text-danger">*</span></label>
                        <input type="hidden" id="dragon_id" name="dragon_id" value="{{ old('dragon_id') }}">
                        <input type="text" id="dragon_name" name="dragon_name" class="form-control" placeholder="Type at least 3 characters..." value="{{ old('dragon_name', optional($dragons->firstWhere('id', old('dragon_id')))->dragon_name) }}" autocomplete="off" required>
                        <div id="dragon_suggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index: 10; display: none; max-height: 220px; overflow-y: auto;"></div>
                        <small class="form-text text-muted">Type 3 or more letters to see matching dragons.</small>
                        @error('dragon_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jumlah_orb">Jumlah Orb <span class="text-danger">*</span></label>
                        <input type="number" id="jumlah_orb" name="jumlah_orb" class="form-control" min="0" value="0" required>
                        @error('jumlah_orb')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Save</button>
                        <a href="{{ route('orb-ownings.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
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
        const dragonData = @json($dragons->map(fn($dragon) => ['id' => $dragon->id, 'name' => $dragon->dragon_name]));

        function clearSuggestions() {
            suggestions.innerHTML = '';
            suggestions.style.display = 'none';
        }

        function fillSuggestions(items) {
            if (items.length === 0) {
                suggestions.innerHTML = '<div class="list-group-item text-muted">No dragons found.</div>';
            } else {
                suggestions.innerHTML = items.map(item =>
                    `<button type="button" class="list-group-item list-group-item-action text-left" data-id="${item.id}">${item.name}</button>`
                ).join('');
            }
            suggestions.style.display = 'block';
        }

        function findMatchingDragon(query) {
            if (!query) {
                return null;
            }

            return dragonData.find(item => item.name.toLowerCase().includes(query));
        }

        function updateSuggestions() {
            const query = dragonNameInput.value.trim().toLowerCase();
            if (query.length < 3) {
                dragonIdInput.value = '';
                return clearSuggestions();
            }

            const matches = dragonData.filter(item => item.name.toLowerCase().includes(query)).slice(0, 10);
            fillSuggestions(matches);
        }

        dragonNameInput.addEventListener('input', updateSuggestions);

        suggestions.addEventListener('click', function (event) {
            const button = event.target.closest('button[data-id]');
            if (!button) return;
            dragonNameInput.value = button.textContent.trim();
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
                const selected = dragonData.find(item => item.name.toLowerCase() === query) || findMatchingDragon(query);
                dragonIdInput.value = selected ? selected.id : '';
            });
        }
    });
</script>
@endsection
