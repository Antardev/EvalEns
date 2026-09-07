<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f7fb; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color:#f4f7fb; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 24px rgba(15, 23, 42, 0.08);">
                    <tr>
                        <td style="background:linear-gradient(135deg, #0f172a 0%, #5d7cd3 100%); padding:28px 32px; text-align:center;">
                            <img src="{{ asset('dashboard/evalens-logo.png') }}" alt="ÉvalENS" style="max-height:70px; width:auto; display:block; margin:0 auto;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h2 style="margin:0 0 12px; font-size:24px; color:#0f172a;">Réinitialiser votre mot de passe</h2>
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.6;">Bonjour{{ $user->prenom ? ' ' . $user->prenom : '' }},</p>
                            <p style="margin:0 0 20px; font-size:16px; line-height:1.6;">
                                Nous avons reçu une demande de réinitialisation du mot de passe associé à votre compte {{ config('app.name') }}.
                            </p>

                            <p style="margin:0 0 24px;">
                                <a href="{{ $resetUrl }}" style="display:inline-block; background-color:#2563eb; color:#ffffff; text-decoration:none; padding:12px 22px; border-radius:999px; font-weight:bold; font-size:15px;">
                                    Réinitialiser mon mot de passe
                                </a>
                            </p>

                            <p style="margin:0 0 16px; font-size:14px; line-height:1.6; color:#64748b;">
                                Ce lien est valable pendant {{ $expire }} minutes et ne peut être utilisé qu'une seule fois.
                            </p>
                            <p style="margin:0; font-size:13px; line-height:1.5; color:#64748b;">
                                Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
