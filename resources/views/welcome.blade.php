<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LGU Facility Reservation</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('Images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .page-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("{{ asset('Images/BG_Facilities.png') }}");
            background-size: cover;
            background-position: center;
            z-index: -1;
            filter: brightness(0.6);
            transform: scale(1.05);
        }

        nav {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .text-backdrop {
            background: radial-gradient(circle at left, rgba(0, 0, 0, 0.6) 0%, transparent 85%);
            padding: 2rem;
            border-radius: 2.5rem;
        }

        .facility-card:hover img {
            transform: scale(1.1);
        }

        .contact-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .contact-input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #f97316;
            outline: none;
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.2);
        }

        #mobile-menu {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-10px);
        }

        #mobile-menu.active {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .facility-swal-popup {
            border-radius: 2rem !important;
            overflow: hidden !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .facility-swal-close {
            color: rgba(255, 255, 255, 0.5) !important;
            font-size: 1.75rem !important;
            top: 0.75rem !important;
            right: 0.75rem !important;
            z-index: 10 !important;
            background: rgba(0, 0, 0, 0.4) !important;
            border-radius: 50% !important;
            width: 36px !important;
            height: 36px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .facility-swal-close:hover {
            color: #fff !important;
            background: rgba(0, 0, 0, 0.6) !important;
        }

        @media (max-width: 640px) {
            .facility-swal-popup {
                border-radius: 1.5rem !important;
            }
            .facility-swal-popup div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>

<body class="antialiased font-['Instrument_Sans'] text-white bg-slate-950 overflow-x-hidden">

    <div id="mainBg" class="page-bg"></div>

    <nav>
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center relative z-[110]">
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('Images/logo.png') }}" alt="Logo"
                    class="h-10 w-10 object-contain drop-shadow-lg group-hover:rotate-12 transition">
                <span class="font-bold text-2xl tracking-tighter uppercase">LGU<span class="text-orange-500">Facility
                        Reservation</span></span>
            </a>
            <div class="hidden lg:flex gap-10 text-[10px] font-black tracking-[0.25em]">
                <a href="#hero" class="hover:text-orange-500 transition">HOME</a>
                <a href="#facilities" class="hover:text-orange-500 transition">FACILITIES</a>
                <a href="#contact" class="hover:text-orange-500 transition">CONTACT US</a>
            </div>
            <div class="hidden md:flex gap-6 items-center">
                <a href="{{ route('login') }}"
                    class="text-sm font-bold hover:text-orange-400 transition uppercase tracking-tighter">Log in</a>
                <a href="{{ route('register') }}"
                    class="bg-orange-600 text-white px-8 py-3 rounded-full text-sm font-bold hover:bg-orange-700 transition shadow-xl shadow-orange-900/40 uppercase">REGISTER</a>
            </div>
            <button id="menu-btn" class="lg:hidden text-white p-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path id="menu-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
        <div id="mobile-menu"
            class="lg:hidden absolute top-0 left-0 w-full min-h-screen bg-slate-950/98 backdrop-blur-3xl z-[105] flex flex-col items-center justify-center gap-8 px-8">
            <a href="#hero" class="mobile-link text-2xl font-black tracking-widest uppercase">HOME</a>
            <a href="#facilities" class="mobile-link text-2xl font-black tracking-widest uppercase">FACILITIES</a>
            <a href="#contact" class="mobile-link text-2xl font-black tracking-widest uppercase">CONTACT</a>
            <hr class="w-16 border-white/20">
            <a href="{{ route('login') }}" class="mobile-link text-xl font-bold uppercase">LOG IN</a>
            <a href="{{ route('register') }}"
                class="w-full max-w-xs bg-orange-600 text-white py-5 rounded-2xl font-black text-center text-lg">REGISTER</a>
        </div>
    </nav>

    <main class="relative z-10">
        <section id="hero" class="min-h-screen flex items-center pt-32 pb-20">
            <div class="max-w-7xl mx-auto px-6 w-full">
                <div class="max-w-3xl text-backdrop mx-auto lg:mx-0 text-center lg:text-left">
                    <span
                        class="bg-orange-600/20 text-orange-400 border border-orange-500/30 px-5 py-2 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-8 inline-block backdrop-blur-md">Official
                        LGU Portal</span>
                    <h1 class="text-5xl sm:text-7xl lg:text-[6rem] font-black leading-[1.1] lg:leading-[0.9] mb-8">
                        Booking <br class="hidden sm:block"> spaces <span class="text-orange-500 italic">made
                            simple.</span></h1>
                    <p class="text-lg md:text-xl text-white/70 mb-12 max-w-lg mx-auto lg:mx-0 font-medium">Reserve
                        sports complexes, convention centers, and parks in the city with our fast and transparent
                        digital system.</p>
                    <div class="flex flex-col sm:flex-row gap-6 items-center justify-center lg:justify-start">
                        <a href="#facilities"
                            class="w-full sm:w-auto bg-white text-slate-950 px-12 py-5 rounded-2xl font-black text-lg hover:bg-orange-600 hover:text-white transition-all transform hover:-translate-y-1 shadow-2xl">BROWSE
                            VENUES</a>
                        <div class="flex items-center gap-6 px-8 border-l-0 sm:border-l-2 border-white/20">
                            <span class="text-5xl font-black text-orange-500">ALL DAY</span>
                            <span
                                class="text-[11px] text-white/50 uppercase font-black leading-tight text-left">Instant<br>Online
                                Access</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="facilities" class="py-32 bg-slate-950/50 backdrop-blur-xl border-t border-white/5">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                    <div>
                        <span class="text-orange-500 font-black tracking-widest text-xs uppercase">Venues</span>
                        <h2 class="text-4xl md:text-5xl font-black mt-2">Featured Facilities</h2>
                    </div>
                    <p class="text-white/50 max-w-xs text-sm">Top-rated public spaces managed by the LGU for your
                        events.</p>
                </div>

                <!-- Facility Cards Grid -->

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($facilities as $facility)
                        <div onclick="openFacilityModal({{ $facility->facility_id }})"
                            class="facility-card relative group rounded-[2.5rem] overflow-hidden aspect-[4/5] bg-slate-900 shadow-2xl cursor-pointer">
                            @if($facility->image_path)
                                <img src="{{ url('/files/' . $facility->image_path) }}"
                                    alt="{{ $facility->name }}"
                                    class="w-full h-full object-cover transition duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-white/20"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
                                </div>
                            @endif
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent opacity-90 transition duration-500">
                            </div>
                            <div class="absolute bottom-0 left-0 p-8">
                                <h3 class="text-2xl font-bold mb-1">{{ $facility->name }}</h3>
                                <p class="text-white/60 text-xs mb-4 uppercase tracking-widest">{{ $facility->lguCity?->city_name ?? $facility->city ?? '' }}</p>
                                <span
                                    class="text-[10px] font-bold bg-orange-600 px-3 py-1 rounded-full uppercase tracking-tighter">View
                                    Details</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>



        <section id="contact" class="py-32 bg-black/40 backdrop-blur-3xl border-t border-white/5">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                    <div>
                        <span class="text-orange-500 font-black tracking-widest text-xs uppercase">Support</span>
                        <h2 class="text-5xl md:text-6xl font-black mt-4 mb-8">Get in <span
                                class="text-orange-500 italic">touch.</span></h2>
                        <p class="text-white/50 text-lg mb-12 max-w-md leading-relaxed">Have questions about our
                            facilities? We are here to assist you with your inquiries.</p>
                        <div class="space-y-6">
                            <a href="https://facebook.com" target="_blank" class="flex items-center gap-6 group w-fit">
                                <div
                                    class="h-14 w-14 rounded-2xl bg-blue-600/10 flex items-center justify-center text-blue-500 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <i data-lucide="facebook"></i>
                                </div>
                                <span class="font-bold text-white/80 group-hover:text-white transition">LGU
                                    Official</span>
                            </a>
                            <a href="viber://chat?number=yournumber" class="flex items-center gap-6 group w-fit">
                                <div
                                    class="h-14 w-14 rounded-2xl bg-purple-600/10 flex items-center justify-center text-purple-500 group-hover:bg-purple-600 group-hover:text-white transition-all">
                                    <i data-lucide="phone-call"></i>
                                </div>
                                <span class="font-bold text-white/80 group-hover:text-white transition">Viber Support
                                    Channel</span>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white/5 p-8 md:p-12 rounded-[3rem] border border-white/10 shadow-2xl">
                        <form id="contactForm" class="space-y-6">
                            <input type="hidden" name="access_key" value="b710b690-34b8-4f0d-8d91-727184e8d8db">
                            <input type="checkbox" name="botcheck" class="hidden" style="display: none;">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black uppercase tracking-widest text-white/30 ml-1">Full
                                        Name</label>
                                    <input type="text" name="name" required placeholder="Your Name"
                                        class="contact-input w-full px-6 py-4 rounded-2xl text-white">
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black uppercase tracking-widest text-white/30 ml-1">Email</label>
                                    <input type="email" name="email" required placeholder="email@example.com"
                                        class="contact-input w-full px-6 py-4 rounded-2xl text-white">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-white/30 ml-1">Message</label>
                                <textarea name="message" required rows="4" placeholder="How can we help?"
                                    class="contact-input w-full px-6 py-4 rounded-2xl text-white resize-none"></textarea>
                            </div>
                            <button type="submit" id="submitBtn"
                                class="w-full bg-orange-600 hover:bg-orange-700 text-white py-5 rounded-2xl font-black text-lg transition shadow-xl shadow-orange-900/20 uppercase tracking-widest">Send
                                Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>

        // Modals Script — built dynamically from DB
        const facilitiesData = {!! $facilitiesJson !!};

        const loginUrl = "{{ route('login') }}";

        function openFacilityModal(key) {
            const data = facilitiesData[key];

            Swal.fire({
                html: `
                    <div style="display:grid;grid-template-columns:1fr 1fr;text-align:left;gap:0;min-height:380px;">
                        <div style="overflow:hidden;background:#1e293b;display:flex;align-items:center;justify-content:center;">
                            ${data.img
                                ? `<img src="${data.img}" style="width:100%;height:100%;object-fit:cover;" alt="${data.title}">`
                                : `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>`
                            }
                        </div>
                        <div style="padding:2rem 2.5rem;display:flex;flex-direction:column;gap:1.25rem;">
                            <div>
                                <h2 style="font-size:1.75rem;font-weight:900;color:#fff;margin:0;">${data.title}</h2>
                                <p style="color:#f97316;font-weight:700;font-size:0.875rem;margin-top:4px;display:flex;align-items:center;gap:6px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    ${data.location}
                                </p>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                                <div style="background:rgba(255,255,255,0.05);padding:0.875rem;border-radius:1rem;border:1px solid rgba(255,255,255,0.05);">
                                    <span style="font-size:10px;text-transform:uppercase;font-weight:900;color:rgba(255,255,255,0.4);display:block;margin-bottom:2px;">Capacity</span>
                                    <span style="font-size:1.1rem;font-weight:700;color:#fff;">${data.capacity}</span>
                                </div>
                                <div style="background:rgba(255,255,255,0.05);padding:0.875rem;border-radius:1rem;border:1px solid rgba(255,255,255,0.05);">
                                    <span style="font-size:10px;text-transform:uppercase;font-weight:900;color:rgba(255,255,255,0.4);display:block;margin-bottom:2px;">Base Hours</span>
                                    <span style="font-size:1.1rem;font-weight:700;color:#fff;">${data.hours}</span>
                                </div>
                                <div style="background:rgba(255,255,255,0.05);padding:0.875rem;border-radius:1rem;border:1px solid rgba(255,255,255,0.05);">
                                    <span style="font-size:10px;text-transform:uppercase;font-weight:900;color:rgba(255,255,255,0.4);display:block;margin-bottom:2px;">Base Rate</span>
                                    <span style="font-size:1.1rem;font-weight:700;color:#fb923c;">${data.rate}</span>
                                </div>
                                <div style="background:rgba(255,255,255,0.05);padding:0.875rem;border-radius:1rem;border:1px solid rgba(255,255,255,0.05);">
                                    <span style="font-size:10px;text-transform:uppercase;font-weight:900;color:rgba(255,255,255,0.4);display:block;margin-bottom:2px;">Ext. Rate</span>
                                    <span style="font-size:1.1rem;font-weight:700;color:#fff;">${data.extRate}</span>
                                </div>
                            </div>
                            <div>
                                <span style="font-size:10px;text-transform:uppercase;font-weight:900;color:rgba(255,255,255,0.4);display:block;margin-bottom:6px;letter-spacing:0.1em;">Description</span>
                                <p style="color:rgba(255,255,255,0.7);font-size:0.875rem;line-height:1.6;margin:0;">${data.desc}</p>
                            </div>
                            <a href="${loginUrl}" style="display:block;width:100%;background:#ea580c;color:#fff;text-align:center;padding:1rem;border-radius:0.75rem;font-weight:700;text-decoration:none;transition:background 0.2s;margin-top:auto;" onmouseover="this.style.background='#c2410c'" onmouseout="this.style.background='#ea580c'">RESERVE NOW</a>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                background: '#0f172a',
                color: '#ffffff',
                width: '700px',
                padding: 0,
                customClass: {
                    popup: 'facility-swal-popup',
                    closeButton: 'facility-swal-close'
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInUp animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutDown animate__faster'
                }
            });
        }

        lucide.createIcons();
        const bg = document.getElementById('mainBg');
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuPath = document.getElementById('menu-path');

        menuBtn.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.contains('active');
            if (isOpen) {
                mobileMenu.classList.remove('active');
                menuPath.setAttribute('d', 'M4 6h16M4 12h16m-7 6h7');
            } else {
                mobileMenu.classList.add('active');
                menuPath.setAttribute('d', 'M6 18L18 6M6 6l12 12');
            }
        });

        document.querySelectorAll('.mobile-link').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                menuPath.setAttribute('d', 'M4 6h16M4 12h16m-7 6h7');
            });
        });

        window.addEventListener('mousemove', (e) => {
            if (window.innerWidth > 1024) {
                const x = (e.clientX / window.innerWidth - 0.5) * 20;
                const y = (e.clientY / window.innerHeight - 0.5) * 20;
                bg.style.transform = `scale(1.05) translate(${x}px, ${y}px)`;
            }
        });

        const contactForm = document.getElementById('contactForm');
        const submitBtn = document.getElementById('submitBtn');

        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            submitBtn.disabled = true;
            submitBtn.innerText = "Sending...";

            const formData = new FormData(contactForm);
            const object = Object.fromEntries(formData);
            const json = JSON.stringify(object);

            fetch('https://api.web3forms.com/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: json
            })
                .then(async (response) => {
                    let res = await response.json();
                    if (response.status == 200) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Your message has been sent successfully.',
                            icon: 'success',
                            confirmButtonColor: '#ea580c',
                            background: '#0f172a',
                            color: '#ffffff'
                        });
                        contactForm.reset();
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: res.message,
                            icon: 'error',
                            confirmButtonColor: '#ea580c'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Something went wrong. Please try again later.',
                        icon: 'error',
                        confirmButtonColor: '#ea580c'
                    });
                })
                .then(function () {
                    submitBtn.disabled = false;
                    submitBtn.innerText = "Send Message";
                });
        });
    </script>
</body>

</html>
