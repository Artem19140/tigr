<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Приглашение в систему</title>
</head>

<body style="margin:0; padding:0; background:#f6f7fb; font-family: Arial, sans-serif;">

    <table role="presentation" width="100%" style="padding:40px 0;">
        <tr>
            <td align="center">

                <table role="presentation" width="600" style="background:#ffffff; border-radius:12px; padding:40px; box-shadow:0 4px 20px rgba(0,0,0,0.06);">

                    <tr>
                        <td style="text-align:center; padding-bottom:20px;">
                            <h1 style="margin:0; font-size:22px; color:#111;">
                                Здравствуйте, {{ $employee->name }} {{ $employee->patronymic }}!
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:center; padding-bottom:20px; color:#444; font-size:15px; line-height:1.6;">
                            Вам отправлено приглашение в систему <strong>«ТИГР»</strong>.
                            <br>
                            Для завершения регистрации перейдите по ссылке ниже.
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:20px 0;">
                            <a href="{{ $url }}"
                               style="display:inline-block; padding:12px 24px; background:#2563eb; color:#fff; text-decoration:none; border-radius:8px; font-size:14px;">
                                Завершить регистрацию
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align:center; font-size:12px; color:#888; padding-top:10px;">
                            Если кнопка не работает, скопируйте ссылку и откройте её в браузере:
                            <br>
                            <span style="word-break:break-all;">{{ $url }}</span>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>