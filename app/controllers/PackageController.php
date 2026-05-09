<?php
class PackageController extends Controller {
    public function index() {
        $packageModel = $this->model('PackageModel');

        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $typeFilter = isset($_GET['type']) ? trim($_GET['type']) : '';
        $priceFilter = isset($_GET['price']) ? $_GET['price'] : '';

        $packages = $packageModel->getFilteredPackages($keyword, $typeFilter, $priceFilter);
        $types = $packageModel->getDistinctTypes();

        $data = [
            'packages' => $packages,
            'types' => $types,
            'keyword' => $keyword,
            'typeFilter' => $typeFilter,
            'priceFilter' => $priceFilter
        ];

        $this->view('package/index', $data);
    }

    public function details($id = 0) {
        $packageModel = $this->model('PackageModel');
        $package = $packageModel->getPackageById($id);

        $itineraryModel = $this->model('ItineraryModel');
        $itineraries = $itineraryModel->getByPackageId($id);

        $reviewModel = $this->model('ReviewModel');
        $reviews = $reviewModel->getReviewsByPackage($id);
        $averageInfo = $reviewModel->getAverageRating($id);
        $ratingBreakdown = $reviewModel->getRatingBreakdown($id);

        $data = [
            'package' => $package,
            'itineraries' => $itineraries,
            'reviews' => $reviews,
            'averageInfo' => $averageInfo,
            'ratingBreakdown' => $ratingBreakdown,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);


        $this->view('package/details', $data);
    }


}
