@props(['review'])

<div class="flex h-full flex-col gap-4 rounded-2xl border border-brand-ink/10 bg-white/60 p-6 whitespace-normal">
    @if ($review->rating)
        <div class="flex gap-0.5 text-brand-signature">
            @for ($i = 1; $i <= 5; $i++)
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                     fill="{{ $i <= $review->rating ? 'currentColor' : 'none' }}"
                     stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.5a.56.56 0 011.04 0l2.12 5.11a.56.56 0 00.48.35l5.52.44c.5.04.7.66.32.99l-4.2 3.6a.56.56 0 00-.19.56l1.29 5.38a.56.56 0 01-.84.61l-4.73-2.88a.56.56 0 00-.58 0l-4.73 2.88a.56.56 0 01-.84-.61l1.29-5.38a.56.56 0 00-.19-.56l-4.2-3.6a.56.56 0 01.32-.99l5.52-.44a.56.56 0 00.48-.35L11.48 3.5z" />
                </svg>
            @endfor
        </div>
    @endif

    <p class="text-sm leading-relaxed text-brand-ink/80 italic">« {{ $review->comment }} »</p>

    <div class="mt-auto flex items-center gap-3 pt-2">
        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-accent/15 font-display text-brand-signature">
            {{ mb_strtoupper(mb_substr($review->author_name, 0, 1)) }}
        </div>
        <div>
            <p class="text-sm font-medium text-brand-ink">{{ $review->author_name }}</p>
            @if ($review->location)
                <p class="text-xs text-brand-muted">{{ $review->location }}</p>
            @endif
        </div>
    </div>
</div>
