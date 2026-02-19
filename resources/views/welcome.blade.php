<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LGU Facility Reservation</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('Images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div onclick="openFacilityModal('buena')"
                        class="facility-card relative group rounded-[2.5rem] overflow-hidden aspect-[4/5] bg-slate-900 shadow-2xl cursor-pointer">
                        <img src="{{ asset('Images/Buena_Park_Caloocan.jpg') }}"
                            class="w-full h-full object-cover transition duration-700">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent opacity-90 transition duration-500">
                        </div>
                        <div class="absolute bottom-0 left-0 p-8">
                            <h3 class="text-2xl font-bold mb-1">Buena Park</h3>
                            <p class="text-white/60 text-xs mb-4 uppercase tracking-widest">Clubhouse & Events</p>
                            <span
                                class="text-[10px] font-bold bg-orange-600 px-3 py-1 rounded-full uppercase tracking-tighter">View
                                Details</span>
                        </div>
                    </div>

                    <div onclick="openFacilityModal('sports')"
                        class="facility-card relative group rounded-[2.5rem] overflow-hidden aspect-[4/5] bg-slate-900 shadow-2xl cursor-pointer">
                        <img src="{{ asset('Images/Caloocan_Sports_Complex.jpg') }}"
                            class="w-full h-full object-cover transition duration-700">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent opacity-90 transition duration-500">
                        </div>
                        <div class="absolute bottom-0 left-0 p-8">
                            <h3 class="text-2xl font-bold mb-1">Sports Complex</h3>
                            <p class="text-white/60 text-xs mb-4 uppercase tracking-widest">Indoor Courts</p>
                            <span
                                class="text-[10px] font-bold bg-orange-600 px-3 py-1 rounded-full uppercase tracking-tighter">View
                                Details</span>
                        </div>
                    </div>

                    <div onclick="openFacilityModal('mice')"
                        class="facility-card relative group rounded-[2.5rem] overflow-hidden aspect-[4/5] bg-slate-900 shadow-2xl cursor-pointer">
                        <img src="{{ asset('Images/MICE_Center_QC.jpg') }}"
                            class="w-full h-full object-cover transition duration-700">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent opacity-90 transition duration-500">
                        </div>
                        <div class="absolute bottom-0 left-0 p-8">
                            <h3 class="text-2xl font-bold mb-1">M.I.C.E. Center</h3>
                            <p class="text-white/60 text-xs mb-4 uppercase tracking-widest">Conventions & Expos</p>
                            <span
                                class="text-[10px] font-bold bg-orange-600 px-3 py-1 rounded-full uppercase tracking-tighter">View
                                Details</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal Cards -->

        <div id="facilityModal" class="fixed inset-0 z-[150] hidden items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md" onclick="closeModal()"></div>
            <div
                class="relative bg-slate-900 border border-white/10 w-full max-w-2xl rounded-[3rem] overflow-hidden shadow-2xl transform transition-all">
                <button onclick="closeModal()" class="absolute top-6 right-6 text-white/50 hover:text-white z-10">
                    <i data-lucide="x"></i>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="h-64 md:h-auto overflow-hidden">
                        <img id="modalImg" src="" class="w-full h-full object-cover">
                    </div>
                    <div class="p-8 md:p-10 space-y-6">
                        <div>
                            <h2 id="modalTitle" class="text-3xl font-black text-white"></h2>
                            <p id="modalLocation"
                                class="text-orange-500 font-bold text-sm flex items-center gap-2 mt-1"></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                <span class="text-[10px] uppercase font-black text-white/40 block mb-1">Capacity</span>
                                <span id="modalCapacity" class="text-lg font-bold"></span>
                            </div>
                            <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                <span class="text-[10px] uppercase font-black text-white/40 block mb-1">Base
                                    Hours</span>
                                <span id="modalHours" class="text-lg font-bold"></span>
                            </div>
                            <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                <span class="text-[10px] uppercase font-black text-white/40 block mb-1">Base Rate</span>
                                <span id="modalRate" class="text-lg font-bold text-orange-400"></span>
                            </div>
                            <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                <span class="text-[10px] uppercase font-black text-white/40 block mb-1">Ext. Rate</span>
                                <span id="modalExtRate" class="text-lg font-bold"></span>
                            </div>
                        </div>

                        <div>
                            <span
                                class="text-[10px] uppercase font-black text-white/40 block mb-2 tracking-widest">Description</span>
                            <p id="modalDesc" class="text-white/70 text-sm leading-relaxed"></p>
                        </div>

                        <a href="{{ route('login') }}"
                            class="block w-full bg-orange-600 text-center py-4 rounded-xl font-bold hover:bg-orange-700 transition">RESERVE
                            NOW</a>
                    </div>
                </div>
            </div>
        </div>



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

        // Modals Script
        const facilitiesData = {
            buena: {
                title: "Buena Park",
                location: "Caloocan City",
                capacity: "800 pax",
                rate: "₱5,000.00",
                extRate: "₱2,000/hr",
                hours: "3 Hours",
                img: "{{ asset('Images/Buena_Park_Caloocan.jpg') }}",
                desc: "Open-air community park suitable for outdoor gatherings and events. Perfect for weekend private events and community celebrations."
            },
            sports: {
                title: "Sports Complex",
                location: "Caloocan City",
                capacity: "3,000 pax",
                rate: "₱18,000.00",
                extRate: "₱7,000/hr",
                hours: "3 Hours",
                img: "{{ asset('Images/Caloocan_Sports_Complex.jpg') }}",
                desc: "Multi-purpose indoor sports facility for athletic events and large gatherings. Ideal for tournaments, competitions, and sporting events."
            },
            mice: {
                title: "M.I.C.E. Center",
                location: "Quezon City",
                capacity: "1,200 pax",
                rate: "₱20,000.00",
                extRate: "₱8,000/hr",
                hours: "4 Hours",
                img: "{{ asset('Images/MICE_Center_QC.jpg') }}",
                desc: "Large-scale venue for conventions, exhibits, and major events. Professional-grade 4-storey M.I.C.E. Center facility. Currently restricted to QC-LGU departments pending ordinance approval for public use."
            }
        };

        function openFacilityModal(key) {
            const data = facilitiesData[key];
            document.getElementById('modalTitle').innerText = data.title;
            document.getElementById('modalLocation').innerHTML = `<i data-lucide="map-pin" class="w-4 h-4"></i> ${data.location}`;
            document.getElementById('modalCapacity').innerText = data.capacity;
            document.getElementById('modalRate').innerText = data.rate;
            document.getElementById('modalExtRate').innerText = data.extRate;
            document.getElementById('modalHours').innerText = data.hours;
            document.getElementById('modalDesc').innerText = data.desc;
            document.getElementById('modalImg').src = data.img;

            const modal = document.getElementById('facilityModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            lucide.createIcons(); // Refresh icons inside modal
            document.body.style.overflow = 'hidden'; // Prevent scroll
        }

        function closeModal() {
            const modal = document.getElementById('facilityModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
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
