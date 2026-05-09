<?php
class IssueController extends Controller {
    public function index() {
        // Issue view is handled via modal (write-us.php) in the header.
        // Direct access to /issue redirects to home.
        header('location:' . BASE_URL);
        exit;
    }
}
