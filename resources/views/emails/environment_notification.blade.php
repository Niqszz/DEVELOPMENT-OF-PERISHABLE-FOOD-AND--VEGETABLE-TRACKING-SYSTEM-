<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Environment Alert</title>
</head>
<body>
    <h1>Environment Alert for Product: {{ $product->name }}</h1>

    <p>Dear {{ $product->user->name }},</p>

    <p>This is an automated notification to inform you that the environment conditions for your product ({{ $product->name }}) are unsuitable.</p>

    <p>Current temperature: {{ $temperature }}°C</p>
    <p>Current humidity: {{ $humidity }}%</p>

    <p>Please take appropriate action to address these conditions.</p>

    <p>Thank you,</p>
    <p>Your Company</p>
</body>
</html>
