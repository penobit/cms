<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
</head>

<body>
    <style>
        html,
        body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        .container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f1f1f1;
            font-family: Arial, sans-serif;
            flex-direction: column;
            gap: 1rem;
        }

        h1 {
            font-size: 2rem;
            color: #c00;
            text-align: center;
            margin: 0;
        }

        p {
            color: #a00;
        }
    </style>

    <div class="container">

        <svg xmlns="http://www.w3.org/2000/svg" width="250px" height="250px" viewBox="0 0 24 24" fill="none"
            stroke="#c00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="feather feather-x-circle">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6L12 14" />
            <circle cx="12" cy="18" r=".25" />
        </svg>
        <h1>404 Not Found</h1>
        <p>The requested URL was not found on this server.</p>
    </div>
</body>

</html>