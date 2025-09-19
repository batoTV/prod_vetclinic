<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consent Form</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 0; }
        .content { margin-top: 20px; }
        .content p { margin-bottom: 15px; line-height: 1.5; }
        .details { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .details td { padding: 5px; }
        .signature-section { margin-top: 40px; }
        .signature-line { border-bottom: 1px solid #000; padding-bottom: 5px; }
        .signature-img { max-width: 200px; max-height: 80px; }
        .financial-policy { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rosevet Animal Clinic</h1>
        <p>City of General Trias, Cavite</p>
        <h2>CONSENT FORM</h2>
        <p>(Vaccination/Medication/Hospitalization)</p>
    </div>

    <table class="details">
        <tr>
            <td><strong>Owner's Name:</strong> {{ $ownerName }}</td>
            <td><strong>Pet's Name:</strong> {{ $petName }}</td>
        </tr>
    </table>

    <div class="content">
        <p>I, the undersigned, certify that I am the owner or authorized agent for the pet named on this form. I hereby authorize the veterinarians and staff of Rosevet Animal Clinic to perform necessary examinations, vaccinations, medications, and treatments for this pet.</p>
        <p>I authorize the veterinarians and their designated assistants to perform diagnostic procedures, administer treatments, and perform emergency surgical procedures as deemed necessary for the health of my pet based on examination findings and in accordance with their professional judgment.</p>
        <p>I understand that all medical and surgical procedures carry inherent risks, which have been explained to me by the attending veterinarian.</p>
        <p>I understand that all treatments will be performed with due care and in accordance with prevailing standards of veterinary medicine. I acknowledge that no guarantee has been made regarding the outcome of any treatment or procedure.</p>
        <p>I agree to seek immediate veterinary care if lameness, inappetence, diarrhea, vomiting, or other adverse clinical signs are observed in my pet following any procedure. Having been informed of the potential risks and precautions, I agree to hold the veterinary staff harmless for such conditions.</p>
        <p class="financial-policy">FINANCIAL POLICY: I understand that there are strictly NO RETURNS or REFUNDS for procedures that have been performed or for medicines that have been purchased.</p>
    </div>

    <div class="signature-section">
        <table style="width: 100%;">
            <tr>
                <td class="signature-line" style="width: 50%;">
                    <img src="{{ $signature }}" alt="Signature" class="signature-img">
                    <br>
                    {{ $ownerName }}
                </td>
                <td class="signature-line" style="width: 50%;">{{ $date }}</td>
            </tr>
            <tr>
                <td>Signature of Owner / Representative</td>
                <td>Date</td>
            </tr>
        </table>
        @if ($notes)
            <p style="margin-top: 20px;"><strong>Notes:</strong><br>{{ $notes }}</p>
        @endif
    </div>
</body>
</html>