<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Helvetica, Arial, sans-serif;
            color: #1e293b;
        }

        .frame {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            padding: 26px;
            border: 10px solid #0f766e;
        }

        .frame-inner {
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            padding: 34px 50px;
            border: 1px solid #99f6e4;
            text-align: center;
        }

        .eyebrow {
            font-size: 13px;
            letter-spacing: 3px;
            color: #0f766e;
            font-weight: bold;
            margin-top: 10px;
        }

        .title {
            font-size: 30px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 6px;
        }

        .lead {
            font-size: 12px;
            color: #475569;
            margin-top: 26px;
        }

        .participant-name {
            font-size: 26px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #cbd5e1;
            display: inline-block;
            min-width: 420px;
        }

        .lead2 {
            font-size: 12px;
            color: #475569;
            margin-top: 22px;
        }

        .event-name {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }

        .event-dates {
            font-size: 12px;
            color: #475569;
            margin-top: 4px;
        }

        .footer-table {
            width: 100%;
            margin-top: 46px;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: bottom;
        }

        .cert-number {
            text-align: left;
            font-size: 11px;
            color: #475569;
        }

        .cert-number strong {
            display: block;
            font-size: 13px;
            color: #0f172a;
            font-family: 'Courier New', Courier, monospace;
        }

        .qr-cell {
            text-align: right;
        }

        .qr-caption {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="frame">
        <div class="frame-inner">
            <div class="eyebrow">SERTIFIKAT</div>
            <div class="title">Sertifikat Partisipasi</div>

            <div class="lead">Diberikan kepada</div>
            <div class="participant-name">{{ $participantName }}</div>

            <div class="lead2">atas partisipasinya dalam acara</div>
            <div class="event-name">{{ $eventName }}</div>
            <div class="event-dates">
                {{ $eventStartsAt->translatedFormat('d F Y') }}
                @if ($eventStartsAt->isSameDay($eventEndsAt))
                    &middot; {{ $eventStartsAt->translatedFormat('H:i') }}&ndash;{{ $eventEndsAt->translatedFormat('H:i') }}
                @else
                    &ndash; {{ $eventEndsAt->translatedFormat('d F Y') }}
                @endif
            </div>

            <table class="footer-table">
                <tr>
                    <td class="cert-number">
                        Nomor sertifikat
                        <strong>{{ $certificateNumber }}</strong>
                    </td>
                    <td class="qr-cell">
                        <div style="width:80px; height:80px; display:inline-block;">{!! $qrSvg !!}</div>
                        <div class="qr-caption">Pindai untuk verifikasi</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
