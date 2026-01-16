<?php

class HomeController extends Controller {
    public function index() {
        // Load the package model
        $packageModel = $this->model('PackageModel');

        // Pagination settings
        $toursPerPage = 6;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($currentPage - 1) * $toursPerPage;

        // Get data from the model
        $packages = $packageModel->getFeaturedPackagesPaginated($toursPerPage, $offset);
        $totalTours = $packageModel->getTotalToursCount();
        $totalPages = ceil($totalTours / $toursPerPage);
        $locations = $packageModel->getDistinctLocations();

        // Prepare data for the view
        $data = [
            'packages' => $packages,
            'locations' => $locations,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalTours' => $totalTours
        ];

        // Load the view and pass data
        $this->view('home/index', $data);
    }
}
