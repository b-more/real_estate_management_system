<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Premium Plots - Smart Real Estate Solutions</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        zambia: {
                            green: '#198754',
                            red: '#CE1126',
                            orange: '#FF8200',
                            black: '#000000'
                        }
                    },
                    fontFamily: {
                        sans: ['Figtree', ...tailwind.defaultTheme.fontFamily.sans],
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-white font-sans antialiased">
    <!-- Header -->
    <header class="fixed w-full z-50 bg-white/90 backdrop-blur-md shadow-sm">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-zambia-green">Premium Plots</h1>
                    </div>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-zambia-green">Features</a>
                    <a href="#solutions" class="text-gray-700 hover:text-zambia-green">Solutions</a>
                    <a href="#contact" class="text-gray-700 hover:text-zambia-green">Contact</a>
                </div>
                <div>
                    <a href="/admin" class="rounded-md bg-zambia-green px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-opacity-90 transition-all">
                        Admin Portal
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <div class="relative h-screen">
        <img src="{{ asset('images/hero.jpg') }}" class="absolute h-full w-full object-cover" alt="Real Estate" />
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-black/40"></div>
            <div class="relative h-full flex items-center">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="md:w-2/3">
                        <h2 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            Smart Property Management Solutions
                        </h2>
                        <p class="mt-6 max-w-xl text-xl text-gray-200">
                            Comprehensive real estate management system designed for Zambian property market.
                        </p>
                        <div class="mt-8 flex gap-4">
                            <a href="#contact" class="rounded-md bg-zambia-green px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-opacity-90 transition-all">
                                Get Started
                            </a>
                            <a href="#features" class="rounded-md bg-white px-6 py-3 text-lg font-semibold text-zambia-green shadow-lg hover:bg-gray-50 transition-all">
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-white py-12">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-zambia-green">100%</div>
                        <div class="mt-2 text-sm text-gray-600">Digital Management</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-zambia-red">24/7</div>
                        <div class="mt-2 text-sm text-gray-600">System Access</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-zambia-orange">4+</div>
                        <div class="mt-2 text-sm text-gray-600">User Roles</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-zambia-green">Real-time</div>
                        <div class="mt-2 text-sm text-gray-600">Analytics</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div id="features" class="py-24 bg-gray-50">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-zambia-black">Comprehensive Features</h2>
                    <p class="mt-4 text-gray-600">Everything you need to manage your real estate business</p>
                </div>
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature Cards -->
                    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-all">
                        <div class="rounded-lg bg-zambia-green/10 p-3 w-fit">
                            <svg class="h-6 w-6 text-zambia-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Plot Inventory Management</h3>
                        <p class="mt-2 text-gray-600">Comprehensive management of plot details, pricing, and availability status.</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-all">
                        <div class="rounded-lg bg-zambia-red/10 p-3 w-fit">
                            <svg class="h-6 w-6 text-zambia-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Customer Management</h3>
                        <p class="mt-2 text-gray-600">Track customer interactions, preferences, and sale progress.</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-all">
                        <div class="rounded-lg bg-zambia-orange/10 p-3 w-fit">
                            <svg class="h-6 w-6 text-zambia-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Payment Tracking</h3>
                        <p class="mt-2 text-gray-600">Monitor payments, generate receipts, and track outstanding balances.</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-all">
                        <div class="rounded-lg bg-zambia-green/10 p-3 w-fit">
                            <svg class="h-6 w-6 text-zambia-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Analytics & Reporting</h3>
                        <p class="mt-2 text-gray-600">Comprehensive insights into sales performance and business metrics.</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-all">
                        <div class="rounded-lg bg-zambia-red/10 p-3 w-fit">
                            <svg class="h-6 w-6 text-zambia-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Document Management</h3>
                        <p class="mt-2 text-gray-600">Secure storage and easy retrieval of all property-related documents.</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-xl transition-all">
                        <div class="rounded-lg bg-zambia-orange/10 p-3 w-fit">
                            <svg class="h-6 w-6 text-zambia-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold">Role-Based Access</h3>
                        <p class="mt-2 text-gray-600">Secure access controls for admins, agents, and staff members.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div id="contact" class="bg-zambia-green py-16">
            <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold text-white mb-8">Ready to Transform Your Real Estate Business?</h2>
                <div class="max-w-2xl mx-auto">
                    <p class="text-gray-100 mb-8">
                        Get in touch with us to learn how our system can streamline your operations
                    </p>
                    <a href="mailto:contact@ontechsolutions.com" class="inline-block rounded-md bg-white px-6 py-3 text-lg font-semibold text-zambia-green shadow-lg hover:bg-gray-50 transition-all">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Premium Plots</h3>
                    <p class="text-gray-400">Smart property management solutions for the modern real estate business.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white">Features</a></li>
                        <li><a href="#solutions" class="text-gray-400 hover:text-white">Solutions</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white">Contact</a></li>
                        <li><a href="/admin" class="text-gray-400 hover:text-white">Admin Portal</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2">
                        <li class="text-gray-400">Email: contact@ontechsolutions.com</li>
                        <li class="text-gray-400">Phone: +260 XXX XXX XXX</li>
                        <li class="text-gray-400">Location: Lusaka, Zambia</li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-gray-800">
                <div class="text-center text-gray-400">
                    <p>&copy; 2024 Premium Plots. All rights reserved.</p>
                    <p class="mt-2">Developed by <a href="https://ontechsolutions.com" class="text-zambia-green hover:text-zambia-green/80">Ontech Solutions</a></p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>