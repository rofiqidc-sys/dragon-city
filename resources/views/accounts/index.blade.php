@extends('layouts.app')

@section('title', 'Accounts')

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5>Accounts</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#!">Master Data</a></li>
                    <li class="breadcrumb-item active">Accounts</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Account List</h5>
                <div class="d-flex gap-2">
                    <form action="{{ route('accounts.update-seeder') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Update AccountSeeder file based on current database data?')">Update Seeder</button>
                    </form>
                    <button type="button" class="btn btn-xs btn-outline-secondary btn-copy" data-copy="albiruni27" title="Copy FB mail">
                        <i class="fas fa-copy"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-outline-info btn-copy" data-copy="!albiruni27" title="Copy FB mail">
                        <i class="fas fa-copy"></i>
                    </button>
                    <a href="{{ route('accounts.create') }}" class="btn btn-primary">Add Account</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div id="copy-toast-container"></div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    <a href="{{ route('accounts.index', ['sort' => 'account_name', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}" class="text-dark">
                                        Account Name
                                        @if($sortField === 'account_name')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('accounts.index', ['sort' => 'fb_mail', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}" class="text-dark">
                                        FB Mail
                                        @if($sortField === 'fb_mail')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('accounts.index', ['sort' => 'gmail', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}" class="text-dark">
                                        Gmail
                                        @if($sortField === 'gmail')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('accounts.index', ['sort' => 'ms_mail', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}" class="text-dark">
                                        MS Mail
                                        @if($sortField === 'ms_mail')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('accounts.index', ['sort' => 'account_status', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}" class="text-dark">
                                        Status
                                        @if($sortField === 'account_status')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $index => $account)
                                <tr class="{{ $index % 2 === 0 ? 'table-active' : '' }} {{ $account->account_status === 'active' ? 'table-success' : 'table-danger' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $account->account_name }}</td>
                                    <td>
                                        {{ $account->fb_mail }}
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-copy" data-copy="{{ $account->fb_mail }}" title="Copy FB mail">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                    <td>{{ $account->gmail }}</td>
                                    <td>{{ $account->ms_mail }}</td>
                                    <td>
                                        <span class="badge {{ $account->account_status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($account->account_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('accounts.destroy', $account) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this account?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="table-info">
                                    <td colspan="7" class="text-center">No accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
                @push('scripts')
                    <script>
                        function showCopyToast(message) {
                            const container = document.getElementById('copy-toast-container');
                            if (!container) return;
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.role = 'alert';
                            alert.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            container.appendChild(alert);
                            setTimeout(() => { if (alert) alert.remove(); }, 2500);
                        }

                        document.addEventListener('click', function (e) {
                            const btn = e.target.closest('.btn-copy');
                            if (!btn) return;
                            const value = btn.getAttribute('data-copy') || '';
                            if (!value) return;

                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                navigator.clipboard.writeText(value).then(() => {
                                    showCopyToast('Copied to clipboard');
                                }).catch(() => {
                                    showCopyToast('Copy failed');
                                });
                            } else {
                                // fallback
                                const textarea = document.createElement('textarea');
                                textarea.value = value;
                                textarea.style.position = 'fixed';
                                textarea.style.left = '-9999px';
                                document.body.appendChild(textarea);
                                textarea.select();
                                try {
                                    document.execCommand('copy');
                                    showCopyToast('Copied to clipboard');
                                } catch (err) {
                                    showCopyToast('Copy failed');
                                }
                                textarea.remove();
                            }
                        });
                    </script>
                @endpush

                @endsection
