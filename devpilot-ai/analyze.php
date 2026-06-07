<?php

header("Content-Type: application/json");

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data["code"])) {
        throw new Exception("No code provided");
    }

    $code = $data["code"];

    $apiKey = "API_KEY_HERE"; // Replace with your actual API key

    $prompt = "
You are an AI Production Copilot.

Analyze this code and provide:

1. Code Review
2. Bugs
3. Security Issues
4. Test Cases
5. Deployment Risk
6. CI/CD YAML Suggestion

Code:
$code
";

    $requestData = [
        "contents" => [
            [
                "parts" => [
                    [
                        "text" => $prompt
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,
    "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS,
    json_encode($requestData));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("API request failed: " . curl_error($ch));
    }

    curl_close($ch);

    $responseData = json_decode($response, true);

    if (!$responseData) {
        throw new Exception("Invalid API response format");
    }

    if (isset($responseData["error"])) {
        throw new Exception("API Error: " . $responseData["error"]["message"]);
    }

    if (!isset($responseData["candidates"][0]["content"]["parts"][0]["text"])) {
        throw new Exception("Unexpected response structure from API");
    }

    $result =
    $responseData["candidates"][0]["content"]["parts"][0]["text"];

    echo json_encode([
        "result" => $result
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}

?>