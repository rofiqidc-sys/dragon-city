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
        <div>
            <h5 class="mb-0">{{ $showOwnedDragons ? 'Daftar semua Dragon:' : 'Dragon yang belum dimiliki:' }} {{ $account->account_name }}</h5>
            <small class="d-block text-muted">Dragon dimiliki saat ini: {{ $ownedDragonCount }}</small>
        </div>
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
            @if($showOwnedDragons)
                <input type="hidden" name="include_owned" value="1">
            @endif
        </form>

        @if($dragons->isEmpty())
            <p class="text-center text-muted mb-0">Tidak ada Dragon yang cocok dengan filter ini.</p>
        @else
            <form action="{{ route('dragon-ownings.quick-store', $account) }}" method="POST">
                @csrf
                <input type="hidden" name="page" value="{{ $dragons->currentPage() }}">
                @if($showOwnedDragons)
                    <input type="hidden" name="include_owned" value="1">
                @endif
                @if($selectedRarity)
                    <input type="hidden" name="rarity" value="{{ $selectedRarity }}">
                @endif
                <div class="mb-3">
                    <a href="{{ route('dragon-ownings.quick-create', array_filter([
                        'account' => $account,
                        'rarity' => $selectedRarity,
                        'include_owned' => $showOwnedDragons ? null : 1,
                    ], fn ($value) => $value !== null)) }}" class="btn btn-outline-secondary btn-sm">
                        {{ $showOwnedDragons ? 'Sembunyikan Dragon' : 'Tampilkan Dragon' }}
                    </a>
                </div>
                <div id="dragon-grid" class="dragon-grid">
                    @foreach($dragons as $dragon)
                        <div>
                            <div class="custom-control custom-checkbox border rounded p-3 h-100">
                                <input type="checkbox" class="custom-control-input dragon-checkbox" id="dragon-{{ $dragon->id }}" name="dragon_ids[]" value="{{ $dragon->id }}" {{ $ownedDragonIds->contains($dragon->id) ? 'disabled' : '' }}>
                                <label class="custom-control-label d-block" for="dragon-{{ $dragon->id }}">
                                    <strong>{{ $dragon->dragon_name }}</strong>
                                    <small class="d-block text-muted">Book: {{ $dragon->dragon_book ?? '-' }} | Rarity: {{ $dragon->rarity->name ?? '-' }}</small>
                                    @if($ownedDragonIds->contains($dragon->id))
                                        <small class="badge badge-success mt-1">Dimiliki</small>
                                    @endif
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

@push('styles')
<style>
    .dragon-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
    }

    @media (max-width: 1199.98px) {
        .dragon-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .dragon-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .dragon-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush