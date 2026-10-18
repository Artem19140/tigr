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

<table class="table border text-center">
    <thead>
        <tr>
            @foreach ($markTable['headers'] as $header)
                <th>
                    {{ $header }}
                </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
    
    @foreach ($markTable['rows'] as $row)
    <tr>
        <td >{{ $row['fullName'] }}</td>
        <td>{{ $row['fullPassport'] }}</td>
        <td>{{ $row['citizenship'] }}</td>

        @if ($row['answers'])
            @foreach ( $row['answers'] as $answer)
                <td class="border">{{ $answer->mark ?? 0}}</td>
            @endforeach                
        @endif
        @if ($row['answers'] === null)
            @for($i = 1; $i <= $exam->tasksCount(); $i++)
                <td >
                </td>
            @endfor
       @endif
    </tr>
    @endforeach

    </tbody>
</table>
<div class="text-small mt-10 mb-10">Результаты экзамена проверены.</div>
<div class="text-small mt-10">Ответственные по проведению экзамена (тесторы):</div>
@foreach($exam->examiners as $examiner)
    @include('pdf.components.signature-section', [
        'date' =>  $exam->beginTimeLocal()->format('d.m.Y'), 
        'fio' => $examiner->full_name, 
    ])
@endforeach

@if ($exam->hasSpeaking())
    <div class="break-avoid">
        <div class="text-small mt-10">
            Председатель комиссии:
        </div>
        @include('pdf.components.signature-section', [
            'date' => $exam->beginTimeLocal()->format('d.m.Y'), 
            'fio' => $center->commissionChairman(), 
        ])
    </div>
@endif