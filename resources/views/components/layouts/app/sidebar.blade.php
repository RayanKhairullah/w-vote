<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('results') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                    <flux:navlist.item icon="chart-bar" :href="route('results')" :current="request()->routeIs('results')" wire:navigate>{{ __('Hasil Pemilihan') }}</flux:navlist.item>

                <flux:navlist.group :heading="__('Manajemen Data')" class="grid">
                    <flux:navlist.item icon="calendar-days" :href="route('admin.elections')" :current="request()->routeIs('admin.elections')" wire:navigate>{{ __(' Pemilihan') }}</flux:navlist.item>
                    <flux:navlist.item icon="users" :href="route('admin.candidates')" :current="request()->routeIs('admin.candidates')" wire:navigate>{{ __('Kandidat') }}</flux:navlist.item>
                    <flux:navlist.item icon="user-group" :href="route('admin.voters.import')" :current="request()->routeIs('admin.voters.import')" wire:navigate>{{ __('Pemilih') }}</flux:navlist.item>
                </flux:navlist.group>
                
                
            </flux:navlist>

            <flux:spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <!-- Global Toasts -->
        <div x-data="{
                toasts: [],
                add(toast) {
                    const id = Date.now() + Math.random();
                    const item = { id, title: toast.title ?? null, message: toast.message ?? '', type: toast.type ?? 'default', timeout: toast.timeout ?? 3000, tid: null };
                    this.toasts.push(item);
                    item.tid = setTimeout(() => { this.remove(id); }, item.timeout);
                },
                remove(id) {
                    const t = this.toasts.find(tt => tt.id === id);
                    if (t && t.tid) clearTimeout(t.tid);
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }
            }"
            x-on:toast.window="add($event.detail)"
            class="pointer-events-none fixed top-4 right-4 z-[80] space-y-2"
            aria-live="polite" aria-atomic="true"
        >
            <template x-for="t in toasts" :key="t.id">
                <div
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="translate-y-2 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="pointer-events-auto min-w-[260px] max-w-sm rounded-md border p-3 pr-2 shadow-xl ring-1 ring-black/5 flex items-start gap-3 backdrop-blur-sm"
                    :class="{
                        'bg-emerald-50 border-emerald-200 text-emerald-900 dark:bg-emerald-900/30 dark:border-emerald-700 dark:text-emerald-100': t.type === 'success',
                        'bg-red-50 border-red-200 text-red-900 dark:bg-red-900/30 dark:border-red-700 dark:text-red-100': t.type === 'error',
                        'bg-amber-50 border-amber-200 text-amber-900 dark:bg-amber-900/30 dark:border-amber-700 dark:text-amber-100': t.type === 'warning',
                        'bg-white border-gray-200 text-gray-800 dark:bg-zinc-800/95 dark:border-zinc-700 dark:text-zinc-100': !['success','error','warning'].includes(t.type),
                    }"
                    role="status"
                    @mouseenter="if (t.tid) { clearTimeout(t.tid); t.tid = null }"
                    @mouseleave="if (!t.tid) { t.tid = setTimeout(() => remove(t.id), t.timeout / 2) }"
                >
                    <div class="shrink-0 mt-0.5">
                        <div class="h-6 w-6 rounded-full flex items-center justify-center"
                             :class="{
                               'bg-emerald-100 text-emerald-700 dark:bg-emerald-800 dark:text-emerald-200': t.type === 'success',
                               'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200': t.type === 'error',
                               'bg-amber-100 text-amber-700 dark:bg-amber-800 dark:text-amber-200': t.type === 'warning',
                               'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-zinc-200': !['success','error','warning'].includes(t.type),
                             }">
                            <svg x-show="t.type === 'success'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <svg x-show="t.type === 'error'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <svg x-show="t.type === 'warning'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14l-7-14-7 14z"/></svg>
                            <svg x-show="!['success','error','warning'].includes(t.type)" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p x-show="t.title" class="text-sm font-semibold leading-5" x-text="t.title"></p>
                        <p class="text-sm leading-5" x-text="t.message"></p>
                    </div>
                    <button class="shrink-0 opacity-70 hover:opacity-100 p-1 rounded-md hover:bg-black/5 dark:hover:bg-white/5" @click="remove(t.id)" aria-label="Close">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>

        @fluxScripts
    </body>
</html>
