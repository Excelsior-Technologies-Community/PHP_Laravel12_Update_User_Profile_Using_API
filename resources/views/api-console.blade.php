<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 12 REST API Console & Interactive Playground</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"Fira Code"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        pre code { font-family: 'Fira Code', monospace; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/25">
                    <i class="fa-solid fa-bolt-lightning text-white text-lg"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-white via-slate-200 to-indigo-300 bg-clip-text text-transparent">API Playground</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">Laravel 12</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Sanctum</span>
                    </div>
                    <p class="text-xs text-slate-400">Interactive REST API Documentation & Live Console</p>
                </div>
            </div>

            <!-- Active Token Pill & Quick Controls -->
            <div class="flex items-center space-x-3">
                <div id="tokenBadge" class="hidden sm:flex items-center bg-slate-800/90 border border-slate-700/80 rounded-full px-3 py-1.5 text-xs text-slate-300 shadow-inner">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span>
                    <span class="font-medium mr-2">Bearer Token:</span>
                    <span id="tokenPreview" class="font-mono text-emerald-400 max-w-[120px] truncate">None</span>
                    <button onclick="copyToken()" title="Copy Token" class="ml-2 text-slate-400 hover:text-white transition">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                    <button onclick="clearToken()" title="Clear Token" class="ml-2 text-rose-400 hover:text-rose-300 transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <a href="#quick-docs" class="text-xs text-slate-400 hover:text-slate-200 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 transition border border-slate-700">
                    <i class="fa-solid fa-book-open mr-1.5 text-indigo-400"></i> Docs
                </a>
            </div>
        </div>
    </header>

    <!-- Main Workspace Layout -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 w-full grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: API Catalog & Endpoint Explorer (4 cols) -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Auth Status Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-indigo-400"></i> Authentication Key
                    </h3>
                    <span id="authStatusText" class="text-xs px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30">Unauthorized</span>
                </div>
                <div class="space-y-2">
                    <input type="text" id="sanctumTokenInput" placeholder="Paste Sanctum Bearer Token or Login below..."
                           class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs font-mono text-emerald-400 placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <div class="flex gap-2">
                        <button onclick="saveManualToken()" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition shadow-md shadow-indigo-600/20">
                            Set Token
                        </button>
                        <button onclick="clearToken()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium py-1.5 px-3 rounded-lg transition border border-slate-700">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Endpoint List Grouped by Category -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-3 shadow-xl max-h-[calc(100vh-280px)] overflow-y-auto space-y-4">
                
                <!-- Category: Auth -->
                <div>
                    <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                        <span><i class="fa-solid fa-key mr-1.5 text-amber-400"></i> Authentication</span>
                        <span class="text-[10px] text-slate-500">6 APIs</span>
                    </div>
                    <div class="space-y-1 mt-1">
                        <button onclick="loadEndpoint('register')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">POST</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/register</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('login')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">POST</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/login</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('logout')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">POST</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/logout</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('logout-all')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">POST</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/logout-all</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('sessions')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">GET</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/sessions</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('revoke-session')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">DEL</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/sessions/{id}</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                    </div>
                </div>

                <!-- Category: Profile & Extended Info -->
                <div>
                    <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                        <span><i class="fa-solid fa-id-card mr-1.5 text-indigo-400"></i> Profile & Extended</span>
                        <span class="text-[10px] text-slate-500">3 APIs</span>
                    </div>
                    <div class="space-y-1 mt-1">
                        <button onclick="loadEndpoint('get-profile')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">GET</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/profile</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('update-profile')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">PUT</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/profile</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('delete-account')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">DEL</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/profile</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                    </div>
                </div>

                <!-- Category: Avatar API -->
                <div>
                    <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                        <span><i class="fa-solid fa-image mr-1.5 text-pink-400"></i> Avatar Management</span>
                        <span class="text-[10px] text-slate-500">2 APIs</span>
                    </div>
                    <div class="space-y-1 mt-1">
                        <button onclick="loadEndpoint('upload-avatar')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">POST</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/profile/avatar</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('delete-avatar')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">DEL</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/profile/avatar</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                    </div>
                </div>

                <!-- Category: Security & Password -->
                <div>
                    <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                        <span><i class="fa-solid fa-lock mr-1.5 text-emerald-400"></i> Security & Password</span>
                        <span class="text-[10px] text-slate-500">3 APIs</span>
                    </div>
                    <div class="space-y-1 mt-1">
                        <button onclick="loadEndpoint('change-password')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">PUT</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/change-password</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('password-strength')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">POST</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/password-strength</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('change-email')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">PUT</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/change-email</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                    </div>
                </div>

                <!-- Category: Analytics & Completion -->
                <div>
                    <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center justify-between">
                        <span><i class="fa-solid fa-chart-pie mr-1.5 text-violet-400"></i> Analytics & Stats</span>
                        <span class="text-[10px] text-slate-500">3 APIs</span>
                    </div>
                    <div class="space-y-1 mt-1">
                        <button onclick="loadEndpoint('profile-completion')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">GET</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/profile/completion</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('last-login')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">GET</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/profile/last-login</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                        <button onclick="loadEndpoint('statistics')" class="endpoint-btn w-full text-left px-3 py-2 rounded-xl text-xs flex items-center justify-between hover:bg-slate-800/80 transition group border border-transparent hover:border-slate-700">
                            <span class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-sky-500/20 text-sky-400 border border-sky-500/30">GET</span>
                                <span class="font-mono text-slate-300 group-hover:text-white">/api/profile/statistics</span>
                            </span>
                            <i class="fa-solid fa-chevron-right text-[10px] text-slate-600 group-hover:text-indigo-400"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column: Interactive Request Runner & Live JSON Response (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Request Console Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                
                <!-- URL Bar & Send Action -->
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="flex flex-1 items-center bg-slate-950 border border-slate-800 rounded-xl overflow-hidden focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                        <select id="requestMethod" class="bg-slate-900 text-xs font-bold font-mono px-3 py-2.5 text-indigo-400 border-r border-slate-800 focus:outline-none cursor-pointer">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                        <input type="text" id="requestUrl" value="/api/profile"
                               class="w-full bg-transparent px-3 py-2 text-xs font-mono text-slate-200 placeholder:text-slate-600 focus:outline-none">
                    </div>
                    <button id="sendBtn" onclick="executeApiRequest()"
                            class="bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-xs font-bold px-6 py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/30 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Send Request</span>
                    </button>
                </div>

                <!-- Request Options Tabs (Body, Headers, Multipart Avatar) -->
                <div>
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <div class="flex gap-2">
                            <button onclick="switchTab('body')" id="tabBtnBody" class="px-3 py-1 text-xs font-semibold rounded-lg bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">
                                JSON Body
                            </button>
                            <button onclick="switchTab('file')" id="tabBtnFile" class="px-3 py-1 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-200 border border-transparent hover:border-slate-800">
                                Multipart Avatar File
                            </button>
                            <button onclick="switchTab('headers')" id="tabBtnHeaders" class="px-3 py-1 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-200 border border-transparent hover:border-slate-800">
                                Headers (Auto Sanctum)
                            </button>
                        </div>
                        <div class="text-[11px] text-slate-500 flex items-center gap-2">
                            <span id="endpointDescription" class="truncate max-w-[280px]">Get user profile data</span>
                        </div>
                    </div>

                    <!-- Tab: JSON Body -->
                    <div id="tabBody" class="mt-3">
                        <div class="relative">
                            <textarea id="requestBodyJson" rows="6" placeholder="{}"
                                      class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs font-mono text-slate-300 placeholder:text-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
                            <button onclick="formatJsonInput()" title="Format JSON" class="absolute top-2 right-2 px-2 py-1 bg-slate-800 hover:bg-slate-700 text-[10px] text-slate-400 hover:text-white rounded border border-slate-700 transition">
                                Prettify JSON
                            </button>
                        </div>
                    </div>

                    <!-- Tab: Multipart Avatar Upload -->
                    <div id="tabFile" class="mt-3 hidden">
                        <div class="border-2 border-dashed border-slate-800 hover:border-indigo-500/50 rounded-2xl p-6 text-center bg-slate-950/50 transition">
                            <input type="file" id="avatarFileInput" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="handleAvatarFileSelect(this)">
                            <div onclick="document.getElementById('avatarFileInput').click()" class="cursor-pointer space-y-2">
                                <div class="w-12 h-12 mx-auto rounded-full bg-indigo-600/10 text-indigo-400 flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <div class="text-xs font-medium text-slate-300">Click to select an image for <code class="text-indigo-400 font-mono">POST /api/profile/avatar</code></div>
                                <div class="text-[11px] text-slate-500">Supports JPG, PNG, WEBP (Max 2MB)</div>
                            </div>
                            <div id="fileSelectedInfo" class="mt-3 hidden items-center justify-center gap-3 bg-slate-900 border border-slate-800 py-2 px-4 rounded-xl max-w-sm mx-auto">
                                <img id="filePreviewThumb" class="w-8 h-8 rounded-full object-cover border border-slate-700" src="" alt="preview">
                                <div class="text-left text-xs truncate">
                                    <div id="fileNameText" class="font-medium text-slate-200 truncate">image.jpg</div>
                                    <div id="fileSizeText" class="text-[10px] text-slate-400">120 KB</div>
                                </div>
                                <button onclick="clearSelectedFile(event)" class="text-rose-400 hover:text-rose-300 ml-auto text-xs">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Headers -->
                    <div id="tabHeaders" class="mt-3 hidden space-y-2 font-mono text-xs">
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-3 space-y-1 text-slate-400">
                            <div><span class="text-indigo-400">Accept:</span> application/json</div>
                            <div><span class="text-indigo-400">Content-Type:</span> application/json <span class="text-slate-600">(or multipart/form-data for avatar)</span></div>
                            <div id="headerAuthDisplay"><span class="text-indigo-400">Authorization:</span> <span class="text-emerald-400">Bearer &lt;Current-Sanctum-Token&gt;</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Response Console -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
                
                <!-- Response Header / Stats Bar -->
                <div class="px-5 py-3 border-b border-slate-800 bg-slate-900/90 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-terminal text-indigo-400"></i> Response
                        </span>
                        <span id="resStatusBadge" class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700">
                            Ready
                        </span>
                    </div>

                    <div class="flex items-center gap-4 text-xs font-mono text-slate-400">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-slate-500"></i>
                            <span id="resLatency">-- ms</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-database text-slate-500"></i>
                            <span id="resSize">-- B</span>
                        </div>
                        <button onclick="copyResponseJson()" class="hover:text-white transition" title="Copy Response JSON">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- Live User & Avatar Badge Preview Component (Shows automatically when profile is fetched) -->
                <div id="liveUserBadgeCard" class="hidden p-4 border-b border-slate-800 bg-gradient-to-r from-indigo-950/40 via-slate-900 to-purple-950/40">
                    <div class="flex items-center gap-4">
                        <div id="liveAvatarContainer" class="relative">
                            <!-- Dynamic Avatar Image or Initials Badge -->
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 id="liveUserName" class="text-sm font-bold text-white truncate">User Name</h4>
                                <span id="liveUserCompletion" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">100% Complete</span>
                            </div>
                            <p id="liveUserEmail" class="text-xs text-slate-400 truncate">user@example.com</p>
                            <div class="flex flex-wrap gap-2 mt-1 text-[11px] text-slate-400">
                                <span id="liveUserPhone"><i class="fa-solid fa-phone text-[10px] text-indigo-400 mr-1"></i> --</span>
                                <span id="liveUserLocation"><i class="fa-solid fa-location-dot text-[10px] text-pink-400 mr-1"></i> --</span>
                                <span id="liveUserSocials"><i class="fa-brands fa-github text-[10px] text-slate-400 mr-1"></i> --</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- JSON Response Code Viewer -->
                <div class="p-4 bg-slate-950 overflow-x-auto min-h-[260px] max-h-[460px]">
                    <pre><code id="responseBody" class="text-xs text-slate-300 font-mono leading-relaxed block">// Select an endpoint on the left and click "Send Request" to test live APIs.
// Sanctum tokens will automatically be stored and attached to subsequent requests.</code></pre>
                </div>
            </div>

            <!-- Quick API Reference Table -->
            <div id="quick-docs" class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-nodes text-indigo-400"></i> RESTful API Reference Guide
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 font-mono">
                                <th class="py-2 px-3">Method</th>
                                <th class="py-2 px-3">Endpoint</th>
                                <th class="py-2 px-3">Auth</th>
                                <th class="py-2 px-3">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-mono text-slate-300">
                            <tr>
                                <td class="py-2 px-3 text-emerald-400 font-bold">POST</td>
                                <td class="py-2 px-3">/api/register</td>
                                <td class="py-2 px-3 text-slate-500">Public</td>
                                <td class="py-2 px-3 font-sans text-slate-400">Register new user & receive Sanctum token</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 text-emerald-400 font-bold">POST</td>
                                <td class="py-2 px-3">/api/login</td>
                                <td class="py-2 px-3 text-slate-500">Public</td>
                                <td class="py-2 px-3 font-sans text-slate-400">Login with email/password & receive Sanctum token</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 text-sky-400 font-bold">GET</td>
                                <td class="py-2 px-3">/api/profile</td>
                                <td class="py-2 px-3 text-indigo-400">Bearer</td>
                                <td class="py-2 px-3 font-sans text-slate-400">Get profile with avatar_url, initials, bio, phone, etc.</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 text-amber-400 font-bold">PUT</td>
                                <td class="py-2 px-3">/api/profile</td>
                                <td class="py-2 px-3 text-indigo-400">Bearer</td>
                                <td class="py-2 px-3 font-sans text-slate-400">Update extended fields (bio, phone, city, country, socials)</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 text-emerald-400 font-bold">POST</td>
                                <td class="py-2 px-3">/api/profile/avatar</td>
                                <td class="py-2 px-3 text-indigo-400">Bearer</td>
                                <td class="py-2 px-3 font-sans text-slate-400">Upload profile image (multipart/form-data, max 2MB)</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 text-rose-400 font-bold">DELETE</td>
                                <td class="py-2 px-3">/api/profile/avatar</td>
                                <td class="py-2 px-3 text-indigo-400">Bearer</td>
                                <td class="py-2 px-3 font-sans text-slate-400">Delete uploaded avatar and fallback to initials</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 text-sky-400 font-bold">GET</td>
                                <td class="py-2 px-3">/api/profile/completion</td>
                                <td class="py-2 px-3 text-indigo-400">Bearer</td>
                                <td class="py-2 px-3 font-sans text-slate-400">Dynamic completion % and missing profile field checklist</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- Script: Interactive API Playground Logic -->
    <script>
        const API_BASE = window.location.origin;
        let activeToken = localStorage.getItem('sanctum_api_token') || '';
        let currentEndpointKey = 'get-profile';
        let selectedAvatarFile = null;

        // Endpoint Presets & Configurations
        const ENDPOINTS = {
            'register': {
                method: 'POST',
                url: '/api/register',
                description: 'Register a new user account and obtain a Sanctum Bearer token',
                body: {
                    name: 'Alex Johnson',
                    email: 'alex' + Math.floor(Math.random() * 9000 + 1000) + '@example.com',
                    password: 'Password@123',
                    password_confirmation: 'Password@123'
                },
                tab: 'body'
            },
            'login': {
                method: 'POST',
                url: '/api/login',
                description: 'Login with email & password to generate Bearer token',
                body: {
                    email: 'alex@example.com',
                    password: 'Password@123'
                },
                tab: 'body'
            },
            'logout': {
                method: 'POST',
                url: '/api/logout',
                description: 'Revoke current device token and log out',
                body: {},
                tab: 'body'
            },
            'logout-all': {
                method: 'POST',
                url: '/api/logout-all',
                description: 'Revoke all tokens on all devices',
                body: {},
                tab: 'body'
            },
            'sessions': {
                method: 'GET',
                url: '/api/sessions',
                description: 'List all active Sanctum session tokens with IP & last used timestamp',
                body: null,
                tab: 'body'
            },
            'revoke-session': {
                method: 'DELETE',
                url: '/api/sessions/1',
                description: 'Revoke a specific session token by ID',
                body: null,
                tab: 'body'
            },
            'get-profile': {
                method: 'GET',
                url: '/api/profile',
                description: 'Get authenticated user profile, avatar URL, initials, badges and extended info',
                body: null,
                tab: 'body'
            },
            'update-profile': {
                method: 'PUT',
                url: '/api/profile',
                description: 'Update user profile including name, bio, phone, city, country, socials and timezone',
                body: {
                    name: 'Alex Johnson',
                    phone: '+91 9876543210',
                    bio: 'Full Stack Laravel & React Developer passionate about clean API architecture.',
                    city: 'Ahmedabad',
                    country: 'India',
                    website: 'https://example.com',
                    github_profile: 'https://github.com/developer',
                    twitter_profile: 'https://twitter.com/developer',
                    timezone: 'Asia/Kolkata'
                },
                tab: 'body'
            },
            'delete-account': {
                method: 'DELETE',
                url: '/api/profile',
                description: 'Permanently delete user account and all personal access tokens',
                body: {
                    password: 'Password@123'
                },
                tab: 'body'
            },
            'upload-avatar': {
                method: 'POST',
                url: '/api/profile/avatar',
                description: 'Upload custom avatar image via multipart/form-data (JPG, PNG, WEBP max 2MB)',
                body: null,
                tab: 'file'
            },
            'delete-avatar': {
                method: 'DELETE',
                url: '/api/profile/avatar',
                description: 'Remove custom profile picture and revert to dynamic initials badge',
                body: null,
                tab: 'body'
            },
            'change-password': {
                method: 'PUT',
                url: '/api/change-password',
                description: 'Change user password with old password verification',
                body: {
                    current_password: 'Password@123',
                    new_password: 'NewSecurePassword@456',
                    new_password_confirmation: 'NewSecurePassword@456'
                },
                tab: 'body'
            },
            'password-strength': {
                method: 'POST',
                url: '/api/password-strength',
                description: 'Analyze password strength and security score',
                body: {
                    password: 'MySecretPassword@2026'
                },
                tab: 'body'
            },
            'change-email': {
                method: 'PUT',
                url: '/api/change-email',
                description: 'Update user email address with password confirmation',
                body: {
                    email: 'newemail' + Math.floor(Math.random() * 9000 + 1000) + '@example.com',
                    password: 'Password@123'
                },
                tab: 'body'
            },
            'profile-completion': {
                method: 'GET',
                url: '/api/profile/completion',
                description: 'Get profile completion percentage and list of missing fields',
                body: null,
                tab: 'body'
            },
            'last-login': {
                method: 'GET',
                url: '/api/profile/last-login',
                description: 'Get details about last login time, IP address and device',
                body: null,
                tab: 'body'
            },
            'statistics': {
                method: 'GET',
                url: '/api/profile/statistics',
                description: 'Get user account statistics and activity metrics',
                body: null,
                tab: 'body'
            }
        };

        // Initialize Page
        document.addEventListener('DOMContentLoaded', () => {
            updateTokenUI();
            loadEndpoint('get-profile');
        });

        // Update Token State in UI
        function updateTokenUI() {
            const tokenBadge = document.getElementById('tokenBadge');
            const tokenPreview = document.getElementById('tokenPreview');
            const authStatusText = document.getElementById('authStatusText');
            const sanctumTokenInput = document.getElementById('sanctumTokenInput');
            const headerAuthDisplay = document.getElementById('headerAuthDisplay');

            if (activeToken) {
                tokenBadge.classList.remove('hidden');
                tokenPreview.textContent = activeToken.substring(0, 16) + '...';
                authStatusText.textContent = 'Authenticated';
                authStatusText.className = 'text-xs px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                sanctumTokenInput.value = activeToken;
                headerAuthDisplay.innerHTML = `<span class="text-indigo-400">Authorization:</span> <span class="text-emerald-400">Bearer ${activeToken.substring(0, 20)}...</span>`;
            } else {
                tokenBadge.classList.add('hidden');
                authStatusText.textContent = 'Unauthorized';
                authStatusText.className = 'text-xs px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30';
                sanctumTokenInput.value = '';
                headerAuthDisplay.innerHTML = `<span class="text-indigo-400">Authorization:</span> <span class="text-slate-500">None (Set Bearer Token above)</span>`;
            }
        }

        function saveManualToken() {
            const val = document.getElementById('sanctumTokenInput').value.trim();
            if (val) {
                activeToken = val;
                localStorage.setItem('sanctum_api_token', val);
                updateTokenUI();
            }
        }

        function clearToken() {
            activeToken = '';
            localStorage.removeItem('sanctum_api_token');
            updateTokenUI();
        }

        function copyToken() {
            if (activeToken) {
                navigator.clipboard.writeText(activeToken);
                alert('Token copied to clipboard!');
            }
        }

        // Switch Endpoint Preset
        function loadEndpoint(key) {
            currentEndpointKey = key;
            const ep = ENDPOINTS[key];
            if (!ep) return;

            document.getElementById('requestMethod').value = ep.method;
            document.getElementById('requestUrl').value = ep.url;
            document.getElementById('endpointDescription').textContent = ep.description;

            if (ep.body) {
                document.getElementById('requestBodyJson').value = JSON.stringify(ep.body, null, 2);
            } else {
                document.getElementById('requestBodyJson').value = '';
            }

            switchTab(ep.tab || 'body');
        }

        // Tab Switching
        function switchTab(tab) {
            const tabBody = document.getElementById('tabBody');
            const tabFile = document.getElementById('tabFile');
            const tabHeaders = document.getElementById('tabHeaders');
            const tabBtnBody = document.getElementById('tabBtnBody');
            const tabBtnFile = document.getElementById('tabBtnFile');
            const tabBtnHeaders = document.getElementById('tabBtnHeaders');

            tabBody.classList.add('hidden');
            tabFile.classList.add('hidden');
            tabHeaders.classList.add('hidden');

            tabBtnBody.className = 'px-3 py-1 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-200 border border-transparent hover:border-slate-800';
            tabBtnFile.className = 'px-3 py-1 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-200 border border-transparent hover:border-slate-800';
            tabBtnHeaders.className = 'px-3 py-1 text-xs font-semibold rounded-lg text-slate-400 hover:text-slate-200 border border-transparent hover:border-slate-800';

            if (tab === 'body') {
                tabBody.classList.remove('hidden');
                tabBtnBody.className = 'px-3 py-1 text-xs font-semibold rounded-lg bg-indigo-600/20 text-indigo-400 border border-indigo-500/30';
            } else if (tab === 'file') {
                tabFile.classList.remove('hidden');
                tabBtnFile.className = 'px-3 py-1 text-xs font-semibold rounded-lg bg-pink-600/20 text-pink-400 border border-pink-500/30';
            } else if (tab === 'headers') {
                tabHeaders.classList.remove('hidden');
                tabBtnHeaders.className = 'px-3 py-1 text-xs font-semibold rounded-lg bg-indigo-600/20 text-indigo-400 border border-indigo-500/30';
            }
        }

        function formatJsonInput() {
            try {
                const val = document.getElementById('requestBodyJson').value;
                if (val.trim()) {
                    const parsed = JSON.parse(val);
                    document.getElementById('requestBodyJson').value = JSON.stringify(parsed, null, 2);
                }
            } catch (e) {
                alert('Invalid JSON: ' + e.message);
            }
        }

        // Avatar File Handler
        function handleAvatarFileSelect(input) {
            if (input.files && input.files[0]) {
                selectedAvatarFile = input.files[0];
                document.getElementById('fileNameText').textContent = selectedAvatarFile.name;
                document.getElementById('fileSizeText').textContent = (selectedAvatarFile.size / 1024).toFixed(1) + ' KB';

                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('filePreviewThumb').src = e.target.result;
                    document.getElementById('fileSelectedInfo').classList.remove('hidden');
                    document.getElementById('fileSelectedInfo').classList.add('flex');
                };
                reader.readAsDataURL(selectedAvatarFile);
            }
        }

        function clearSelectedFile(e) {
            e.stopPropagation();
            selectedAvatarFile = null;
            document.getElementById('avatarFileInput').value = '';
            document.getElementById('fileSelectedInfo').classList.add('hidden');
            document.getElementById('fileSelectedInfo').classList.remove('flex');
        }

        // Execute API Request
        async function executeApiRequest() {
            const method = document.getElementById('requestMethod').value;
            const url = document.getElementById('requestUrl').value;
            const sendBtn = document.getElementById('sendBtn');
            const resStatusBadge = document.getElementById('resStatusBadge');
            const resLatency = document.getElementById('resLatency');
            const resSize = document.getElementById('resSize');
            const responseBody = document.getElementById('responseBody');

            sendBtn.disabled = true;
            sendBtn.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i> <span>Sending...</span>`;
            resStatusBadge.textContent = 'Fetching...';
            resStatusBadge.className = 'text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-slate-800 text-amber-400 border border-amber-500/30';

            const headers = {
                'Accept': 'application/json'
            };

            if (activeToken) {
                headers['Authorization'] = 'Bearer ' + activeToken;
            }

            let requestOptions = {
                method: method,
                headers: headers
            };

            // Check if uploading avatar multipart file
            if (url.includes('/api/profile/avatar') && method === 'POST' && selectedAvatarFile) {
                const formData = new FormData();
                formData.append('avatar', selectedAvatarFile);
                requestOptions.body = formData;
                // Note: Do NOT set Content-Type header when using FormData so browser sets boundary
            } else if (method !== 'GET' && method !== 'HEAD') {
                headers['Content-Type'] = 'application/json';
                const bodyJson = document.getElementById('requestBodyJson').value.trim();
                if (bodyJson) {
                    try {
                        JSON.parse(bodyJson);
                        requestOptions.body = bodyJson;
                    } catch (e) {
                        alert('Request Body is not valid JSON!');
                        sendBtn.disabled = false;
                        sendBtn.innerHTML = `<i class="fa-solid fa-paper-plane"></i> <span>Send Request</span>`;
                        return;
                    }
                }
            }

            const startTime = performance.now();

            try {
                const response = await fetch(API_BASE + url, requestOptions);
                const latency = Math.round(performance.now() - startTime);
                const responseText = await response.text();
                const contentLength = new Blob([responseText]).size;

                resLatency.textContent = latency + ' ms';
                resSize.textContent = contentLength < 1024 ? contentLength + ' B' : (contentLength / 1024).toFixed(1) + ' KB';

                // Status Badge Color
                if (response.status >= 200 && response.status < 300) {
                    resStatusBadge.textContent = response.status + ' ' + response.statusText;
                    resStatusBadge.className = 'text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                } else if (response.status === 422) {
                    resStatusBadge.textContent = '422 Unprocessable Content';
                    resStatusBadge.className = 'text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30';
                } else if (response.status === 401) {
                    resStatusBadge.textContent = '401 Unauthorized';
                    resStatusBadge.className = 'text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30';
                } else {
                    resStatusBadge.textContent = response.status + ' ' + response.statusText;
                    resStatusBadge.className = 'text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30';
                }

                // Parse and Format JSON
                try {
                    const data = JSON.parse(responseText);
                    responseBody.textContent = JSON.stringify(data, null, 2);

                    // If register or login returned token, auto-save and update UI!
                    if (data.token) {
                        activeToken = data.token;
                        localStorage.setItem('sanctum_api_token', data.token);
                        updateTokenUI();
                    }

                    // If logout or logout-all, clear token
                    if (url.includes('/logout') && response.ok) {
                        clearToken();
                    }

                    // Render Live User Badge if user data is present in response
                    renderLiveUserBadge(data);

                } catch (e) {
                    responseBody.textContent = responseText;
                }

            } catch (err) {
                resStatusBadge.textContent = 'Network Error';
                resStatusBadge.className = 'text-xs font-mono font-bold px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30';
                responseBody.textContent = '// Failed to fetch: ' + err.message;
            } finally {
                sendBtn.disabled = false;
                sendBtn.innerHTML = `<i class="fa-solid fa-paper-plane"></i> <span>Send Request</span>`;
            }
        }

        // Render Live User Badge Preview
        function renderLiveUserBadge(data) {
            const user = data.user || data.data || (data.name && data.email ? data : null);
            const liveUserBadgeCard = document.getElementById('liveUserBadgeCard');
            const liveAvatarContainer = document.getElementById('liveAvatarContainer');
            const liveUserName = document.getElementById('liveUserName');
            const liveUserEmail = document.getElementById('liveUserEmail');
            const liveUserPhone = document.getElementById('liveUserPhone');
            const liveUserLocation = document.getElementById('liveUserLocation');
            const liveUserSocials = document.getElementById('liveUserSocials');
            const liveUserCompletion = document.getElementById('liveUserCompletion');

            if (!user || !user.name) {
                // Keep existing or ignore
                return;
            }

            liveUserBadgeCard.classList.remove('hidden');
            liveUserName.textContent = user.name;
            liveUserEmail.textContent = user.email;

            // Avatar render
            if (user.avatar_url) {
                liveAvatarContainer.innerHTML = `<img src="${user.avatar_url}" class="w-14 h-14 rounded-2xl object-cover border-2 border-indigo-500/50 shadow-md shadow-indigo-500/20" alt="Avatar">`;
            } else {
                const initials = user.initials || user.name.substring(0, 2).toUpperCase();
                const bg = user.avatar_bg_color || '#4f46e5';
                liveAvatarContainer.innerHTML = `<div style="background-color: ${bg}" class="w-14 h-14 rounded-2xl text-white font-bold text-lg flex items-center justify-center border-2 border-white/20 shadow-md shadow-indigo-500/20">${initials}</div>`;
            }

            liveUserPhone.innerHTML = `<i class="fa-solid fa-phone text-[10px] text-indigo-400 mr-1"></i> ${user.phone || 'No phone'}`;
            liveUserLocation.innerHTML = `<i class="fa-solid fa-location-dot text-[10px] text-pink-400 mr-1"></i> ${user.city || ''} ${user.country ? (user.city ? ', ' : '') + user.country : (user.city ? '' : 'No location')}`;
            liveUserSocials.innerHTML = user.github_profile ? `<a href="${user.github_profile}" target="_blank" class="text-indigo-400 hover:underline"><i class="fa-brands fa-github mr-1"></i> GitHub</a>` : `<i class="fa-solid fa-globe text-[10px] text-slate-400 mr-1"></i> ${user.website || 'No website'}`;

            if (data.completion_percentage !== undefined) {
                liveUserCompletion.textContent = data.completion_percentage + '% Complete';
            }
        }

        function copyResponseJson() {
            const code = document.getElementById('responseBody').textContent;
            navigator.clipboard.writeText(code);
            alert('Response JSON copied to clipboard!');
        }
    </script>
</body>
</html>
