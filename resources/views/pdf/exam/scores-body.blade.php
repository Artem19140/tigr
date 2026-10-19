<div class="text-center">
   Баллы экзамена по русскому языку как иностранному, истории России и основам законодательства Российской Федерации, проведенного в {{ $center->shortName() }}
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

<table class="table bordered-table text-center">
    <thead>
        <tr>
            @foreach ($headers as $header)
                <th class="text-xs">
                    {{ $header }}
                </th>
            @endforeach
        </tr>
    </thead>

    <tbody>

        @foreach ($rows as $row)
        <tr>
            @foreach ($row as $data)
                <td class="text-xs">{{ $data }}</td>
            @endforeach
        </tr>
        @endforeach

    </tbody>
</table>

<div class="text-small mt-10 mb-10">Результаты экзамена проверены.</div>
<div class="text-small mt-10">Ответственные по проведению экзамена (тесторы):</div>

@foreach($exam->examiners as $examiner)
    {{-- <x-pdf.components.signature-section
        :date="$exam->beginTimeLocal()->format('d.m.Y')"
        :fio="$examiner->full_name"
    /> --}}

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

        {{-- <x-pdf.components.signature-section
            :date="$exam->beginTimeLocal()->format('d.m.Y')"
            :fio="$center->commissionChairman()"
        /> --}}

        @include('pdf.components.signature-section', [
            'date' =>  $exam?->beginTimeLocal()->format('d.m.Y'), 
            'fio' => $center->commissionChairman(), 
        ])
    </div>
@endif