<nav class="bg-[#004d32] h-full w-full flex flex-col relative overflow-y-auto custom-scrollbar">

    <button @click="sidebarOpen = false"
        class="absolute top-6 right-6 text-white/40 hover:text-[#FECB02] transition-colors lg:hidden p-2 z-50">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <div class="px-6 pt-12 pb-8 space-y-3 flex-1 mt-4 lg:mt-0">
        @php
            $activeClass = 'bg-[#FECB02] text-[#004d32] shadow-md font-bold border-2 border-[#FECB02]';
            $inactiveClass =
                'text-white/80 hover:bg-white/10 hover:text-white font-semibold border-2 border-transparent';
        @endphp

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-5 px-6 py-4 rounded-xl transition-all {{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }}">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="text-base">Overview</span>
        </a>

        <div class="pt-8 pb-3">
            <p class="px-6 text-sm font-bold text-green-200/60 uppercase tracking-wider mb-4">Lost & Found</p>

            <a href="{{ route('assets.index') }}"
                class="flex items-center gap-5 px-6 py-4 rounded-xl transition-all mb-3 {{ request()->routeIs('assets.index') ? $activeClass : $inactiveClass }}">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    stroke-width="2.5">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-base">Lost & Found</span>
            </a>

            <a href="{{ route('assets.lost-ids') }}"
                class="flex items-center gap-5 px-6 py-3.5 rounded-xl transition-all ml-4 {{ request()->routeIs('assets.lost-ids') ? 'bg-white/10 text-[#FECB02] font-bold border-2 border-white/20' : 'text-white/70 hover:text-white hover:bg-white/5 font-semibold border-2 border-transparent' }}">
                <div
                    class="w-2.5 h-2.5 rounded-full {{ request()->routeIs('assets.lost-ids') ? 'bg-[#FECB02] shadow-[0_0_8px_rgba(254,203,2,0.8)]' : 'bg-white/30' }}">
                </div>
                <span class="text-base">ID Recovery</span>
            </a>
        </div>


        <div class="pt-6 pb-3">
            <p class="px-6 text-sm font-bold text-green-200/60 uppercase tracking-wider mb-4">Registry Management</p>

            <a href="{{ route('students.index') }}"
                class="flex items-center gap-5 px-6 py-4 rounded-xl transition-all mb-3 {{ request()->routeIs('students.*') ? $activeClass : $inactiveClass }}">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    stroke-width="2.5">
                    <path
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-base">Student Directory</span>
            </a>

            <a href="{{ route('violations.report') }}"
                class="flex items-center gap-5 px-6 py-4 rounded-xl transition-all mb-3 {{ request()->routeIs('violations.*') ? $activeClass : $inactiveClass }}">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    stroke-width="2.5">
                    <path
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="text-base">Violations</span>
            </a>

            <a href="{{ route('incidents.index') }}"
                class="flex items-center gap-5 px-6 py-4 rounded-xl transition-all mb-3 {{ request()->routeIs('incidents.*') ? $activeClass : $inactiveClass }}">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span class="text-base">Incident Reports</span>
            </a>

            <a href="{{ route('confiscated-items.index') }}"
                class="flex items-center gap-5 px-6 py-4 rounded-xl transition-all mb-3 {{ request()->routeIs('confiscated-items.*') ? $activeClass : $inactiveClass }}">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <span class="text-base">Confiscated Items</span>
            </a>

            <a href="{{ route('referrals.index') }}"
                class="flex items-center gap-5 px-6 py-4 rounded-xl transition-all {{ request()->routeIs('referrals.*') ? $activeClass : $inactiveClass }}">
                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span class="text-base">Counseling Referrals</span>
            </a>

        </div>
    </div>

    <div class="p-8 border-t-2 border-white/10 bg-black/10 shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full py-4 border-2 border-white/20 hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/40 rounded-xl text-base font-bold text-white/80 transition-all flex justify-center items-center gap-3">
                Log Out
            </button>
        </form>
    </div>
</nav>
