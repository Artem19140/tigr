<div class="text-center">
    Ведомость результатов экзамена по русскому языку как иностранному, истории России и основам законодательства Российской Федерации, проведенного в {{ $center->shortName() }} 
    ({{ $exam->addressName() }})
</div>

<div class="text-center text-small">
    Экзамен на уровень {{ $exam->level() }} - Сертификат о владении русским языком, знании истории России и основ законодательства Российской Федерации на уровне, 
    соответствующем цели получения {{ $exam->certificateName() }}
</div>

<div class="text-small">
    Сессия № {{ $exam->session }}, группа № {{ $exam->group }}
</div>

<div class="text-small">
    Дата и время: {{ $exam->beginTimeLocal()->format('d.m.Y, H:i') }}
</div>

@php
    $leftAlign = [
        'fullName',
        'fullNameLatin'
    ]
@endphp

<table class="small-table bordered-table">
    <thead>
        <tr>
            @foreach ($headers as $header)
                <th
                    colspan="{{ $header['colspan'] ?? 1 }}"
                    rowspan="{{ $header['rowspan'] ?? 1 }}"
                    class="text-xs"
                >
                    {{ $header['label'] }}
                </th>
            @endforeach
        </tr>

        <tr>
            @foreach ($headers as $header)
                @foreach ($header['childrens'] ?? [] as $child)
                    <th class="text-xs">{{ $child['label'] }}</th>
                @endforeach
            @endforeach
        </tr>


    </thead>

    <tbody>
        @foreach ($rows as $row)
            <tr>
                @foreach ($row as $key => $data)
                    <td 
                        style="
                            {{  in_array( $key, $leftAlign ) 
                                ? 'text-align: left;' 
                                : '' 
                            }}
                        "
                        class="text-xs"
                    >
                        {{ $data['value'] }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<div class="text-small mt-10 mb-10">
    Результаты экзамена проверены.
</div>
<div class="text-small">
    Ответственные по проведению экзамена (тесторы):
</div>

@foreach($exam->examiners as $examiner)
    @include('pdf.components.signature-section', [
        'date' =>  $exam?->beginTimeLocal()->format('d.m.Y'), 
        'fio' => $examiner->full_name, 
    ])
@endforeach  



@if ($exam->hasSpeaking())
    <div class="break-avoid">
        <div class="text-small mt-10">
            Председатель комиссии:
        </div>

        @include('pdf.components.signature-section', [
            'date' =>  $exam?->beginTimeLocal()->format('d.m.Y'), 
            'fio' => $center->commissionChairman(), 
        ])
    </div>
@endif
