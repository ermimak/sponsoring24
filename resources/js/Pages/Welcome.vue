<template>
    <MainLayout>
        <!-- Hero Section -->
        <section class="relative bg-white">
            <div class="absolute inset-0">
                <img src="/images/hero-bg.jpg" alt="Fundraising" class="w-full h-full object-cover object-center opacity-80" />
                <div class="absolute inset-0 bg-gradient-to-b from-black/40 to-white/0"></div>
            </div>
            <div class="relative z-10 max-w-5xl mx-auto px-4 py-24 flex flex-col items-start">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 drop-shadow-lg">Your solution for successful fundraising projects</h1>
                <p class="text-lg md:text-2xl text-white mb-8 max-w-2xl drop-shadow">Sponsorship runs, crowdfunding, donation pages. Let us show you how you can reach your fundraising goals with fundoo in Switzerland.</p>
                <a href="#" class="bg-lime-400 hover:bg-lime-500 text-lg font-bold px-8 py-3 rounded-full shadow">Start project</a>
            </div>
        </section>

        <!-- Partner Logos -->
        <section class="bg-white py-8">
            <div class="max-w-5xl mx-auto flex flex-wrap justify-center items-center gap-8 opacity-80">
                <img v-for="n in 10" :key="n" :src="`/images/partner${n}.png`" alt="Partner logo" class="h-10" />
            </div>
        </section>

        <!-- Reaching the goal together -->
        <section class="bg-gradient-to-b from-white to-[#6d4aff0d] py-12 text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-purple-800 mb-4">Reaching the goal together</h2>
            <p class="text-lg text-gray-700 max-w-2xl mx-auto">Hundreds of clubs, organizations and educational institutions rely on our professional tools and personal service.</p>
        </section>

        <!-- Stats -->
        <section class="bg-gradient-to-b from-[#6d4aff0d] to-white py-12">
            <div class="max-w-5xl mx-auto flex flex-wrap justify-center gap-12 text-center">
                <div>
                    <div class="text-3xl md:text-5xl font-extrabold text-purple-700">1,795</div>
                    <div class="text-gray-700 mt-2">Projects</div>
                </div>
                <div>
                    <div class="text-3xl md:text-5xl font-extrabold text-purple-700">28,430,448</div>
                    <div class="text-gray-700 mt-2">Donations in CHF</div>
                </div>
                <div>
                    <div class="text-3xl md:text-5xl font-extrabold text-purple-700">563,784</div>
                    <div class="text-gray-700 mt-2">Supporters</div>
                </div>
            </div>
        </section>

        <!-- Current Projects -->
        <section class="py-16 bg-gradient-to-b from-white to-[#f6f3ff]">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-10">Current projects</h2>
            <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
                <ProjectCard v-for="project in projects" :key="project.id" :project="project" />
            </div>
            <div class="flex justify-center mt-8">
                <a href="#" class="bg-lime-400 hover:bg-lime-500 text-lg font-bold px-8 py-3 rounded-full shadow">Show all projects</a>
            </div>
        </section>

        <!-- Donation Project Info -->
        <section class="py-16 bg-white flex flex-col md:flex-row items-center max-w-5xl mx-auto gap-12">
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-4">Your donation project</h3>
                <p class="mb-4 text-gray-700">Collect donation and sponsorship money with ease. We have the right solution for every project.</p>
                <ul class="list-disc ml-6 text-gray-600 mb-4">
                    <li>Donation pages, sponsorship runs, crowdfunding</li>
                    <li>Customizable for schools, clubs, and organizations</li>
                    <li>Easy setup and personal support</li>
                </ul>
                <a href="#" class="bg-lime-400 hover:bg-lime-500 text-lg font-bold px-8 py-3 rounded-full shadow">Start project</a>
            </div>
            <div class="flex-1 flex justify-center">
                <img src="/images/piggy-illustration.png" alt="Donation project" class="w-72" />
            </div>
        </section>

        <!-- Our Tools -->
        <section class="py-16 bg-gradient-to-b from-[#f6f3ff] to-white flex flex-col md:flex-row items-center max-w-5xl mx-auto gap-12">
            <div class="flex-1 flex justify-center">
                <img src="/images/tools-illustration.png" alt="Our tools" class="w-72" />
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-4">Our tools</h3>
                <p class="mb-4 text-gray-700">Manage fundraising projects, sponsors, evidence, manage payments, issue donation receipts, and see all statistics and results in real time.</p>
                <ul class="list-disc ml-6 text-gray-600 mb-4">
                    <li>Full automation: from fundraising setup to communication and payment</li>
                    <li>Donation statistics, participant tracking, and more</li>
                    <li>Different donation models</li>
                </ul>
            </div>
        </section>

        <!-- With fundoo you can collect... -->
        <section class="py-16 bg-white flex flex-col md:flex-row items-center max-w-5xl mx-auto gap-12">
            <div class="flex-1">
                <img src="/images/collect-donations.jpg" alt="Collect donations" class="rounded-xl shadow-lg w-full" />
            </div>
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-4">With fundoo you can collect donations easily and effortlessly</h3>
                <a href="#" class="bg-lime-400 hover:bg-lime-500 text-lg font-bold px-8 py-3 rounded-full shadow">Get to know fundoo</a>
            </div>
        </section>

        <!-- Free Consultation & Demo -->
        <section class="py-16 bg-gradient-to-b from-[#f6f3ff] to-white flex flex-col md:flex-row items-center max-w-5xl mx-auto gap-12">
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-4">Free consultation and online demo</h3>
                <p class="mb-4 text-gray-700">Whether school, club or organization: Collect donations easily and efficiently. We'll show you how our tools make your fundraising project successful. Book a free online demo or personal consultation now.</p>
                <a href="#" class="bg-purple-600 hover:bg-purple-700 text-lg font-bold px-8 py-3 rounded-full shadow text-white">Request online demo</a>
            </div>
            <div class="flex-1 flex justify-center">
                <img src="/images/consultation-illustration.png" alt="Consultation" class="w-72" />
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-16 bg-white">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-10">What our partners say</h2>
            <div class="max-w-3xl mx-auto bg-gray-50 rounded-xl shadow-lg p-8 flex flex-col md:flex-row items-center gap-8">
                <img src="/images/partner-avatar.jpg" alt="Partner" class="w-24 h-24 rounded-full object-cover border-4 border-purple-200" />
                <div>
                    <p class="text-lg text-gray-700 mb-2">As Europe's largest youth ice hockey organization, we've finally found the ideal modern fundraising tool. Fundoo made fundraising easy, flexible for our clubs/members and significantly reduces our administrative burden.</p>
                    <div class="font-bold text-gray-900">Marco Müller &amp; Ueli Engler, SIHF Youth, Swiss Ice Hockey</div>
                </div>
            </div>
        </section>

        <!-- News -->
        <section class="py-16 bg-gradient-to-b from-[#f6f3ff] to-white">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-10">News from fundoo</h2>
            <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col gap-4">
                    <h3 class="text-lg font-bold">Step by step</h3>
                    <p class="text-gray-700">We're constantly adding new features and expanding fundoo for you. Stay tuned for the next program release directly out of fundoo!</p>
                    <a href="#" class="text-purple-700 underline font-medium">To the news</a>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col gap-4">
                    <h3 class="text-lg font-bold">Project photos and thank you!</h3>
                    <p class="text-gray-700">Enjoy highlights and thank yous from our latest fundraising projects and events.</p>
                    <a href="#" class="text-purple-700 underline font-medium">Read more</a>
                </div>
            </div>
        </section>

        <!-- Footer handled by MainLayout -->
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import ProjectCard from '@/Components/ProjectCard.vue';

const projects = [
    {
        id: 1,
        title: 'Sponsored Run 2023',
        description: 'FCA club run for new equipment. Every kilometer counts! Join us in supporting our youth teams.',
        image: '/images/project1.jpg',
        raised: 'CHF 12,500',
    },
    {
        id: 2,
        title: 'Sponsored Run 2023 Rudolf Steiner School',
        description: 'Help us raise funds for new classroom materials and outdoor learning spaces.',
        image: '/images/project2.jpg',
        raised: 'CHF 8,200',
    },
    {
        id: 3,
        title: 'Local School Run',
        description: 'Support our annual run to promote health and fitness among students.',
        image: '/images/project3.jpg',
        raised: 'CHF 5,900',
    },
];
</script> 