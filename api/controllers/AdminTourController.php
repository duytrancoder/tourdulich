<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Models\Tour;

class AdminTourController {

    public function __construct() {
        // Bảo vệ toàn bộ Admin routes bằng JWT Middleware
        $user = JWTHandler::verifyBearerToken();
        // In a real app, you would check if $user->role === 'admin'
        // Since we didn't implement Admin roles in DB yet, we just ensure they are logged in.
    }

    /**
     * GET /api/admin/tours
     */
    public function index() {
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $location = $_GET['location'] ?? '';

        $tourModel = new Tour();
        $tours = $tourModel->getAll($search, $type, $location);

        Response::success($tours, "Lấy danh sách tour thành công");
    }

    /**
     * POST /api/admin/tours
     */
    public function create() {
        // Validate FormData fields (from $_POST since content-type is multipart/form-data)
        $name = $_POST['packagename'] ?? '';
        $type = $_POST['packagetype'] ?? '';
        $location = $_POST['packagelocation'] ?? '';
        $duration = $_POST['tourduration'] ?? '';
        $price = $_POST['packageprice'] ?? 0;
        $features = $_POST['packagefeatures'] ?? '';
        $details = $_POST['packagedetails'] ?? '';

        if (empty($name) || empty($type) || empty($location) || empty($duration) || empty($features) || empty($details)) {
            Response::error("Vui lòng điền đầy đủ thông tin", null, 400);
        }

        if ($price <= 0) {
            Response::error("Giá gói tour không hợp lệ", null, 400);
        }

        $imageName = '';
        if (isset($_FILES['packageimage']) && $_FILES['packageimage']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['packageimage']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (!in_array(strtolower($filetype), $allowed)) {
                Response::error("Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif)", null, 400);
            }

            // Sanitized image name
            $imageName = preg_replace('/[^a-zA-Z0-9-_\.]/', '', basename($filename));
            
            // Note: In real setup, you use absolute path.
            $uploadPath = dirname(dirname(__DIR__)) . "/public/packageimages/" . $imageName;
            
            // Create dir if not exists
            if (!file_exists(dirname($uploadPath))) {
                mkdir(dirname($uploadPath), 0777, true);
            }

            if (!move_uploaded_file($_FILES['packageimage']['tmp_name'], $uploadPath)) {
                Response::error("Không thể lưu ảnh", null, 500);
            }
        } else {
            Response::error("Vui lòng chọn hình ảnh", null, 400);
        }

        $tourModel = new Tour();
        $id = $tourModel->create($name, $type, $location, $duration, $price, $features, $details, $imageName);

        if ($id) {
            // Xử lý lưu lộ trình (Itinerary)
            $itineraryData = $_POST['itineraryData'] ?? '';
            if (!empty($itineraryData)) {
                $itineraries = json_decode($itineraryData, true);
                if (is_array($itineraries)) {
                    foreach ($itineraries as $item) {
                        $tourModel->addItinerary($id, $item['timeLabel'], $item['activity'], $item['sortOrder']);
                    }
                }
            }
            Response::success(['id' => $id], "Tạo gói tour thành công", 201);
        } else {
            Response::error("Lỗi khi lưu vào CSDL", null, 500);
        }
    }

    /**
     * GET /api/admin/tours/{id}
     */
    public function show($id) {
        $tourModel = new Tour();
        $tour = $tourModel->getById($id);
        
        if ($tour) {
            $tour['itineraries'] = $tourModel->getItineraries($id);
            Response::success($tour, "Lấy chi tiết tour thành công");
        } else {
            Response::error("Gói tour không tồn tại", null, 404);
        }
    }

    /**
     * PUT /api/admin/tours/{id} (Cập nhật Tour & Itinerary)
     * Vì multipart/form-data không hỗ trợ PUT trong PHP mặc định, 
     * chúng ta sẽ xử lý PATCH/PUT bằng cách đọc stream hoặc dùng POST + _method override.
     * Tuy nhiên, trong context này, chúng ta sẽ viết update() nhận $_POST và $_FILES.
     */
    public function update($id) {
        // Validation
        $name = $_POST['packagename'] ?? '';
        $type = $_POST['packagetype'] ?? '';
        $location = $_POST['packagelocation'] ?? '';
        $duration = $_POST['tourduration'] ?? '';
        $price = $_POST['packageprice'] ?? 0;
        $features = $_POST['packagefeatures'] ?? '';
        $details = $_POST['packagedetails'] ?? '';

        if (empty($name) || empty($type) || empty($location) || empty($duration) || empty($features) || empty($details)) {
            Response::error("Vui lòng điền đầy đủ thông tin", null, 400);
        }

        $tourModel = new Tour();
        $existingTour = $tourModel->getById($id);
        if (!$existingTour) {
            Response::error("Gói tour không tồn tại", null, 404);
        }

        $data = [
            'PackageName' => $name,
            'PackageType' => $type,
            'PackageLocation' => $location,
            'TourDuration' => $duration,
            'PackagePrice' => $price,
            'PackageFetures' => $features,
            'PackageDetails' => $details
        ];

        // Xử lý ảnh
        if (isset($_FILES['packageimage']) && $_FILES['packageimage']['error'] === UPLOAD_ERR_OK) {
            $filename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', basename($_FILES['packageimage']['name']));
            $uploadPath = dirname(dirname(__DIR__)) . "/public/packageimages/" . $filename;
            
            if (move_uploaded_file($_FILES['packageimage']['tmp_name'], $uploadPath)) {
                $data['PackageImage'] = $filename;
            }
        }

        if ($tourModel->update($id, $data)) {
            // Xử lý Itinerary: Full Array Replacement
            $itineraryData = $_POST['itineraryData'] ?? '';
            if ($itineraryData !== '') {
                $itineraries = json_decode($itineraryData, true);
                if (is_array($itineraries)) {
                    $tourModel->clearItineraries($id);
                    foreach ($itineraries as $index => $item) {
                        $tourModel->addItinerary($id, $item['timeLabel'], $item['activity'], $index + 1);
                    }
                }
            }
            Response::success(null, "Cập nhật gói tour thành công");
        } else {
            Response::error("Lỗi khi cập nhật dữ liệu", null, 500);
        }
    }

    /**
     * DELETE /api/admin/tours/{id}
     */
    public function delete($id) {
        $tourModel = new Tour();
        if ($tourModel->delete($id)) {
            Response::success(null, "Xóa gói tour thành công");
        } else {
            Response::error("Không thể xóa gói tour", null, 500);
        }
    }
}
