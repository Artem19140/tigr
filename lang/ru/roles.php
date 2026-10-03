<?php

use App\Enums\EmployeeRole;

return [
    EmployeeRole::Operator->value => 'Оператор',
    EmployeeRole::CenterAdmin->value => 'Администратор организации',
    EmployeeRole::Director->value => 'Директор',
    EmployeeRole::Examiner->value => 'Экзаменатор'
];
