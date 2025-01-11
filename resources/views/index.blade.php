<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Japanese NLP Parser</title>

  <!-- Include CSS and JS using Vite -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Optional: Add some basic styling -->
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      margin: 0;
      padding: 20px;
    }

    h1 {
      color: #333;
    }

    form {
      margin-top: 20px;
    }

    textarea {
      width: 100%;
      height: 100px;
      padding: 10px;
      font-size: 16px;
    }

    button {
      margin-top: 10px;
      padding: 10px 20px;
      font-size: 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
    }

    pre {
      background-color: #e9ecef;
      padding: 15px;
      border-radius: 4px;
    }
  </style>
</head>

<body>
  <div id="app">
    <parse-component></parse-component>
  </div>
</body>

</html>
