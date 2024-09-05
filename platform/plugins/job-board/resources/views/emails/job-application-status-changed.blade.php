<!DOCTYPE html>
<html>
<head>
    <title>Job Application Status Updated</title>
</head>
<body>
    <p>Dear {{ $recruitmentProgress->nama_kandidat }},</p>

    <p>Your job application status has been updated to "Process".</p>

    <p>Please click the link below for further actions:</p>

    <a href="{{ $url }}">Click Here</a>

    <p>Thank you!</p>
</body>
</html>
