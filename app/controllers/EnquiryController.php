<?php
class EnquiryController extends Controller {
    public function index() {
        $data = [
            'error' => null,
            'msg' => null
        ];
        $this->view('enquiry/index', $data);
    }
}
