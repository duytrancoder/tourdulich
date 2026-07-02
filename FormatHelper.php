<?php
// Ham ho tro format hien thi thong tin tour du lich
function formatTourStatus($status) {
    if ($status == 1) {
        return '<span class="badge bg-success">Con trong</span>';
    }
    return '<span class="badge bg-danger">Het cho</span>';
}