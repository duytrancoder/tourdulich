<?php
class PackageController extends Controller {
    public function index() {
        $packageModel = $this->model('PackageModel');

        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $locationFilter = isset($_GET['location']) ? trim($_GET['location']) : '';
        $priceFilter = isset($_GET['price']) ? $_GET['price'] : '';

        $packages = $packageModel->getFilteredPackages($keyword, $locationFilter, $priceFilter);
        $locations = $packageModel->getDistinctLocations();

        $data = [
            'packages' => $packages,
            'locations' => $locations,
            'keyword' => $keyword,
            'locationFilter' => $locationFilter,
            'priceFilter' => $priceFilter
        ];

        $this->view('package/index', $data);
    }

    public function details($id = 0) {
        $packageModel = $this->model('PackageModel');
        $package = $packageModel->getPackageById($id);

        $itineraryModel = $this->model('ItineraryModel');
        $itineraries = $itineraryModel->getByPackageId($id);

        $data = [
            'package' => $package,
            'itineraries' => $itineraries,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);


        $this->view('package/details', $data);
    }

    public function book($id = 0) {
        if (strlen($_SESSION['login']) == 0) {
            $_SESSION['error'] = "Vui lòng đăng nhập để đặt tour";
            header('Location: ' . BASE_URL);
            exit;
        }

        if (isset($_POST['submit2'])) {
            $pid = intval($id);
            $useremail = $_SESSION['login'];
            $departureDate = trim($_POST['departuredate'] ?? '');
            $comment = trim($_POST['comment'] ?? '');

            // Validate inputs
            if (empty($departureDate)) {
                $_SESSION['error'] = "Vui lòng chọn ngày khởi hành";
                header('Location: ' . BASE_URL . 'package/details/' . $pid);
                exit;
            }

            // Validate date
            $departureTimestamp = strtotime($departureDate);
            $todayTimestamp = strtotime('today');

            if ($departureTimestamp === false) {
                $_SESSION['error'] = "Ngày không hợp lệ";
                header('Location: ' . BASE_URL . 'package/details/' . $pid);
                exit;
            }

            if ($departureTimestamp < $todayTimestamp) {
                $_SESSION['error'] = "Ngày khởi hành không thể là ngày trong quá khứ";
                header('Location: ' . BASE_URL . 'package/details/' . $pid);
                exit;
            }

            if ($pid <= 0) {
                $_SESSION['error'] = "Gói tour không hợp lệ";
                header('Location: ' . BASE_URL . 'package');
                exit;
            }

            // Use same date for both fromdate and todate for database compatibility
            $bookingModel = $this->model('BookingModel');
            $lastInsertId = $bookingModel->createBooking($pid, $useremail, $departureDate, $departureDate, $comment);

            if ($lastInsertId) {
                $_SESSION['msg'] = "Đặt tour thành công.";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
            }
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }
        header('Location: ' . BASE_URL . 'package');
        exit;
    }
}
