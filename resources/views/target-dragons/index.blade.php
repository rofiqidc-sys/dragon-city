@extends('layouts.app')

@section('title', 'Target Dragon')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Target Dragon</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item active">Target Dragon</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card shadow-sm border-0" style="background: #ffffff;">
            <div class="card-header" style="background: linear-gradient(145deg, #73EC8B 0%, #06D001 100%); border-radius: .5rem; color: #ffffff; border: none;">
                <h5 class="mb-1" style="font-weight: 700;">Target Dragon</h5>
                <p class="mb-0" style="opacity: .9;">Daftar dragon yang dimiliki oleh account selain account ID 1, ditampilkan satu nama dragon saja.</p>
            </div>
            <div class="card-body" style="background: #ffffff; color: #1f2d20;">
                <p class="card-text text-dark">Halaman ini menampilkan dragon yang sudah dimiliki oleh akun lain dan tidak termasuk dragon yang hanya dimiliki oleh account dengan ID 1. Jika nama dragon sama, hanya satu data yang tampil.</p>
                <form method="GET" action="{{ route('target-dragons.index') }}" class="row gx-2 align-items-end mb-4">
                    <div class="col-md-4">
                        <label for="account" class="form-label">Filter Account</label>
                        <select id="account" name="account" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Account --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ (string) $selectedAccount === (string) $account->id ? 'selected' : '' }}>{{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="rarity" class="form-label">Filter Rarity</label>
                        <select id="rarity" name="rarity" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Semua Rarity --</option>
                            @foreach($rarities as $rarity)
                                <option value="{{ $rarity->id }}" {{ (string) $selectedRarity === (string) $rarity->id ? 'selected' : '' }}>{{ $rarity->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Dragon</label>
                        <div class="input-group">
                            <input id="search" name="search" type="search" class="form-control" placeholder="Cari nama, book, atau rarity..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-success">Cari</button>
                        </div>
                    </div>
                </form>
                <div class="row mt-4">
                    @forelse($dragons as $dragon)
                        <div class="col-sm-6 col-lg-3 mb-4">
                            <div class="card h-100 shadow-sm" style="background: #ffffff; border: 1.5px solid #73EC8B;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1 text-dark">{{ $dragon->dragon_name }}</h5>
                                            <p class="text-muted mb-0">{{ $dragon->dragon_book ?? '-' }}</p>
                                        </div>
                                        <span class="badge" style="background: #73EC8B; color: #064400; font-weight: 700;">ID {{ $dragon->id }}</span>
                                    </div>

                                    <p class="mb-2"><strong>Summon Time:</strong> <span class="text-dark">{{ $dragon->summon_time ?? '-' }}</span></p>
                                    <p class="mb-2"><strong>Orb to Summon:</strong> <span class="text-dark">{{ $dragon->orb_to_summon }}</span></p>
                                    <p class="mb-2"><strong>Rarity:</strong> <span class="text-dark">{{ $dragon->rarity->name ?? '-' }}</span></p>
                                    <p class="mb-2"><strong>Elements:</strong>
                                        <span class="text-dark">
                                            {{ $dragon->element1->name ?? '' }}
                                            {{ $dragon->element2 ? ' / ' . $dragon->element2->name : '' }}
                                            {{ $dragon->element3 ? ' / ' . $dragon->element3->name : '' }}
                                            {{ $dragon->element4 ? ' / ' . $dragon->element4->name : '' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <span class="text-success" style="font-weight: 700;">Dimiliki oleh account selain #1</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-success" role="alert">
                                Tidak ada dragon target yang cocok dengan filter saat ini.
                            </div>
                        </div>
                    @endforelse
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $dragons->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
