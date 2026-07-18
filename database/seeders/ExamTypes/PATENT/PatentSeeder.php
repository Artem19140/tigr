<?php

namespace Database\Seeders\ExamTypes\PATENT;

use App\Enums\TaskType;
use App\Models\ExamType;
use Database\Seeders\ExamTypes\ExamTypeSeeder;
use Illuminate\Database\Seeder;

class PatentSeeder extends Seeder
{
    public function __construct(
        protected ExamTypeSeeder $seeder
    ){}
    private string $path = 'resources/data/PATENT/tasks/variants/';
    public function run(): void
    {
        $examType = ExamType::firstOrCreate(
            ['level' => 1],
            [
                'name' => 'Разрешение на работу (патент)',
                'need_human_check' => false,
                'tasks_count' => 22,
                'short_name' => 'ПАТЕНТ',
                'amount' => 3800,
                'amount_in_words' => 'три тысячи восемьсот рублей ноль копеек',
                'duration' => 80,
                'min_mark' => 11,
                'has_speaking_tasks' => false,
                'certificate_name' => 'разрешения на работу или патента',
            ]);

        $this->seeder->run(
            $examType,
            $this->examBlocks()
        );

    }

    public function examBlocks()
    {
        return [
            $this->russianBlock(),
            $this->historyBlock(),
            $this->legislationBlock(),
        ];
    }

    private function russianBlock(): array
    {
        return [
            'name' => 'РУССКИЙ ЯЗЫК КАК ИНОСТРАННЫЙ',
            'min_mark' => 6,
            'subblocks' => [
                $this->audioSubblock(),
                $this->readingSubblock(),
                $this->letterSubblock(),
                $this->vocabularAndGrammarSubblock(),
            ],
        ];
    }

    private function audioSubblock(): array
    {

        return [
            'name' => 'Аудирование',
            'min_mark' => 2,
            'tasks' => [
                [
                    'type' => TaskType::SingleChoice,
                    'mark' => 1,
                    'description' => 'Прослушайте аудиозапись и ответьте на задания номер 1-2',
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task1.json')), true),
                ],
                [
                    'type' => TaskType::SingleChoice,
                    'mark' => 1,
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task2.json')), true),
                ],
                [
                    'type' => TaskType::SingleChoice,
                    'mark' => 1,
                    'description' => 'Прослушайте аудиозапись и ответьте на задания номер 3-4',
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task3.json')), true),
                ],
                [
                    'type' => TaskType::SingleChoice,
                    'mark' => 1,
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task4.json')), true),
                ],

            ],
        ];
    }

    private function readingSubblock(): array
    {
        return [
            'name' => 'Чтение',
            'min_mark' => 0,
            'tasks' => [
                [
                    'type' => TaskType::SingleChoice,
                    'description' => 'Прочитайте объявление и выберите правильный ответ.',
                    'mark' => 1,
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task5.json')), true),
                ],
                [
                    'type' => TaskType::SingleChoice,
                    'description' => 'Прочитайте текст и выберите правильный ответ.',
                    'mark' => 1,
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task6.json')), true),
                ],
            ],
        ];
    }

    private function letterSubblock(): array
    {
        return [
            'name' => 'Письмо',
            'min_mark' => 0,
            'tasks' => [
                [
                    'type' => TaskType::SingleInput,
                    'description' => 'Прочитайте текст и вставьте пропущенное слово.',
                    'mark' => 1,
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task7.json')), true),
                    'checking_mode' => 'auto'
                ],
            ],
        ];
    }

    private function vocabularAndGrammarSubblock(): array
    {
        return [
            'name' => 'Лексика и грамматика',
            'min_mark' => 0,
            'tasks' => [
                [
                    'type' => TaskType::SingleChoice,
                    'mark' => 1,
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task8.json')), true),
                ],
                [
                    'type' => TaskType::SingleChoice,
                    'mark' => 1,
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task9.json')), true),
                ],
                [
                    'type' => TaskType::SingleChoice,
                    'mark' => 1,
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task10.json')), true),
                ],
                [
                    'type' => TaskType::SingleChoice,
                    'mark' => 1,
                    'variants' => json_decode(file_get_contents(base_path($this->path.'task11.json')), true),
                ],
            ],
        ];
    }

    private function historyBlock(): array
    {
        return [
            'name' => 'ИСТОРИЯ РОССИИ',
            'min_mark' => 2,
            'subblocks' => [
                [
                    'name' => '',
                    'min_mark' => 2,
                    'tasks' => [
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task12.json')), true),
                        ],
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task13.json')), true),
                        ],
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task14.json')), true),
                        ],
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task15.json')), true),
                        ],
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task16.json')), true),
                        ],
                    ],
                ],
            ],
        ];
    }

    private function legislationBlock()
    {
        return [
            'name' => 'ОСНОВЫ ЗАКОНОДАТЕЛЬСТВА РОССИЙСКОЙ ФЕДЕРАЦИИ',
            'min_mark' => 3,
            'subblocks' => [
                [
                    'name' => '',
                    'min_mark' => 3,
                    'tasks' => [
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task17.json')), true),
                        ],
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task18.json')), true),
                        ],
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task19.json')), true),
                        ],
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task20.json')), true),
                        ],
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task21.json')), true),
                        ],
                        [
                            'type' => TaskType::SingleChoice,
                            'mark' => 1,
                            'variants' => json_decode(file_get_contents(base_path($this->path.'task22.json')), true),
                            'postscriptum' => 'Верно ли это суждение?',
                        ],
                    ],
                ],
            ],
        ];
    }
}
