<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; color: #0f172a; -webkit-font-smoothing: antialiased;">

    <!-- Zone globale -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f1f5f9; padding: 40px 15px;">
        <tr>
            <td align="center">

                <!-- Card Principale -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 580px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02); border: 1px solid #e2e8f0;">

                    <!-- Header avec Bandeau Vert d'Accent -->
                    <tr>
                        <td style="background-color: #0a3663; border-top: 5px solid #008a3c; padding: 32px 40px; text-align: left;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td>
                                        <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">
                                            AFRIYOL
                                        </h1>
                                        <p style="color: #93c5fd; margin: 4px 0 0 0; font-size: 12px; font-weight: 500; letter-spacing: 0.5px;">
                                            AFRICAN YOUNG LEADERS
                                        </p>
                                    </td>
                                    <td align="right" style="vertical-align: middle;">
                                        <span style="background-color: rgba(255, 255, 255, 0.1); color: #ffffff; font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Nouveau Message
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Contenu du mail -->
                    <tr>
                        <td style="padding: 36px 40px;">

                            <!-- Information d'alerte UI -->
                            <p style="font-size: 15px; line-height: 1.6; color: #334155; margin: 0 0 24px 0;">
                                Un nouveau formulaire de contact vient d'être soumis sur la plateforme.
                            </p>

                            <!-- Blocs UI : Détails Expéditeur -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding-bottom: 12px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    Expéditeur
                                                </td>
                                                <td align="right" style="padding-bottom: 12px; font-size: 12px; color: #94a3b8;">
                                                    {{ $contact->created_at->format('d/m/Y à H:i') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="font-size: 16px; font-weight: 700; color: #0a3663; padding-bottom: 4px;">
                                                    {{ $contact->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="font-size: 14px; color: #0c4a80; padding-bottom: 12px;">
                                                    <a href="mailto:{{ $contact->email }}" style="color: #0c4a80; text-decoration: none; font-weight: 500;">
                                                        {{ $contact->email }}
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="border-top: 1px solid #e2e8f0; padding-top: 12px;">
                                                    <span style="font-size: 12px; color: #64748b;">Objet : </span>
                                                    <span style="font-size: 13px; font-weight: 600; color: #0f172a; background-color: #e2e8f0; padding: 3px 8px; border-radius: 4px;">
                                                        {{ ucfirst($contact->subject) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Bloc UI : Message -->
                            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                                Contenu de la demande
                            </div>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-left: 3px solid #008a3c; border-top: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; border-radius: 0 8px 8px 0; margin-bottom: 32px;">
                                <tr>
                                    <td style="padding: 16px 20px; font-size: 14px; line-height: 1.7; color: #334155; white-space: pre-line;">
                                        {{ $contact->message }}
                                    </td>
                                </tr>
                            </table>

                            <!-- Bouton Call To Action -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <a href="mailto:{{ $contact->email }}" style="background-color: #008a3c; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 14px; display: inline-block; box-shadow: 0 4px 6px -1px rgba(0, 138, 60, 0.2);">
                                            Répondre directement
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #071e36; padding: 24px 40px; text-align: center;">
                            <p style="margin: 0 0 4px 0; font-size: 12px; color: #94a3b8;">
                                Notification automatique générée par le système
                            </p>
                            <p style="margin: 0; font-size: 13px; font-weight: 600; color: #ffffff;">
                                {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>

                </table>

                <!-- Subcopy hors de la carte -->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width: 580px; margin-top: 16px;">
                    <tr>
                        <td style="text-align: center; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                            Si le bouton ne s'ouvre pas, répondez à l'adresse :
                            <a href="mailto:{{ $contact->email }}" style="color: #64748b; text-decoration: underline;">
                                {{ $contact->email }}
                            </a>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>
