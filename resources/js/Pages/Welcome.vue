<template>
    <MainLayout>
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative max-w-6xl mx-auto mt-4" role="alert">
            <strong class="font-bold">Success! </strong>
            <span class="block sm:inline">{{ $page.props.flash.success }}</span>
        </div>

        <!-- Hero Section -->
        <section class="relative bg-white">
            <div class="absolute inset-0">
                <img src="/images/hero-bg.jpg" alt="Sponsoring" class="w-full h-full object-cover object-center opacity-90" />
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-purple-900/20"></div>
            </div>
            <div class="relative z-10 max-w-6xl mx-auto px-4 py-32 flex flex-col items-start">
                <div class="mb-4 inline-block px-4 py-1 bg-white/20 backdrop-blur-md rounded-full text-white font-medium">
                    #1 Sponsoring Platform in Switzerland
                </div>
                <h1 class="text-4xl md:text-7xl font-black text-white mb-8 drop-shadow-lg leading-tight tracking-tight">Elevate Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-lime-400 to-cyan-400">Sponsoring</span> Experience</h1>
                <p class="text-xl md:text-2xl text-white mb-12 max-w-2xl drop-shadow font-light leading-relaxed">Sponsorship runs, crowdfunding, donation campaigns. Reach your fundraising goals with Sponsoring24 — Switzerland's premier digital sponsoring platform.</p>
                <div class="flex flex-wrap gap-5">
                    <a href="#" class="bg-gradient-to-r from-lime-500 to-emerald-500 hover:from-lime-600 hover:to-emerald-600 text-lg font-bold px-10 py-5 rounded-full shadow-lg transition-all duration-300 text-white transform hover:scale-105">Launch Your Campaign</a>
                    <a href="#" class="bg-white/10 backdrop-blur-md hover:bg-white/20 text-lg font-bold px-10 py-5 rounded-full shadow-lg text-white border border-white/30 transition-all duration-300 transform hover:scale-105">Discover How</a>
                </div>
            </div>
        </section>

        <!-- Notifications Panel
        <section v-if="notifications && notifications.length > 0" class="bg-white py-8">
            <div class="max-w-6xl mx-auto px-4">
                <NotificationDropdown />
            </div>
        </section> -->
        
        <!-- Referral Program -->
        <section class="bg-gradient-to-r from-purple-50 to-blue-50 py-16">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center mb-10">
                    <span class="inline-block px-4 py-1 bg-purple-100 text-purple-700 font-medium rounded-full mb-4">Special Offer</span>
                    <h2 class="text-3xl md:text-4xl font-black mb-6 bg-clip-text text-transparent bg-gradient-to-r from-purple-700 to-blue-700">Join Our Referral Program</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">Share Sponsoring24 with your network and earn rewards!</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center text-center">
                        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Earn CHF 100</h3>
                        <p class="text-gray-600 mb-6">For each new user who signs up using your referral link and purchases an annual license, you'll receive CHF 100 in bonus credits.</p>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center text-center">
                        <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Your Friends Save CHF 50</h3>
                        <p class="text-gray-600 mb-6">New users who register with your referral link will receive a CHF 50 discount on their first annual Sponsoring24 license.</p>
                    </div>
                </div>
                
                <div class="mt-12 text-center">
                    <p class="text-gray-600 mb-6">Already a user? Log in to your account to find your unique referral link.</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="/register" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold px-8 py-3 rounded-full shadow-lg transition-all duration-300">Sign Up Now</a>
                        <a href="/login" class="bg-white hover:bg-gray-100 text-gray-800 font-bold px-8 py-3 rounded-full shadow-lg border border-gray-200 transition-all duration-300">Log In</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Partner Logos -->
        <section class="bg-white py-12">
            <div class="max-w-6xl mx-auto flex flex-wrap justify-center items-center gap-8 opacity-80">
                <img v-for="n in 10"
                     :key="n"
                     :src="`/images/partner${n}.png`"
                     alt="Partner logo"
                     class="h-12 transition-all duration-300" />
            </div>
        </section>

        <!-- Referral Tracking -->
        <section v-if="referralInfo" class="bg-purple-50 py-8">
            <div class="max-w-6xl mx-auto px-4">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-purple-800 mb-4">Referral Information</h3>
                    <div class="space-y-4">
                        <div v-if="referralInfo.success" class="text-green-600">
                            <p class="font-semibold">Successfully registered with referral code!</p>
                            <p>You will receive bonus credits once your account is verified.</p>
                        </div>
                        <div v-else class="text-red-600">
                            <p class="font-semibold">Invalid referral code</p>
                            <p>Please check your referral code and try again.</p>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p>Your referral code: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ referralInfo.code }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Reaching the goal together -->
        <section class="bg-gradient-to-b from-white to-[#6d4aff0d] py-16 text-center">
            <div class="max-w-4xl mx-auto px-4">
                <h2 class="text-3xl md:text-5xl font-extrabold text-purple-800 mb-6 leading-tight">Reaching the goal together</h2>
                <p class="text-lg md:text-xl text-gray-700 max-w-3xl mx-auto">Hundreds of clubs, organizations and educational institutions rely on our professional tools and personal service to achieve their fundraising goals.</p>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-24 bg-gradient-to-r from-purple-50 to-blue-50">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center mb-16">
                    <span class="inline-block px-4 py-1 bg-purple-100 text-purple-700 font-medium rounded-full mb-4">Our Impact</span>
                    <h2 class="text-4xl md:text-5xl font-black mb-6 bg-clip-text text-transparent bg-gradient-to-r from-purple-700 to-blue-700">Driving Real Results</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">We've empowered organizations across Switzerland to achieve their sponsoring goals with our innovative platform.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-10 rounded-2xl shadow-xl text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl border border-gray-100">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-purple-100 text-purple-600 mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="text-5xl font-black text-gray-800 mb-2">{{ stats.projects }}</div>
                        <div class="text-gray-600 font-medium text-lg">Successful Campaigns</div>
                    </div>
                    <div class="bg-white p-10 rounded-2xl shadow-xl text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl border border-gray-100">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-5xl font-black text-gray-800 mb-2">CHF {{ stats.donations }}</div>
                        <div class="text-gray-600 font-medium text-lg">Total Funds Raised</div>
                    </div>
                    <div class="bg-white p-10 rounded-2xl shadow-xl text-center transform transition-all duration-300 hover:scale-105 hover:shadow-2xl border border-gray-100">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="text-5xl font-black text-gray-800 mb-2">{{ stats.supporters }}</div>
                        <div class="text-gray-600 font-medium text-lg">Engaged Supporters</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Current Projects Section -->
        <section class="py-24 bg-white">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center mb-16">
                    <span class="inline-block px-4 py-1 bg-emerald-100 text-emerald-700 font-medium rounded-full mb-4">Discover</span>
                    <h2 class="text-4xl md:text-5xl font-black mb-6 bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-blue-600">Featured Campaigns</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">Explore our most impactful sponsoring campaigns and see how Sponsoring24 helps organizations achieve their goals.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <ProjectCard v-if="projects" v-for="project in projects" :key="project.id" :project="project" />
                </div>
                <div class="text-center mt-16">
                    <a href="#" class="inline-block bg-gradient-to-r from-emerald-500 to-blue-500 hover:from-emerald-600 hover:to-blue-600 text-white font-bold py-4 px-10 rounded-full shadow-lg transition-all duration-300 transform hover:scale-105">Explore All Campaigns</a>
                </div>
            </div>
        </section>

        <!-- Sponsoring Campaign Info -->
        <section class="py-24 bg-white">
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-16 px-4">
                <div class="flex-1">
                    <span class="inline-block px-4 py-1 bg-purple-100 text-purple-700 font-medium rounded-full mb-4">Your Campaign</span>
                    <h3 class="text-4xl font-black mb-6 bg-clip-text text-transparent bg-gradient-to-r from-purple-700 to-blue-700">Launch Your Sponsoring Campaign</h3>
                    <p class="mb-8 text-gray-700 text-xl leading-relaxed">Collect sponsorships and donations with ease using our powerful platform. We have the perfect solution for every type of campaign.</p>
                    <ul class="space-y-4 mb-10">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-emerald-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-lg text-gray-700">Customizable donation pages, sponsorship runs, and crowdfunding</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-emerald-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-lg text-gray-700">Tailored solutions for schools, sports clubs, and nonprofit organizations</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-emerald-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-lg text-gray-700">Easy setup with dedicated personal support</span>
                        </li>
                    </ul>
                    <a href="#" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-lg font-bold px-10 py-5 rounded-full shadow-lg inline-block transition-all duration-300 text-white transform hover:scale-105">Start Your Campaign</a>
                </div>
                <div class="flex-1 flex justify-center">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-purple-200 to-blue-200 rounded-full blur-lg opacity-70"></div>
                        <img src="/images/piggy-illustration.png" alt="Sponsoring Campaign" class="w-full max-w-lg relative drop-shadow-xl transform hover:rotate-2 transition-all duration-500" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Platform Tools -->
        <section class="py-24 bg-gradient-to-r from-blue-50 to-purple-50">
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row-reverse items-center gap-16 px-4">
                <div class="flex-1">
                    <span class="inline-block px-4 py-1 bg-blue-100 text-blue-700 font-medium rounded-full mb-4">Powerful Tools</span>
                    <h3 class="text-4xl font-black mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-purple-700">Advanced Platform Features</h3>
                    <p class="mb-8 text-gray-700 text-xl leading-relaxed">Manage your sponsoring campaigns, track supporters, process payments, issue receipts, and monitor all statistics and results in real-time with our comprehensive suite of tools.</p>
                    <ul class="space-y-4 mb-10">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-blue-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-lg text-gray-700">Complete automation: from campaign setup to communication and payment processing</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-blue-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-lg text-gray-700">Detailed analytics, participant tracking, and performance metrics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-blue-500 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-lg text-gray-700">Flexible sponsorship models to fit your organization's needs</span>
                        </li>
                    </ul>
                </div>
                <div class="flex-1 flex justify-center">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-blue-200 to-purple-200 rounded-full blur-lg opacity-70"></div>
                        <img src="/images/tools-illustration.png" alt="Platform Tools" class="w-full max-w-lg relative drop-shadow-xl transform hover:rotate-2 transition-all duration-500" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Final Call to Action -->
        <section class="py-24 bg-white">
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center gap-16 px-4">
                <div class="flex-1">
                    <div class="relative overflow-hidden rounded-3xl shadow-2xl">
                        <div class="absolute inset-0 bg-gradient-to-tr from-purple-600/30 to-blue-600/30 mix-blend-multiply"></div>
                        <img src="/images/collect-donations.jpg" alt="Sponsoring Platform" class="w-full object-cover h-96 transform hover:scale-105 transition-transform duration-700" />
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-4xl font-black mb-8 leading-tight bg-clip-text text-transparent bg-gradient-to-r from-purple-700 to-blue-700">With Sponsoring24, collecting donations becomes effortless and efficient</h3>
                    <p class="text-xl text-gray-700 mb-10 leading-relaxed">Join hundreds of organizations across Switzerland who have transformed their fundraising experience with our powerful platform.</p>
                    <a href="#" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-lg font-bold px-10 py-5 rounded-full shadow-lg inline-flex items-center transition-all duration-300 text-white transform hover:scale-105 group">
                        Discover Sponsoring24
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Free Consultation & Demo -->
        <section class="py-24 bg-gradient-to-r from-purple-50 to-blue-50">
            <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row gap-16 items-center">
                <div class="flex-1">
                    <span class="inline-block px-4 py-1 bg-purple-100 text-purple-700 font-medium rounded-full mb-4">Expert Support</span>
                    <h3 class="text-4xl font-black mb-6 bg-clip-text text-transparent bg-gradient-to-r from-purple-700 to-blue-700">Free Consultation & Live Demo</h3>
                    <p class="mb-10 text-gray-700 text-xl leading-relaxed">Whether you're a school, sports club, or nonprofit organization: Our platform makes collecting sponsorships easy and efficient. Let our experts show you how Sponsoring24 tools can make your campaign successful. Book a free online demo or personal consultation today.</p>
                    <a href="#" class="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-lg font-bold px-10 py-5 rounded-full shadow-lg text-white inline-block transition-all duration-300 transform hover:scale-105">Schedule Your Demo</a>
                </div>
                <div class="flex-1 flex justify-center">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-purple-200 to-blue-200 rounded-full blur-lg opacity-70"></div>
                        <img src="/images/consultation-illustration.png" alt="Consultation" class="w-full max-w-lg relative drop-shadow-xl transform hover:rotate-2 transition-all duration-500" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-24 bg-white">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center mb-16">
                    <span class="inline-block px-4 py-1 bg-blue-100 text-blue-700 font-medium rounded-full mb-4">Success Stories</span>
                    <h2 class="text-4xl md:text-5xl font-black mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-purple-700">What Our Partners Say</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">Discover how organizations across Switzerland have transformed their sponsoring experience with our platform.</p>
                </div>
                <div class="max-w-4xl mx-auto bg-gradient-to-r from-white to-blue-50 rounded-3xl shadow-xl p-10 md:p-12 flex flex-col md:flex-row items-center gap-10 border border-blue-100">
                    <div class="relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-purple-400 to-blue-400 rounded-full blur opacity-30"></div>
                        <img src="/images/partner-avatar.jpg" alt="Partner" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg relative z-10" />
                    </div>
                    <div>
                        <div class="flex mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                            </svg>
                        </div>
                        <p class="text-xl text-gray-700 mb-6 leading-relaxed">"As Europe's largest youth ice hockey organization, we've finally found the ideal modern sponsoring platform. Sponsoring24 made fundraising easy, flexible for our clubs/members and significantly reduces our administrative burden."</p>
                        <div class="font-bold text-gray-900 text-lg">Marco Müller &amp; Ueli Engler, SIHF Youth, Swiss Ice Hockey</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- News & Updates -->
        <section class="py-24 bg-gradient-to-r from-blue-50 to-purple-50">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center mb-16">
                    <span class="inline-block px-4 py-1 bg-emerald-100 text-emerald-700 font-medium rounded-full mb-4">Latest Updates</span>
                    <h2 class="text-4xl md:text-5xl font-black mb-6 bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-blue-600">News from Sponsoring24</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">Stay up to date with our latest features, success stories, and sponsoring tips.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div v-for="news in news" :key="news.id" class="bg-white rounded-3xl shadow-xl p-10 flex flex-col gap-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100"> 
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600">
                                <img :src="news.image_url" alt="News Image" class="w-full object-cover mb-4 rounded-lg">

                            </div>
                            <h3 class="text-2xl font-bold">{{ news.title }}</h3>
                            <p class="text-gray-700 text-lg leading-relaxed">{{ news.excerpt }}</p>
                            <a :href="route('content.news.show', news.id)" class="inline-flex items-center text-emerald-600 font-bold mt-4 group">
                                Read More
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-10 flex flex-col gap-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold">Partner Referral Program</h3>
                        <p class="text-gray-700 text-lg leading-relaxed">Recommend Sponsoring24 to your network and earn rewards through our partner referral program. Help others discover the best sponsoring platform in Switzerland.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 font-bold mt-4 group">
                            Join the Program
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                    <div class="bg-white rounded-3xl shadow-xl p-10 flex flex-col gap-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold">Partner Referral Program</h3>
                        <p class="text-gray-700 text-lg leading-relaxed">Recommend Sponsoring24 to your network and earn rewards through our partner referral program. Help others discover the best sponsoring platform in Switzerland.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 font-bold mt-4 group">
                            Join the Program
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer handled by MainLayout -->
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import ProjectCard from '@/Components/ProjectCard.vue';
import { usePage } from '@inertiajs/vue3';
import {route} from 'ziggy-js';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';
const page = usePage();
const referralInfo = page.props.referralInfo;
const notifications = page.props.notifications;
const projects = page.props.projects || [];
const stats = page.props.stats || {
    projects: 1795,
    donations: 28430448,
    supporters: 563784
};
const news = page.props.news || [];
console.log(news);
</script>