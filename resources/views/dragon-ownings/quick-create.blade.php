@extends('layouts.app')

@section('title', 'Tambah Cepat Dragon')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Tambah Cepat Dragon</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dragon-ownings.index') }}">Dragon Owning</a></li>
                    <li class="breadcrumb-item active">Tambah Cepat</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Dragon yang belum dimiliki: {{ $account->account_name }}</h5>
        <a href="{{ route('dragon-ownings.show', $account) }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('dragon-ownings.quick-create', $account) }}" method="GET" class="row align-items-end mb-4">
            <div class="col-md-5">
                <label for="rarity">Filter Rarity</label>
                <select id="rarity" name="rarity" class="form-control">
                    <option value="">-- Semua Rarity --</option>
                    @foreach($rarities as $rarity)
                        <option value="{{ $rarity->id }}" {{ (string) $selectedRarity === (string) $rarity->id ? 'selected' : '' }}>{{ $rarity->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mt-2 mt-md-0">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                <a href="{{ route('dragon-ownings.quick-create', $account) }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        @if($dragons->isEmpty())
            <p class="text-center text-muted mb-0">Semua dragon sudah dimiliki oleh account ini.</p>
        @else
            <form action="{{ route('dragon-ownings.quick-store', $account) }}" method="POST">
                @csrf
                @if($selectedRarity)
                    <input type="hidden" name="rarity" value="{{ $selectedRarity }}">
                @endif
                <div class="row">
                    @foreach($dragons as $dragon)
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="custom-control custom-checkbox border rounded p-3 h-100">
                                <input type="checkbox" class="custom-control-input dragon-checkbox" id="dragon-{{ $dragon->id }}" name="dragon_ids[]" value="{{ $dragon->id }}">
                                <label class="custom-control-label d-block" for="dragon-{{ $dragon->id }}">
                                    <strong>{{ $dragon->dragon_name }}</strong>
                                    <small class="d-block text-muted">Book: {{ $dragon->dragon_book ?? '-' }} | Rarity: {{ $dragon->rarity->name ?? '-' }}</small>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="border-top pt-3 mt-2 d-flex justify-content-between align-items-center">
                    <label class="mb-0">
                        <input type="checkbox" id="select-all-dragons" class="mr-1"> Pilih semua
                    </label>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambahkan Dragon Terpilih</button>
                </div>
            </form>
            <div class="d-flex justify-content-center mt-4">
                {{ $dragons->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('select-all-dragons')?.addEventListener('change', function () {
        document.querySelectorAll('.dragon-checkbox').forEach((checkbox) => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endpush