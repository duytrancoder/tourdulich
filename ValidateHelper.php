<?php
// File kiem tra tinh hop le cua du lieu dau vao
function validateRegisterForm($phone, $email) {
    if (strlen($phone) < 10) {
        return "So dien thoai khong hop le!";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Email khong dung dinh dang!";
    }
    return true;
}