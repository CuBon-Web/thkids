@php
    $siteName = $setting->webname ?? ($setting->company ?? config('app.name'));
    $orgName = $setting->company ?? $siteName;
    $orgUrl = url('/');
    $logoUrl = !empty($setting->logo) ? url($setting->logo) : null;
    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    $graph = [
        [
            '@type' => 'WebSite',
            '@id' => $orgUrl . '#website',
            'url' => $orgUrl,
            'name' => $siteName,
            'inLanguage' => 'vi-VN',
        ],
        [
            '@type' => 'Organization',
            '@id' => $orgUrl . '#organization',
            'name' => $orgName,
            'url' => $orgUrl,
        ],
    ];

    if ($logoUrl) {
        $graph[1]['logo'] = [
            '@type' => 'ImageObject',
            'url' => $logoUrl,
        ];
    }

    if (!empty($setting->phone1) || !empty($setting->address1)) {
        $localBusiness = [
            '@type' => 'Store',
            '@id' => $orgUrl . '#localbusiness',
            'name' => $orgName,
            'url' => $orgUrl,
            'image' => $logoUrl,
            'telephone' => $setting->phone1 ?? null,
            'address' => !empty($setting->address1) ? [
                '@type' => 'PostalAddress',
                'streetAddress' => $setting->address1,
                'addressCountry' => 'VN',
            ] : null,
            'sameAs' => array_values(array_filter([
                $setting->facebook ?? null,
                $setting->instagram ?? ($setting->fbPixel ?? null),
            ])),
        ];
        $localBusiness = array_filter($localBusiness);
        $graph[] = $localBusiness;
    }
@endphp
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@graph' => $graph], $jsonFlags) !!}
</script>
