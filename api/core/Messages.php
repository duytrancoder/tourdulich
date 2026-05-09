<?php
namespace Api\Core;

class Messages {
    // --- General / System ---
    const SUCCESS = "Thành công";
    const ERROR_SYSTEM = "Lỗi hệ thống, vui lòng thử lại sau";
    const ERROR_DATABASE = "Lỗi hệ thống khi truy vấn dữ liệu";
    const ERROR_INVALID_DATA = "Dữ liệu không hợp lệ";
    const ERROR_FORBIDDEN = "Bạn không có quyền thực hiện tác vụ này";
    const ERROR_UNAUTHORIZED = "Chưa đăng nhập hoặc phiên làm việc hết hạn";
    const ERROR_MISSING_INFO = "Vui lòng nhập đầy đủ thông tin";

    // --- Auth ---
    const AUTH_LOGIN_SUCCESS = "Đăng nhập thành công";
    const AUTH_LOGIN_ADMIN_SUCCESS = "Đăng nhập quản trị viên thành công";
    const AUTH_INVALID_CREDENTIALS = "Email hoặc mật khẩu không chính xác";
    const AUTH_INVALID_ADMIN_CREDENTIALS = "Tài khoản hoặc mật khẩu quản trị không chính xác";
    const AUTH_REGISTER_SUCCESS = "Đăng ký tài khoản thành công";
    const AUTH_EMAIL_EXISTS = "Email này đã được đăng ký sử dụng";
    const AUTH_EMAIL_INVALID = "Định dạng email không hợp lệ";
    const AUTH_PASSWORD_MIN = "Mật khẩu phải từ 6 ký tự trở lên";
    const AUTH_FORGOT_PWD_SENT = "Yêu cầu đã được ghi nhận. Vui lòng kiểm tra email của bạn.";

    // --- Tours / Packages ---
    const TOUR_FETCH_SUCCESS = "Lấy danh sách gói tour thành công";
    const TOUR_DETAIL_SUCCESS = "Lấy chi tiết gói tour thành công";
    const TOUR_NOT_FOUND = "Gói tour không tồn tại";
    const TOUR_CREATE_SUCCESS = "Tạo gói tour mới thành công";
    const TOUR_UPDATE_SUCCESS = "Cập nhật gói tour thành công";
    const TOUR_DELETE_SUCCESS = "Xóa gói tour thành công";
    const TOUR_IMAGE_INVALID = "Định dạng ảnh không hợp lệ (JPG, PNG, GIF)";

    // --- Booking ---
    const BOOKING_FETCH_SUCCESS = "Lấy danh sách đặt tour thành công";
    const BOOKING_DETAIL_SUCCESS = "Lấy chi tiết đặt tour thành công";
    const BOOKING_SUCCESS = "Đặt tour thành công";
    const BOOKING_UPDATE_SUCCESS = "Cập nhật trạng thái đặt tour thành công";
    const BOOKING_CANCEL_SUCCESS = "Hủy đặt tour thành công";
    const BOOKING_NOT_FOUND = "Đơn đặt tour không tồn tại";
    const BOOKING_CANCEL_DENIED = "Bạn chỉ được hủy tour trước giờ khởi hành 24h";

    // --- Issues / Enquiries ---
    const ISSUE_FETCH_SUCCESS = "Lấy danh sách yêu cầu hỗ trợ thành công";
    const ISSUE_SUBMIT_SUCCESS = "Gửi yêu cầu hỗ trợ thành công";
    const ISSUE_UPDATE_SUCCESS = "Cập nhật ghi chú xử lý thành công";
    const ENQUIRY_SUBMIT_SUCCESS = "Gửi yêu cầu liên hệ thành công";

    // --- Profile / Account ---
    const PROFILE_FETCH_SUCCESS = "Lấy thông tin tài khoản thành công";
    const PROFILE_UPDATE_SUCCESS = "Cập nhật thông tin hồ sơ thành công";
    const PWD_CHANGE_SUCCESS = "Thay đổi mật khẩu thành công";
    const PWD_CURRENT_WRONG = "Mật khẩu hiện tại không chính xác";
    const PWD_MISMATCH = "Mật khẩu xác nhận không khớp";

    // --- Review ---
    const REVIEW_SUBMIT_SUCCESS = "Gửi đánh giá thành công. Cảm ơn bạn!";
    const REVIEW_ALREADY_EXISTS = "Bạn đã thực hiện đánh giá cho tour này";
    const REVIEW_NOT_ALLOWED = "Bạn chỉ có thể đánh giá những tour đã hoàn thành";

    // --- Wishlist (Base for dynamic) ---
    const WISHLIST_ADDED = "Đã thêm vào danh sách yêu thích";
    const WISHLIST_REMOVED = "Đã xóa khỏi danh sách yêu thích";
    const WISHLIST_FETCH_SUCCESS = "Lấy danh sách yêu thích thành công";
}
