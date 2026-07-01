<?php
namespace Api\Core;

class Response {
    /**
     * Send a success JSON response
     * 
     * @param mixed $data Data to send in the response
     * @param string $message Success message
     * @param int $statusCode HTTP Status Code (default 200)
     */
    public static function success($data = null, $message = "Success", $statusCode = 200) {
         self::send([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Send an error JSON response
     * 
     * @param string $message Error message
     * @param mixed $errors Detailed errors (e.g. validation errors)
     * @param int $statusCode HTTP Status Code (default 400)
     */
    public static function error($message = "Error", $errors = null, $statusCode = 400) {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        self::send($response, $statusCode);
    }

    private static function send(array $payload, $statusCode) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
