<?php

require_once 'userModel.php';

class AdminModel extends UserModel {
    public $isAdmin;

    public function __construct($data = []) {
        parent::__construct($data);
        
        if (!empty($data)) {
            $this->isAdmin = $data['isAdmin'] ?? false;
        }
    }

    public function toArray() {
        $userData = parent::toArray();
        return array_merge($userData, [
            'isAdmin' => $this->isAdmin
        ]);
    }
}
?>
