<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos identifiants de connexion</title>
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
                            <h2 style="margin:0 0 12px; font-size:24px; color:#0f172a;">Bienvenue sur ÉvalENS</h2>
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.6;">Bonjour <strong>{{ $prenom }}</strong>,</p>
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.6;">
                                Un compte enseignant a été créé pour vous sur la plateforme de <strong>{{ $annexeNom }}</strong>.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin:20px 0; background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="margin:0 0 10px; font-size:15px; color:#475569;"><strong>Voici vos identifiants de connexion :</strong></p>
                                        <p style="margin:0 0 8px; font-size:15px; line-height:1.5;"><strong>Email :</strong> {{ $email }}</p>
                                        <p style="margin:0; font-size:15px; line-height:1.5;"><strong>Mot de passe temporaire :</strong> {{ $motDePasse }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 20px; font-size:15px; line-height:1.6;">
                                Merci de vous connecter et de <strong>changer ce mot de passe</strong> dès votre première connexion.
                            </p>

                            <p style="margin:0 0 24px;">
                                <a href="{{ url('/login') }}" style="display:inline-block; background-color:#2563eb; color:#ffffff; text-decoration:none; padding:12px 22px; border-radius:999px; font-weight:bold; font-size:15px;">
                                    Se connecter
                                </a>
                            </p>

                            <p style="margin:0; font-size:13px; line-height:1.5; color:#64748b;">
                                Si vous avez un problème de connexion, contactez l’administrateur de votre établissement.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
