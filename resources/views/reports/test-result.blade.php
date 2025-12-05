<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ __('common.test_result') }}{{ isset($testResults) && $testResults->count() > 1 ? ' - ' . __('common.group') : ' - ' . $testResult->result_id }}</title>
    <style>
        @charset "UTF-8";
        * {
            font-family: DejaVu Sans, Arial, sans-serif;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #2563eb;
            padding-top: 5px;
            padding-bottom: 10px;
            margin-bottom: 15px;
            position: relative;
        }
        .header-top {
            width: 100%;
            margin-bottom: 10px;
        }
        .header-date {
            text-align: right;
            font-size: 9px;
            font-weight: normal;
            color: #666;
            padding: 0;
            margin: 0;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo-cell {
            width: 100px;
            vertical-align: top;
            padding-right: 20px;
            padding-top: 0;
            padding-left: 0;
            padding-bottom: 0;
        }
        .header-logo {
            width: 80px;
            height: 80px;
            background-color: #2563eb;
            border: 3px solid #1e40af;
            text-align: center;
            margin: 0 auto;
            position: relative;
        }
        .header-logo-text {
            font-size: 36px;
            font-weight: bold;
            color: #ffffff;
            line-height: 74px;
            margin: 0;
            padding: 0;
        }
        .header-content-cell {
            vertical-align: middle;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 17px;
            font-weight: bold;
        }
        .header-info {
            margin-top: 5px;
            font-size: 11px;
            color: #666;
            line-height: 1.4;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 10px;
            border-left: 4px solid #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        table th {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .signature-area {
            margin-top: 60px;
            text-align: right;
            font-size: 11px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 40px 0 5px auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-logo-cell" style="width: 100px; vertical-align: middle; padding-right: 20px; padding-top: 5px;">
                    @if(isset($logoBase64) && $logoBase64)
                        <img src="{{ $logoBase64 }}" alt="YAMAN LAB Logo" style="width: 100px; height: auto; max-height: 100px; display: block; margin: 0;" />
                    @else
                        <table style="width: 80px; height: 80px; background-color: #2563eb; border: 3px solid #1e40af; margin: 0;">
                            <tr>
                                <td style="text-align: center; vertical-align: middle; padding: 0;">
                                    <span style="font-size: 36px; font-weight: bold; color: #ffffff; line-height: 1;">YL</span>
                                </td>
                            </tr>
                        </table>
                    @endif
                </td>
                <td class="header-content-cell" style="vertical-align: middle;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="vertical-align: middle;">
                                <h1>YAMAN LAB</h1>
                            </td>
                            <td style="text-align: right; vertical-align: top; padding-left: 15px;">
                                <div class="header-date">
                                    @php
                                        $athensTime = now()->setTimezone('Europe/Athens');
                                    @endphp
                                    {{ __('common.date') }}: {{ $athensTime->format('d.m.Y') }}<br>
                                    {{ __('common.time') }}: {{ $athensTime->format('H:i') }}
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class="header-info">
                        Tel: +90 392 8612643<br>
                        Mobil: +90 533 8612643<br>
                        Adres: Ecevit Caddesi No:2 Terminal Karşısı
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @php
        $testResults = $testResults ?? collect([$testResult]);
        $firstResult = $testResults->first();
        
        // Set locale for date formatting
        $locale = app()->getLocale();
        $months = [
            'tr' => ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            'en' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        ];
        $monthNames = $months[$locale] ?? $months['en'];
    @endphp

    <div class="section">
        <div class="section-title">{{ __('common.patient_information') }}</div>
        <table>
            <tr>
                <th width="30%">{{ __('common.patient_id') }}</th>
                <td>{{ $firstResult->patient->patient_id }}</td>
            </tr>
            <tr>
                <th>{{ __('common.name') }}</th>
                <td>{{ $firstResult->patient->name }}</td>
            </tr>
            <tr>
                <th>{{ __('common.age') }}</th>
                <td>{{ $firstResult->patient->age ?? __('common.n_a') }}</td>
            </tr>
            <tr>
                <th>{{ __('common.gender') }}</th>
                <td>
                    @if($firstResult->patient->gender === 'male')
                        {{ __('common.male') }}
                    @elseif($firstResult->patient->gender === 'female')
                        {{ __('common.female') }}
                    @else
                        {{ __('common.n_a') }}
                    @endif
                </td>
            </tr>
            @if($firstResult->sample_collection_date)
            <tr>
                <th>{{ __('common.sample_collection_date') }}</th>
                <td>{{ $firstResult->sample_collection_date->format('d') }} {{ $monthNames[$firstResult->sample_collection_date->format('n') - 1] }} {{ $firstResult->sample_collection_date->format('Y') }}</td>
            </tr>
            @endif
        </table>
    </div>

    @foreach($testResults as $testResultItem)
    <div class="section" style="page-break-inside: avoid;">
        <div class="section-title" style="background-color: #e0e7ff; border-left-color: #4f46e5;">
            {{ $testResultItem->test->category->name }} - {{ $testResultItem->test->name }}
            <span style="font-size: 10px; font-weight: normal; color: #666; margin-left: 10px;">
                ({{ __('common.result_id') }}: {{ $testResultItem->result_id }})
            </span>
        </div>
        
        @php
            // Sadece değeri girilmiş olan sonuçları PDF'te göster
            $displayValues = $testResultItem->values->filter(function ($v) {
                return $v->value !== null && $v->value !== '';
            });
        @endphp

        @if($displayValues->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>{{ __('common.parameter_name') }}</th>
                    <th>{{ __('common.value') }}</th>
                    <th>{{ __('common.unit') }}</th>
                    <th>{{ __('common.reference_range') }}</th>
                    <th>{{ __('common.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($displayValues as $value)
                @php
                    $parameter = $value->parameter;
                    $range = $parameter->getNormalRange($firstResult->patient->gender);
                @endphp
                <tr>
                    <td>{{ $parameter->name }}</td>
                    <td><strong>{{ $value->value ?? __('common.n_a') }}</strong></td>
                    <td>{{ $parameter->unit ?? __('common.n_a') }}</td>
                    <td>
                        @if($parameter->reference_ranges && count($parameter->reference_ranges) > 0)
                            <div style="font-size: 10px; line-height: 1.4;">
                                @foreach($parameter->reference_ranges as $refRange)
                                    <div style="margin-bottom: 2px;">
                                        <strong>{{ $refRange['label'] ?? '' }}:</strong>
                                        @php
                                            $refValue = $refRange['value'] ?? null;
                                            $min = $refRange['min'] ?? null;
                                            $max = $refRange['max'] ?? null;
                                            $prefix = $refRange['prefix'] ?? '';
                                            
                                            $hasMinMax = ($min !== null && $min !== '') || ($max !== null && $max !== '') || ($min === 0 || $min === '0') || ($max === 0 || $max === '0');
                                            $hasValue = $refValue !== null && $refValue !== '' && strtolower(trim($refValue)) !== strtolower(trim($parameter->unit ?? ''));
                                            
                                            $displayValue = '';
                                            
                                            if ($hasMinMax) {
                                                // Format min/max values - handle 0 as valid value
                                                $minStr = '';
                                                if ($min !== null && $min !== '') {
                                                    $minStr = number_format((float)$min, 2, '.', '');
                                                    // Remove trailing zeros but keep 0
                                                    $minStr = rtrim(rtrim($minStr, '0'), '.');
                                                    if ($minStr === '') $minStr = '0';
                                                } elseif ($min === 0 || $min === '0') {
                                                    $minStr = '0';
                                                }
                                                
                                                $maxStr = '';
                                                if ($max !== null && $max !== '') {
                                                    $maxStr = number_format((float)$max, 2, '.', '');
                                                    // Remove trailing zeros but keep 0
                                                    $maxStr = rtrim(rtrim($maxStr, '0'), '.');
                                                    if ($maxStr === '') $maxStr = '0';
                                                } elseif ($max === 0 || $max === '0') {
                                                    $maxStr = '0';
                                                }
                                                
                                                if ($minStr !== '' && $maxStr !== '') {
                                                    $displayValue = $prefix . $minStr . '-' . $maxStr;
                                                } elseif ($minStr !== '') {
                                                    $displayValue = $prefix . $minStr;
                                                } elseif ($maxStr !== '') {
                                                    $displayValue = $prefix . $maxStr;
                                                }
                                                
                                                // Add unit if we have a numeric range
                                                if ($displayValue && $parameter->unit) {
                                                    $displayValue .= ' ' . $parameter->unit;
                                                }
                                                
                                                // Add value if it exists
                                                if ($hasValue) {
                                                    $displayValue .= ($displayValue ? ' (' . $refValue . ')' : $refValue);
                                                }
                                            } 
                                            // If no min/max but we have a value (and it's not just unit info)
                                            elseif ($hasValue) {
                                                $displayValue = $refValue;
                                            }
                                        @endphp
                                        @if($displayValue)
                                            {{ $displayValue }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @elseif($range['min'] !== null || $range['max'] !== null)
                            {{ $range['min'] ?? 'N/A' }} - {{ $range['max'] ?? 'N/A' }}
                            @if($parameter->unit)
                                {{ $parameter->unit }}
                            @endif
                        @else
                            {{ __('common.n_a') }}
                        @endif
                    </td>
                    <td>
                        @if($value->is_outside_normal_range)
                        <span style="color: #dc2626; font-weight: bold; font-size: 16px;">*</span>
                        @else
                        <span style="color: #16a34a; font-weight: bold;">{{ __('common.normal') }}</span>
                        @endif
                    </td>
                </tr>
                @if($value->notes)
                <tr>
                    <td colspan="5" style="font-size: 10px; font-style: italic; color: #666;">
                        {{ __('common.note') }}: {{ $value->notes }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color: #666; font-style: italic;">{{ __('common.no_test_values') }}</p>
        @endif

        @if($testResultItem->notes || $testResultItem->doctor_remarks || $testResultItem->technician_notes)
        <div style="margin-top: 15px;">
            @if($testResultItem->notes)
            <p style="font-size: 11px;"><strong>{{ __('common.notes') }}:</strong> {{ $testResultItem->notes }}</p>
            @endif
            @if($testResultItem->doctor_remarks)
            <p style="font-size: 11px;"><strong>{{ __('common.doctor_remarks') }}:</strong> {{ $testResultItem->doctor_remarks }}</p>
            @endif
            @if($testResultItem->technician_notes)
            <p style="font-size: 11px;"><strong>{{ __('common.technician_notes') }}:</strong> {{ $testResultItem->technician_notes }}</p>
            @endif
        </div>
        @endif
    </div>
    @endforeach

    <div class="signature-area">
        <div class="signature-line"></div>
        <p style="margin: 0; font-weight: bold;">{{ __('common.medical_chemist') }}</p>
    </div>
</body>
</html>

