<!doctype html>
<html lang="en">

<head>@include('partials.head')</head>

<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div class="dashboard-shell @yield('dashboard-mode')-shell" x-data="dashboard" :class="shellClass">
        <div class="sidebar-overlay" x-show="mobileOpen" x-cloak @click="closeMobile" aria-hidden="true">
        </div>
        <aside id="dashboard-sidebar" class="sidebar" :class="sidebarClass" aria-label="@yield('workspace-name') navigation">
            <div class="sidebar-brand">
                <x-brand />
                <button class="icon-button sidebar-close" type="button" @click="closeMobile"
                    aria-label="Close navigation">
                    <x-icon name="close" size="16" />
                </button>
            </div>
            <nav>
                <p class="sidebar-heading">@yield('workspace-name')</p>@yield('sidebar-navigation')
            </nav>
            <div class="sidebar-bottom">
                <div class="sidebar-note">
                    <strong>
                        <x-icon name="shield" size="15" /> A community built on
                        trust</strong>Verified identities. Protected transactions.
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="side-link" title="Log out">
                        <x-icon name="logout" />
                        <span class="side-label">Log out</span>
                    </button>
                </form>
            </div>
        </aside>
        <div class="dashboard-main" id="dashboard-workspace">
            <header class="dashboard-topbar">
                <div>
                    <button type="button" class="icon-button desktop-toggle" @click="toggleCollapse"
                        :aria-expanded="expanded" aria-controls="dashboard-sidebar"
                        aria-label="Collapse or expand sidebar">
                        <x-icon name="menu" />
                    </button>
                    <button type="button" class="icon-button mobile-toggle" @click="openMobile"
                        :aria-expanded="mobileOpen" aria-controls="dashboard-sidebar" aria-label="Open navigation">
                        <x-icon name="menu" />
                    </button>
                    <span class="muted text-xs">@yield('workspace-name')</span>
                </div>
                <div class="topbar-account">
                    <a class="text-link" href="{{ route('listings.index') }}">Marketplace
                        <x-icon name="arrow" size="15" />
                    </a>
                    <x-avatar :user="auth()->user()" />
                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->isAdmin() ? 'Administrator' : 'DELSU student' }}</small>
                    </div>
                </div>
            </header>
            <main id="main-content" class="dashboard-content">
                <x-flash />@yield('content')
            </main>
        </div>
    </div>
</body>

</html>
