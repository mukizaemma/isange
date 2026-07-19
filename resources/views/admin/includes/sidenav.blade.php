<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">

            <a class="nav-link" href="{{ route('siteContent') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                Website content
            </a>
            <a class="nav-link" href="{{route('setting')}}">
                <div class="sb-nav-link-icon"><i class="fas fa-sliders-h"></i></div>
                Site settings
            </a>
            <a class="nav-link" href="{{route('about')}}">
                <div class="sb-nav-link-icon"><i class="fas fa-edit"></i></div>
                Welcome text
            </a>
            <a class="nav-link" href="{{route('getServices')}}">
                <div class="sb-nav-link-icon"><i class="fas fa-concierge-bell"></i></div>
                Services
            </a>
            <a class="nav-link" href="{{route('getRooms')}}">
                <div class="sb-nav-link-icon"><i class="fas fa-bed"></i></div>
                Rooms
            </a>

            <a class="nav-link" href="{{route('facilityCrud')}}">
                <div class="sb-nav-link-icon"><i class="fas fa-swimming-pool"></i></div>
                Facilities
            </a>

            <a class="nav-link" href="{{ route('diningMenu') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-utensils"></i></div>
                Dining page &amp; gallery
            </a>

            <a class="nav-link" href="{{ route('diningMenu.manage') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                Restaurant menu
            </a>

            <a class="nav-link" href="{{ route('diningMenu.categories.manage') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-layer-group"></i></div>
                Menu categories
            </a>

            <a class="nav-link" href="{{route('slides')}}">
                <div class="sb-nav-link-icon"><i class="fas fa-images"></i></div>
                Home slider
            </a>
            <a class="nav-link" href="{{route('getGalleries')}}">
                <div class="sb-nav-link-icon"><i class="fas fa-photo-video"></i></div>
                Gallery
            </a>
            <a class="nav-link" href="{{ route('partnerCrud') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                Partners
            </a>
            <a class="nav-link" href="{{ route('admin.blogs.index') }}">
                <div class="sb-nav-link-icon"><i class="far fa-newspaper"></i></div>
                Updates
            </a>
            <hr>
            {{-- <a class="nav-link" href="{{route('RestoMenu')}}">
                <div class="sb-nav-link-icon"><i class="fa fa-hotel"></i></div>
                Restaurant Menu
            </a>
            <hr> --}}

            <a class="nav-link" href="{{route('bookings')}}">
                <div class="sb-nav-link-icon"><i class="far fa-heart"></i></div>
                Reservations
            </a>
            <a class="nav-link" href="{{ route('guestInsights') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                Guest insights
            </a>
            <a class="nav-link" href="{{ route('admin.guests.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-user-friends"></i></div>
                Guests
            </a>
            <a class="nav-link" href="https://analytics.google.com/analytics/web/?authuser=6#/a352534398p486046047/realtime/overview?params=_u..nav%3Dmaui" target="_blank">
                <div class="sb-nav-link-icon"><i class="fa fa-globe"></i></div>
                Google Analytics
            </a>  
            @auth
                @if (auth()->user()->canManageUsers())
                <a class="nav-link" href="{{ route('admin.users') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Users
                </a>
                @endif
                <a class="nav-link" href="{{ route('account.password') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-key"></i></div>
                    Change password
                </a>
            @endauth
           



        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">Logged in as:</div>
        Susper Admin
    </div>
</nav>
