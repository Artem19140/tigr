<?php

use App\Enums\EmployeeRole;

return [
    EmployeeRole::Operator->value => 'Оператор',
    EmployeeRole::CenterAdmin->value => 'Администратор центра',
    EmployeeRole::Director->value => 'Директор',
    EmployeeRole::Examiner->value => 'Экзаменатор'
];
