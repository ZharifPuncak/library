<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>@yield('title')</title>
<style>
    @page { margin: 28mm 22mm 28mm 22mm; }
    body  { font-family: DejaVu Sans, sans-serif; font-size: 11pt; color: #1f2937; line-height: 1.45; }

    .cover         { text-align: center; padding: 60mm 0 0; }
    .cover .crest  { font-size: 11pt; letter-spacing: 6pt; color: #64748b; text-transform: uppercase; margin-bottom: 30mm; }
    .cover h1     { font-size: 36pt; color: #003865; margin: 0 0 12mm; }
    .cover .sub   { font-size: 14pt; color: #475569; margin-bottom: 30mm; }
    .cover .meta  { font-size: 10pt; color: #94a3b8; }

    h1, h2, h3, h4 { color: #003865; }
    h1 { font-size: 22pt; margin: 8mm 0 4mm; border-bottom: 2px solid #003865; padding-bottom: 2mm; }
    h2 { font-size: 16pt; margin: 8mm 0 3mm; }
    h3 { font-size: 13pt; margin: 6mm 0 2mm; color: #1d6a99; }
    h4 { font-size: 11pt; margin: 4mm 0 2mm; color: #1d6a99; }

    p     { margin: 0 0 3mm; }
    ul,ol { margin: 0 0 3mm 5mm; padding: 0; }
    li    { margin: 0 0 1.5mm; }

    code, .code { font-family: DejaVu Sans Mono, monospace; font-size: 9.5pt; background: #f1f5f9; padding: 0.4mm 1.5mm; border-radius: 1mm; color: #003865; }
    pre   { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 2mm; padding: 3mm; font-family: DejaVu Sans Mono, monospace; font-size: 9pt; color: #0f172a; white-space: pre-wrap; word-wrap: break-word; }

    table { width: 100%; border-collapse: collapse; margin: 3mm 0 5mm; font-size: 9.5pt; }
    th, td { text-align: left; padding: 2mm 2.5mm; border: 1px solid #e2e8f0; vertical-align: top; }
    th { background: #f1f5f9; color: #003865; font-weight: 700; }

    .callout { border-left: 3px solid #0ea5e9; background: #f0f9ff; padding: 3mm 4mm; margin: 3mm 0; border-radius: 0 2mm 2mm 0; font-size: 10pt; }
    .callout.warn { border-color: #f59e0b; background: #fffbeb; }
    .callout.danger { border-color: #ef4444; background: #fef2f2; }

    .toc        { font-size: 11pt; }
    .toc h2     { border-bottom: 1px solid #cbd5e1; padding-bottom: 2mm; }
    .toc-row    { display: block; margin: 1mm 0; }
    .toc-row .num { color: #1d6a99; font-weight: 700; min-width: 12mm; display: inline-block; }
    .toc-row .ttl { color: #1f2937; }

    .pill { display: inline-block; padding: 0.5mm 2mm; border-radius: 6mm; font-size: 8.5pt; font-weight: 700; }
    .pill-navy   { background: #003865; color: #fff; }
    .pill-sky    { background: #0ea5e9; color: #fff; }
    .pill-amber  { background: #f59e0b; color: #fff; }
    .pill-slate  { background: #e2e8f0; color: #334155; }
    .pill-green  { background: #10b981; color: #fff; }

    .page-break { page-break-before: always; }

    .small { font-size: 9pt; color: #64748b; }
    .footer-note { border-top: 1px solid #e2e8f0; margin-top: 8mm; padding-top: 3mm; font-size: 9pt; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
@yield('content')
</body>
</html>
