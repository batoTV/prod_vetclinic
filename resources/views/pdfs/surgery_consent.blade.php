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
        <p>(Surgery/Hospitalization/Boarding)</p>
    </div>

    <table class="details">
        <tr>
            <td><strong>Owner's Name:</strong> {{ $ownerName }}</td>
            <td><strong>Pet's Name:</strong> {{ $petName }}</td>
        </tr>
    </table>

    <div class="content">
        <p>I am the owner or agent for the animal(s) described on this form and have the authority to execute this consent. I request that the veterinarians, agents, and employees of Rosevet Animal Clinic perform the services which are necessary to the examination, medication, and treatment of the animal specifically described and identified on this form.</p>
        <p>I authorize the veterinarians on duty (and the assistants they designate) to examine the animal(s) and to administer medical treatment or emergency surgical treatment which is considered therapeutically and/or diagnostically necessary on the basis of the findings during the course of examination. Therefore, I hereby consent to and authorize the performance of such procedure(s) as are necessary and in the exercise of the veterinarian's professional judgment.</p>
        <p>I further understand that any animal found to be infected with either external or internal parasites will be treated for sums at my expense.</p>
        <p>I understand that the treatment of the patient will be conducted with due care and in accordance with the prevailing standards of competency in Veterinary Medicine. I certify that no guarantee or assurance has been made as to the result that may be obtained through the course of the treatment undertaken by the veterinarians, agents, or employees of Rosevet Animal Clinic.</p>
        <p>I assume financial responsibility for all charges incurred to the patient for services rendered and understand the full payment is required upon discharge. In case of non-payment, I am aware that Rosevet Animal Clinic will charge the cost of collecting the debt on the amount owed for services. This includes the collections company's charges, attorney's fees and interest of 1.5% per month (18%) annum.</p>
        <p>I understand that updates on my pet's condition while confined at Rosevet Animal Clinic will be provided at scheduled intervals.</p>
        <p>I understand that a written estimate of charges is available with reasonable time at my request. I also consent to the release of medical information.</p>
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