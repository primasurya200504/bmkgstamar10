<!-- Sidebar -->
<div class="sidebar w-72 flex flex-col shadow-2xl">
    <div class="flex items-center justify-center h-20 border-b border-white border-opacity-20">
        <div class="text-center">
            <img src="/img/logo.png" alt="BMKG Logo" class="w-16 h-16 mx-auto mb-2">
            <h1 class="text-white text-xl font-bold">BMKG STAMAR</h1>
            <p class="text-white text-sm opacity-80">Admin Panel</p>
        </div>
    </div>

    <nav class="flex-1 px-6 py-8 space-y-3">
        <a href="{{ route('admin.dashboard') }}" id="nav-dashboard"
            class="flex items-center px-4 py-3 text-white bg-white bg-opacity-20 rounded-xl hover:bg-opacity-30 transition-all duration-200 backdrop-blur-sm">
            <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z">
                </path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('admin.submissions') }}" id="nav-submissions"
            class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
            <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Kelola Pengajuan</span>
        </a>

        <a href="{{ route('admin.ebilling.upload') }}" id="nav-ebilling"
            class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
            <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Upload e-Billing</span>
        </a>

        <a href="{{ route('admin.ebilling') }}" id="nav-payments"
            class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
            <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Kelola Pembayaran</span>
        </a>

        <a href="{{ route('admin.data-uploads.index') }}" id="nav-data-uploads"
            class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
            <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Upload Data</span>
        </a>

        <a href="{{ route('admin.guidelines') }}" id="nav-guidelines"
            class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
            <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Kelola Panduan</span>
        </a>

        <a href="{{ route('admin.users') }}" id="nav-users"
            class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
            <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Kelola Pengguna</span>
        </a>

        <a href="{{ route('admin.archives') }}" id="nav-archives"
            class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
            <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="font-medium">Arsip & Laporan</span>
        </a>
    </nav>

    <div class="px-6 py-6 border-t border-white border-opacity-20">
        <div class="flex items-center mb-6">
            <div
                class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white font-bold text-lg backdrop-blur-sm">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
            <div class="ml-4">
                <p class="text-white font-semibold">{{ Auth::user()->name ?? 'Admin' }}</p>
                <p class="text-white text-sm opacity-80">{{ Auth::user()->email ?? '' }}</p>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit"
                class="w-full bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white border-opacity-20">
                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                        clip-rule="evenodd"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>
