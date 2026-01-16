<?php

class Controller {
    /**
     * Load a model file.
     * @param string $model The name of the model.
     * @return object The model object.
     */
    public function model($model) {
        if (file_exists(APP . '/models/' . $model . '.php')) {
            require_once APP . '/models/' . $model . '.php';
            return new $model();
        }
        return null;
    }

    /**
     * Load a view file.
     * @param string $view The name of the view file.
     * @param array $data Data to be extracted for the view.
     */
    public function view($view, $data = []) {
        if (file_exists(APP . '/views/' . $view . '.php')) {
            // Extract data to be used in the view
            extract($data);
            
            require_once APP . '/views/' . $view . '.php';
        } else {
            // If the view does not exist, show an error
            die('View does not exist: ' . $view);
        }
    }

    /**
     * Format giá VNĐ với dấu phân cách nghìn
     * @param int|float $vndPrice Giá gốc (VNĐ)
     * @return string Giá đã định dạng kèm đơn vị
     */
    public static function formatVND($vndPrice) {
        return number_format($vndPrice, 0, ',', '.') . ' vnđ';
    }
}
