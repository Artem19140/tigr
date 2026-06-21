<?php

namespace App\Modules\Exam;

use App\Modules\Exam\ProtocolCommentRules;
use App\Exceptions\BusinessException;
use App\Models\Exam;
use App\Support\Audit;

class UpdateProtocolComment
{
    public function __construct(
        protected ProtocolCommentRules $protocolCommentRules,
        protected Audit $audit
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
        $this->audit->log(
            'update_protocol_comment',
            $exam, 
            [
                'changes' => [
                    'before' => $oldValue,
                    'after' => $exam->protocol_comment
                ],
            ]
        );
    }

    protected function log(Exam $exam, string $oldValue)
    {
        
    }
}
