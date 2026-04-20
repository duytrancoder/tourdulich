<?php
class ChatController extends Controller {

    // --- USER API ---

    public function getMessages() {
        header('Content-Type: application/json');
        
        if (empty($_SESSION['login'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $email = $_SESSION['login'];
        $chatModel = $this->model('ChatModel');
        
        // Mark admin messages as read by user
        $chatModel->markMessagesAsRead($email, 'admin');
        
        $messages = $chatModel->getMessagesByEmail($email);
        echo json_encode(['status' => 'success', 'data' => $messages]);
    }

    public function sendMessage() {
        header('Content-Type: application/json');
        
        if (empty($_SESSION['login'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $email = $_SESSION['login'];
        $message = trim($_POST['message'] ?? '');
        
        if (empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Message is empty']);
            return;
        }

        $chatModel = $this->model('ChatModel');
        $id = $chatModel->sendMessage($email, 'user', $message);
        
        if ($id) {
            echo json_encode(['status' => 'success', 'id' => $id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send']);
        }
    }
    
    public function getUnreadCount() {
        header('Content-Type: application/json');
        if (empty($_SESSION['login'])) {
            echo json_encode(['status' => 'success', 'count' => 0]);
            return;
        }
        $email = $_SESSION['login'];
        $chatModel = $this->model('ChatModel');
        $count = $chatModel->getUnreadCount($email, 'admin');
        echo json_encode(['status' => 'success', 'count' => $count]);
    }

    // --- ADMIN API ---

    public function adminGetListUsers() {
        header('Content-Type: application/json');
        if (empty($_SESSION['alogin'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not admin']);
            return;
        }

        $chatModel = $this->model('ChatModel');
        $users = $chatModel->getListUsers();
        echo json_encode(['status' => 'success', 'data' => $users]);
    }

    public function adminGetMessages() {
        header('Content-Type: application/json');
        if (empty($_SESSION['alogin'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not admin']);
            return;
        }

        $email = $_GET['email'] ?? '';
        if (empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Email missing']);
            return;
        }

        $chatModel = $this->model('ChatModel');
        // Mark user messages as read by admin
        $chatModel->markMessagesAsRead($email, 'user');
        
        $messages = $chatModel->getMessagesByEmail($email);
        echo json_encode(['status' => 'success', 'data' => $messages]);
    }

    public function adminSendMessage() {
        header('Content-Type: application/json');
        if (empty($_SESSION['alogin'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not admin']);
            return;
        }

        $email = $_POST['email'] ?? '';
        $message = trim($_POST['message'] ?? '');

        if (empty($email) || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            return;
        }

        $chatModel = $this->model('ChatModel');
        $id = $chatModel->sendMessage($email, 'admin', $message);
        
        if ($id) {
            echo json_encode(['status' => 'success', 'id' => $id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send']);
        }
    }
}
