@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-2">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-700 text-gray-400 dark:text-zinc-500 cursor-not-allowed"
                  aria-disabled="true" aria-label="Previous">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M11.78 4.22a.75.75 0 0 1 0 1.06L6.06 11l5.72 5.72a.75.75 0 1 1-1.06 1.06l-6.25-6.25a.75.75 0 0 1 0-1.06l6.25-6.25a.75.75 0 0 1 1.06 0zm6 0a.75.75 0 0 1 0 1.06L12.06 11l5.72 5.72a.75.75 0 1 1-1.06 1.06l-6.25-6.25a.75.75 0 0 1 0-1.06l6.25-6.25a.75.75 0 0 1 1.06 0z" />
                </svg>
            </span>
        @else
            <button type="button" wire:click="previousPage" rel="prev"
               class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-white/5 transition"
               aria-label="Previous">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M11.78 4.22a.75.75 0 0 1 0 1.06L6.06 11l5.72 5.72a.75.75 0 1 1-1.06 1.06l-6.25-6.25a.75.75 0 0 1 0-1.06l6.25-6.25a.75.75 0 0 1 1.06 0zm6 0a.75.75 0 0 1 0 1.06L12.06 11l5.72 5.72a.75.75 0 1 1-1.06 1.06l-6.25-6.25a.75.75 0 0 1 0-1.06l6.25-6.25a.75.75 0 0 1 1.06 0z" />
                </svg>
            </button>
        @endif

        @if ($paginator->hasMorePages())
            <button type="button" wire:click="nextPage" rel="next"
               class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-white/5 transition"
               aria-label="Next">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12.22 19.78a.75.75 0 0 1 0-1.06L17.94 12l-5.72-5.72a.75.75 0 1 1 1.06-1.06l6.25 6.25a.75.75 0 0 1 0 1.06l-6.25 6.25a.75.75 0 0 1-1.06 0zm-6 0a.75.75 0 0 1 0-1.06L11.94 12 6.22 6.28a.75.75 0 1 1 1.06-1.06l6.25 6.25a.75.75 0 0 1 0 1.06l-6.25 6.25a.75.75 0 0 1-1.06 0z" />
                </svg>
            </button>
        @else
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-700 text-gray-400 dark:text-zinc-500 cursor-not-allowed"
                  aria-disabled="true" aria-label="Next">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12.22 19.78a.75.75 0 0 1 0-1.06L17.94 12l-5.72-5.72a.75.75 0 1 1 1.06-1.06l6.25 6.25a.75.75 0 0 1 0 1.06l-6.25 6.25a.75.75 0 0 1-1.06 0zm-6 0a.75.75 0 0 1 0-1.06L11.94 12 6.22 6.28a.75.75 0 1 1 1.06-1.06l6.25 6.25a.75.75 0 0 1 0 1.06l-6.25 6.25a.75.75 0 0 1-1.06 0z" />
                </svg>
            </span>
        @endif

    </nav>
@endif