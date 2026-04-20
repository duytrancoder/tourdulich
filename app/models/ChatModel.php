<?php
require_once(ROOT . '/core/Model.php');

class ChatModel extends Model {
    
    // User Methods
    public function getMessagesByEmail($email) {
        $sql = "SELECT * FROM tblchat WHERE UserEmail = :email ORDER BY CreatedAt ASC";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendMessage($email, $sender, $message) {
        $sql = "INSERT INTO tblchat (UserEmail, Sender, Message) VALUES (:email, :sender, :message)";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':sender', $sender, PDO::PARAM_STR);
        $query->bindParam(':message', $message, PDO::PARAM_STR);
        $query->execute();
        return $this->db->lastInsertId();
    }
    
    // Admin Methods
    public function getListUsers() {
        // Get all users who have chat messages, sorted by latest message first
        $sql = "SELECT u.FullName, u.MobileNumber, u.EmailId, 
                MAX(c.CreatedAt) as LastMessageTime,
                SUM(CASE WHEN c.Sender = 'user' AND c.IsRead = 0 THEN 1 ELSE 0 END) as UnreadCount
                FROM tblusers u 
                JOIN tblchat c ON u.EmailId = c.UserEmail 
                GROUP BY u.EmailId 
                ORDER BY LastMessageTime DESC";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function markMessagesAsRead($email, $sender) {
        // If admin views, mark 'user' messages as read. If user views, mark 'admin' messages as read.
        $sql = "UPDATE tblchat SET IsRead = 1 WHERE UserEmail = :email AND Sender = :sender AND IsRead = 0";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':sender', $sender, PDO::PARAM_STR);
        $query->execute();
    }
    
    public function getUnreadCount($email, $sender) {
        $sql = "SELECT COUNT(*) as cnt FROM tblchat WHERE UserEmail = :email AND Sender = :sender AND IsRead = 0";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':sender', $sender, PDO::PARAM_STR);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['cnt'] : 0;
    }
}
