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
        http_response_code($statusCode);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    /**
     * Send an error JSON response
     * 
     * @param string $message Error message
     * @param mixed $errors Detailed errors (e.g. validation errors)
     * @param int $statusCode HTTP Status Code (default 400)
     */
    public static function error($message = "Error", $errors = null, $statusCode = 400) {
        http_response_code($statusCode);
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response);
        exit;
    }
}
