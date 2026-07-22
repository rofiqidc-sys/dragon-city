<nav class="pcoded-navbar menupos-fixed menu-light brand-blue ">
    <div class="navbar-wrapper ">
        <div class="navbar-brand header-logo">
            <a href="{{ url('/') }}" class="b-brand">
                <img src="{{ asset('themes/assets/images/logo.svg') }}" alt="" class="logo images">
                <img src="{{ asset('themes/assets/images/logo-icon.svg') }}" alt="" class="logo-thumb images">
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        </div>
        <div class="navbar-content scroll-div">
            <ul class="nav pcoded-inner-navbar">
                <li class="nav-item pcoded-menu-caption">
                    <label>Navigation</label>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link"><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
                </li>
                <li class="nav-item pcoded-menu-caption">
                    <label>Master Data</label>
                </li>
                <li class="nav-item">
                    <a href="{{ route('accounts.index') }}" class="nav-link"><span class="pcoded-micon"><i class="feather icon-user"></i></span><span class="pcoded-mtext">Account</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('elements.index') }}" class="nav-link"><span class="pcoded-micon"><i class="feather icon-box"></i></span><span class="pcoded-mtext">Element</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rarities.index') }}" class="nav-link"><span class="pcoded-micon"><i class="feather icon-star"></i></span><span class="pcoded-mtext">Rarity</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dragons.index') }}" class="nav-link"><span class="pcoded-micon"><i class="feather icon-zap"></i></span><span class="pcoded-mtext">Dragon</span></a>
                </li>
                <li class="nav-item pcoded-menu-caption">
                    <label>Additional Data</label>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><span class="pcoded-micon"><i class="feather icon-user"></i></span><span class="pcoded-mtext">Food Producer Dragon</span></a>
                </li>
                <li class="nav-item pcoded-menu-caption">
                    <label>InGame Data</label>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dragon-ownings.index') }}" class="nav-link"><span class="pcoded-micon"><i class="feather icon-user"></i></span><span class="pcoded-mtext">Dragon Owning</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('orb-ownings.index') }}" class="nav-link"><span class="pcoded-micon"><i class="feather icon-bell"></i></span><span class="pcoded-mtext">Orb Owning</span></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
