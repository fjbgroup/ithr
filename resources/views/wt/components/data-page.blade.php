@props([
    'title' => 'Inventory List',
    'subtitle' => '',
])

<section {{ $attributes->merge(['class' => 'wt-data-page']) }}>
    <style>
        .wt-data-page {
            display: grid !important;
            gap: 42px !important;
            padding: 0 14px !important;
            color-scheme: dark !important;
        }
        .wt-data-page-hero {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 18px !important;
            min-height: 96px !important;
            padding: 18px 24px !important;
            border-left: 0 !important;
            border-radius: 13px !important;
            background: linear-gradient(90deg, rgba(31, 41, 55, 0.98), rgba(30, 41, 59, 0.98)) !important;
        }
        .wt-data-page-title {
            margin: 0 0 8px !important;
            color: #f8fafc !important;
            font-size: 28px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
        }
        .wt-data-page-subtitle {
            margin: 0 !important;
            color: #aab5c7 !important;
            font-size: 13px !important;
            font-weight: 900 !important;
            letter-spacing: 0.28em !important;
            line-height: 1.25 !important;
            text-transform: uppercase !important;
        }
        .wt-data-page-actions {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }
        .wt-data-page-actions .wt-btn {
            min-width: 164px !important;
            min-height: 52px !important;
            padding: 0 20px !important;
            border-radius: 10px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            font-size: 15px !important;
            font-weight: 900 !important;
            line-height: 1 !important;
            white-space: nowrap !important;
        }
        @media (max-width: 900px) {
            .wt-data-page {
                gap: 24px !important;
                padding: 0 !important;
            }
            .wt-data-page-hero {
                align-items: stretch !important;
                flex-direction: column !important;
            }
            .wt-data-page-actions {
                justify-content: flex-start !important;
            }
        }
    </style>

    <div class="wt-data-page-hero">
        <div>
            <h1 class="wt-data-page-title">{{ $title }}</h1>
            @if($subtitle)
                <p class="wt-data-page-subtitle">{{ $subtitle }}</p>
            @endif
        </div>

        <div class="wt-data-page-actions">
            {{ $actions ?? '' }}
        </div>
    </div>

    {{ $slot }}
</section>
