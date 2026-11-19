<?php

namespace App\Modules\ExamConduct;

use App\Modules\ExamConduct\ProtocolCommentRules;
use App\Exceptions\BusinessException;
use App\Models\Exam;

class UpdateProtocolComment
{
    public function __construct(
        protected ProtocolCommentRules $protocolCommentRules
    ) {}

    public function execute(
        Exam $exam,
        string $protocolComment
    ){
        $result = $this->protocolCommentRules
            ->check($exam);

        if($result->isNotAvailable()){
            throw new BusinessException(
                $result->code()
            );
        }

        $oldValue = $exam->protocol_comment ?? '';
        
        $exam->update([
            'protocol_comment' => $protocolComment
        ]);
    }
}
