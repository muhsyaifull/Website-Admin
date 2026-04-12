<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Session Monitor</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/monitor-session.css') }}" rel="stylesheet">
</head>

<body>
    <header class="monitor-header">
        <div>
            <h1 class="monitor-title">Today's Session List Board</h1>
            <p class="monitor-subtitle">Rumah Atsiri Indonesia</p>
        </div>

        <div class="header-right">
            <div class="meta-time">
                <span>{{ now()->translatedFormat('l, d M Y') }}</span>
                <span>Last update: {{ $lastUpdated->format('H:i:s') }}</span>
            </div>

            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                @csrf
                <button type="button" class="logout-icon-btn" id="logoutTrigger" title="Logout">
                    <img src="{{ asset('Images/Login-Logo2-2.png') }}" alt="Logout">
                </button>
            </form>
        </div>
    </header>

    <main class="monitor-content">
        @foreach($tours as $tour)
            @php
                $sessions = ($tourSessions[$tour->id] ?? collect())
                    ->sortBy('start_time')
                    ->values();
            @endphp

            <section class="tour-panel">
                <div class="tour-panel-header">
                    <h2>{{ $tour->name }}</h2>
                    <div class="tour-capacity">
                        {{ $sessions->sum('booked') }}/{{ $sessions->sum('capacity') }} participants
                    </div>
                </div>

                <div class="session-list">
                    @forelse($sessions as $session)
                        @php
                            $now = \Carbon\Carbon::now();
                            $start = \Carbon\Carbon::parse($session->start_time);
                            $end = \Carbon\Carbon::parse($session->end_time);
                            $isEnded = $now->gt($end);
                            $isRunning = $now->between($start, $end);
                        @endphp

                        <article class="session-card {{ $session->is_full ? 'full' : '' }} {{ $isEnded ? 'ended' : '' }}">
                            <div class="session-top">
                                <div>
                                    <div class="session-label">{{ $session->label }}</div>
                                    <div class="session-time">
                                        {{ $start->format('H:i') }} - {{ $end->format('H:i') }}
                                    </div>
                                </div>

                                <div class="session-state-wrap">
                                    @if($isRunning)
                                        <span class="session-state state-running">In Progress</span>
                                    @elseif($isEnded)
                                        <span class="session-state state-ended">Ended</span>
                                    @else
                                        <span class="session-state state-upcoming">Upcoming</span>
                                    @endif
                                </div>
                            </div>

                            <div class="session-progress">
                                <div class="session-progress-bar"
                                    style="width: {{ $session->booking_percentage }}%; background: {{ $session->bar_color }};">
                                </div>
                            </div>

                            <div class="session-bottom">
                                <div class="session-guide">
                                    <i class="fas fa-user"></i>
                                    {{ $session->educator?->name ?? '-' }}
                                </div>

                                <div class="session-capacity">
                                    <strong>{{ $session->booked }}/{{ $session->capacity }}</strong>
                                    <span class="session-badge"
                                        style="background: {{ $session->status_background }}; color: {{ $session->status_color }};">
                                        {{ $session->status }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state">No active session for this tour today.</div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </main>

    <button type="button" class="fullscreen-btn" id="fullscreenBtn" title="Toggle Fullscreen">
        <i class="fas fa-expand" id="fullscreenIcon"></i>
    </button>

    <div class="logout-modal" id="logoutModal" aria-hidden="true">
        <div class="logout-modal-backdrop" data-logout-close></div>
        <div class="logout-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle">
            <div class="logout-modal-header">
                <div class="logout-modal-icon">
                    <i class="fas fa-right-from-bracket"></i>
                </div>
                <div>
                    <h2 id="logoutModalTitle">Konfirmasi Logout</h2>
                    <p>Apakah Anda yakin ingin keluar dari halaman monitor ini?</p>
                </div>
            </div>

            <div class="logout-modal-actions">
                <button type="button" class="logout-modal-cancel" data-logout-close>Batal</button>
                <button type="submit" class="logout-modal-confirm" form="logoutForm">Logout</button>
            </div>
        </div>
    </div>

    <script>
        const MONITOR_REFRESH_MS = 60000;

        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const fullscreenIcon = document.getElementById('fullscreenIcon');
        const logoutTrigger = document.getElementById('logoutTrigger');
        const logoutModal = document.getElementById('logoutModal');
        const logoutCloseButtons = document.querySelectorAll('[data-logout-close]');

        function openLogoutModal() {
            logoutModal.classList.add('is-open');
            logoutModal.setAttribute('aria-hidden', 'false');
        }

        function closeLogoutModal() {
            logoutModal.classList.remove('is-open');
            logoutModal.setAttribute('aria-hidden', 'true');
        }

        async function refreshBoardContent() {
            if (document.hidden) return;

            try {
                const response = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store'
                });

                if (!response.ok) return;

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const nextMain = doc.querySelector('.monitor-content');
                const nextMeta = doc.querySelector('.meta-time');

                if (nextMain) {
                    document.querySelector('.monitor-content').innerHTML = nextMain.innerHTML;
                }
                if (nextMeta) {
                    document.querySelector('.meta-time').innerHTML = nextMeta.innerHTML;
                }

            } catch (error) {
                console.error('Monitor refresh failed', error);
            }
        }

        setInterval(refreshBoardContent, MONITOR_REFRESH_MS);

        logoutTrigger.addEventListener('click', openLogoutModal);

        logoutCloseButtons.forEach(function (button) {
            button.addEventListener('click', closeLogoutModal);
        });

        logoutModal.addEventListener('click', function (event) {
            if (event.target === logoutModal) {
                closeLogoutModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && logoutModal.classList.contains('is-open')) {
                closeLogoutModal();
            }
        });

        fullscreenBtn.addEventListener('click', async function () {
            try {
                if (!document.fullscreenElement) {
                    await document.documentElement.requestFullscreen();
                } else {
                    await document.exitFullscreen();
                }
            } catch (error) {
                console.error('Fullscreen toggle failed', error);
            }
        });

        document.addEventListener('fullscreenchange', function () {
            const isFull = Boolean(document.fullscreenElement);
            fullscreenIcon.className = isFull ? 'fas fa-compress' : 'fas fa-expand';
            fullscreenBtn.title = isFull ? 'Exit Fullscreen' : 'Toggle Fullscreen';
            document.body.classList.toggle('is-fullscreen', isFull);
        });
    </script>
</body>

</html>