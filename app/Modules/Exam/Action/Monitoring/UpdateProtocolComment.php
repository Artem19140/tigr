<?php

namespace App\Modules\Exam\Action\Monitoring;

use App\Modules\Exam\Rules\ProtocolCommentRules;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use Illuminate\Support\Facades\Log;

class UpdateProtocolComment
{
    public function __construct(
        protected ProtocolCommentRules $protocolCommentRules
    ) {}

    public function execute(
        Exam $exam,
        string $protocolComment
    ){
        $result = $this->protocolCommentRules->check($exam);

        if($result->isNotAvailable()){
            throw new BusinessException($result->message());
        }

        $oldValue = $exam->protocol_comment ?? '';
        $exam->protocol_comment = $protocolComment;

        $exam->save();
        $this->log($exam, $oldValue);
    }

    protected function log(Exam $exam, string $oldValue)
    {
        Log::info('updated_protocol_comment', [
            'exam_id' => $exam->id,
            'changes' => [
                'before' => $oldValue,
                'after' => $exam->protocol_comment,
            ]
        ]);
    }
}
