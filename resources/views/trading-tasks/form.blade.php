@php($task = $task ?? null)
<div class="form-group position-relative">
    <label for="trading_dragon_name">Dragon <span class="text-danger">*</span></label>
    <input type="hidden" id="trading_dragon_id" name="dragon_id" value="{{ old('dragon_id', $task->dragon_id ?? '') }}">
    <input type="text" id="trading_dragon_name" class="form-control" placeholder="Cari dragon berdasarkan nama atau book..." value="{{ old('dragon_name', $task?->dragon?->dragon_name) }}" autocomplete="off" required>
    <div id="trading_dragon_suggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; display: none; max-height: 220px; overflow-y: auto;"></div>
    @error('dragon_id')<span class="text-danger">{{ $message }}</span>@enderror
</div>
<div class="form-group">
    <label for="trader_id">Trader Account <span class="text-danger">*</span></label>
    <select id="trader_id" name="trader_id" class="form-control" required>
        <option value="">-- Select Trader --</option>
        @foreach($accounts as $account)
            <option value="{{ $account->id }}" {{ old('trader_id', $task->trader_id ?? '') == $account->id ? 'selected' : '' }}>{{ $account->account_name }}</option>
        @endforeach
    </select>
    @error('trader_id')<span class="text-danger">{{ $message }}</span>@enderror
</div>
<div class="form-group">
    <label for="reciever_id">Reciever Account <span class="text-danger">*</span></label>
    <select id="reciever_id" name="reciever_id" class="form-control" required>
        <option value="">-- Select Reciever --</option>
        @foreach($accounts as $account)
            <option value="{{ $account->id }}" {{ old('reciever_id', $task->reciever_id ?? '') == $account->id ? 'selected' : '' }}>{{ $account->account_name }}</option>
        @endforeach
    </select>
    @error('reciever_id')<span class="text-danger">{{ $message }}</span>@enderror
</div>
<div class="form-group">
    <label for="jumlah_orb">Jumlah Orb <span class="text-danger">*</span></label>
    <input id="jumlah_orb" name="jumlah_orb" type="number" min="1" class="form-control" value="{{ old('jumlah_orb', $task->jumlah_orb ?? 1) }}" required>
    @error('jumlah_orb')<span class="text-danger">{{ $message }}</span>@enderror
</div>
<div class="form-group">
    <label for="status_trade">Status Trade <span class="text-danger">*</span></label>
    <select id="status_trade" name="status_trade" class="form-control" required>
        @foreach($statuses as $status)
            <option value="{{ $status }}" {{ old('status_trade', $task->status_trade ?? 'recalling') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    @error('status_trade')<span class="text-danger">{{ $message }}</span>@enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dragonNameInput = document.getElementById('trading_dragon_name');
        const dragonIdInput = document.getElementById('trading_dragon_id');
        const suggestions = document.getElementById('trading_dragon_suggestions');
        const dragons = @json($dragonData);

        function clearSuggestions() {
            suggestions.innerHTML = '';
            suggestions.style.display = 'none';
        }

        function updateSuggestions() {
            const query = dragonNameInput.value.trim().toLowerCase();
            dragonIdInput.value = '';

            if (query.length < 2) {
                clearSuggestions();
                return;
            }

            const matches = dragons.filter(dragon => {
                const text = `${dragon.name} ${dragon.dragon_book ?? ''}`.toLowerCase();
                return text.includes(query);
            }).slice(0, 10);

            suggestions.innerHTML = matches.length
                ? matches.map(dragon => `<button type="button" class="list-group-item list-group-item-action text-left" data-id="${dragon.id}" data-name="${dragon.name}">${dragon.dragon_book ?? '-'} - ${dragon.name}</button>`).join('')
                : '<div class="list-group-item text-muted">No dragons found.</div>';
            suggestions.style.display = 'block';
        }

        dragonNameInput.addEventListener('input', updateSuggestions);

        suggestions.addEventListener('click', function (event) {
            const button = event.target.closest('button[data-id]');
            if (!button) return;

            dragonNameInput.value = button.dataset.name;
            dragonIdInput.value = button.dataset.id;
            clearSuggestions();
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('#trading_dragon_name') && !event.target.closest('#trading_dragon_suggestions')) {
                clearSuggestions();
            }
        });

        dragonNameInput.closest('form').addEventListener('submit', function (event) {
            if (!dragonIdInput.value) {
                event.preventDefault();
                dragonNameInput.focus();
                updateSuggestions();
            }
        });
    });
</script>