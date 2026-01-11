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

        $data = [
            'package' => $package,
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
            $fromdate = trim($_POST['fromdate'] ?? '');
            $todate = trim($_POST['todate'] ?? '');
            $comment = trim($_POST['comment'] ?? '');

            // Validate inputs
            if (empty($fromdate) || empty($todate)) {
                $_SESSION['error'] = "Vui lòng chọn ngày đi và ngày về";
                header('Location: ' . BASE_URL . 'package-details/' . $pid);
                exit;
            }

            // Validate dates
            $fromTimestamp = strtotime($fromdate);
            $toTimestamp = strtotime($todate);
            $todayTimestamp = strtotime('today');

            if ($fromTimestamp === false || $toTimestamp === false) {
                $_SESSION['error'] = "Ngày không hợp lệ";
                header('Location: ' . BASE_URL . 'package-details/' . $pid);
                exit;
            }

            if ($fromTimestamp < $todayTimestamp) {
                $_SESSION['error'] = "Ngày đi không thể là ngày trong quá khứ";
                header('Location: ' . BASE_URL . 'package-details/' . $pid);
                exit;
            }

            if ($toTimestamp < $fromTimestamp) {
                $_SESSION['error'] = "Ngày về phải sau ngày đi";
                header('Location: ' . BASE_URL . 'package-details/' . $pid);
                exit;
            }

            if ($pid <= 0) {
                $_SESSION['error'] = "Gói tour không hợp lệ";
                header('Location: ' . BASE_URL . 'package');
                exit;
            }

            $bookingModel = $this->model('BookingModel');
            $lastInsertId = $bookingModel->createBooking($pid, $useremail, $fromdate, $todate, $comment);

            if ($lastInsertId) {
                $_SESSION['msg'] = "Đặt tour thành công.";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
            }
            header('Location: ' . BASE_URL . 'package-details/' . $pid);
            exit;
        }
        header('Location: ' . BASE_URL . 'package');
        exit;
    }
}
