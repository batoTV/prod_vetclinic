<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Non-Consent Form</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details-table td { padding: 5px; }
        .reason { border-bottom: 1px solid #000; padding: 10px; min-height: 40px; }
        .content p { margin-bottom: 15px; line-height: 1.5; }
        .signature-section { margin-top: 40px; }
        .signature-line { border-bottom: 1px solid #000; padding-bottom: 5px; }
        .signature-img { max-width: 200px; max-height: 80px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rosevet Animal Clinic</h1>
        <p>Lot 11A Lot 19A Brookside Lane, Arnaldo Highway, Barangay San Francisco, City of General Trias, Cavite</p>
        <h2>NON-CONSENT FORM</h2>
    </div>

    <table class="details-table">
        <tr>
            <td>
    <strong>Owner's Name:</strong> {{ $first_Name }} {{ $last_Name }}
</td>
            <td><strong>Pet's Name:</strong> {{ $petName }}</td>
        </tr>
    </table>

    <div class="content">
        <p>I am the owner/authorized person for the animal described on this form and I **do not want** my pet(s) to undergo the following procedure(s):</p>
        <div class="reason">
            <p>{{ $notes }}</p>
        </div>
        <p>As being advised by the attending veterinarians, staff, or employees, I understand all the precautions that were explained and the risks that might arise in the future due to this decision.</p>
        <p>I further understand and take full responsibility for the possible outcome.</p>
    </div>

    <div class="signature-section">
        <table style="width: 100%; margin-top: 50px; border-spacing: 20px 0;">
    <tr>
        {{-- Signature Column --}}
        <td style="width: 60%; vertical-align: bottom; text-align: center;">
            <img src="{{ $signature }}" style="width: 200px; height: 50px; display: block; margin: 0 auto;">
            <hr style="border-top: 1px solid black; margin: 0;">
            <p style="margin-top: 5px;">Signature of Owner / Representative</p>
        </td>
        
        {{-- Date Column --}}
        <td style="width: 40%; vertical-align: bottom; text-align: center;">
            <p style="margin-bottom: 5px;">{{ $date }}</p>
            <hr style="border-top: 1px solid black; margin: 0;">
            <p style="margin-top: 5px;">Date</p>
        </td>
    </tr>
</table>
    </div>
</body>
</html>