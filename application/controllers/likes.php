<?php
class Likes extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function status($id) {
        login_required();
        $node = node_load($id);
        like_status($node);
        redirect_back_or_default();
    }
}
