<?php

namespace App\Modules\TaskVariant;

use App\Models\AttemptAnswer;
use App\Models\Employee;
use App\Models\ForeignNational;
use Illuminate\Support\Facades\Storage;

class ContentTransformer
{
    public function transform(
        array $content,
        AttemptAnswer $attemptAnswer,
        Employee | ForeignNational $actor
    ): array {
        foreach ($content as $key => $block) {
            if (!\is_array($block)) {
                continue;
            }

            if (isset($block['type'])) {
                $this->transformBlock(
                    $block,
                    $attemptAnswer,
                    $actor
                );
            }

            if (
                isset($block['children'])
                && \is_array($block['children'])
            ) {
                $block['children'] = $this->transform(
                    $block['children'],
                    $attemptAnswer,
                    $actor
                );
            }

            // if (
            //     isset($block['value'])
            //     && \is_array($block['value'])
            // ) {
            //     $block['value'] = $this->transform(
            //         $block['value'],
            //         $attemptAnswer,
            //         $actor
            //     );
            // }

            if (
                isset($block['row'])
                && \is_array($block['row'])
            ) {
                $block['row'] = $this->transform(
                    $block['row'],
                    $attemptAnswer,
                    $actor
                );
            }

            $content[$key] = $block;
        }

        return $content;
    }

    protected function transformBlock(
        array &$block,
        AttemptAnswer $attemptAnswer,
        Employee | ForeignNational $actor
    ): void {
        
        if ($this->isAudioBlock($block)) {
            $this->transformAudioBlock(
                $attemptAnswer,
                $actor,
                $block
            );
        }

        // if ($this->isImageBlock($block)) {
        //     $this->transformImageBlock($block);
        // }
    }

    protected function isAudioBlock(array $block): bool
    {
        return $block['type']  === 'audio';
    }

    protected function transformAudioBlock(
        AttemptAnswer $attemptAnswer,
        Employee | ForeignNational $actor,
        array &$block
    ): void {
        $audioNotPlayed = $attemptAnswer->audio_played_at === null;
        $isExaminer = $actor instanceof Employee;

        if ($isExaminer) {
            $block['audioPlayUrl'] = null;
            $block['url'] = Storage::disk('public')
                ->url($block['value']);
        } else {
            $block['audioPlayUrl'] = $audioNotPlayed
                ? route('attempts.answers.audio.update', [
                    'attempt' => $attemptAnswer->attempt_id,
                    'attempt_answer' => $attemptAnswer,
                ])
                : null;

            $block['url'] = $audioNotPlayed
                ? Storage::disk('public')->url($block['value'])
                : null;
        }

        unset($block['value']);
    }

    protected function isImageBlock(array $block): bool
    {
        return $block['type'] === 'image';
    }

    protected function transformImageBlock(array &$block): void
    {
        $block['url'] = Storage::disk('public')
            ->url($block['value']);

        unset($block['value']);
    } 
}