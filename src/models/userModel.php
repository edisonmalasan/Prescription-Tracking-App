<?php

class UserModel {
    public $user_id;
    public $last_name;
    public $first_name;
    public $role;
    public $email;
    public $contactno;
    public $pass_hash;
    public $address;
    public $created_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->user_id = $data['user_id'] ?? null;
            $this->last_name = $data['last_name'] ?? '';
            $this->first_name = $data['first_name'] ?? '';
            $this->role = $data['role'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->contactno = $data['contactno'] ?? '';
            $this->pass_hash = $data['pass_hash'] ?? '';
            $this->address = $data['address'] ?? '';
            $this->created_at = $data['created_at'] ?? null;
        }
    }

    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function toArray() {
        return [
            'user_id' => $this->user_id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'role' => $this->role,
            'email' => $this->email,
            'contactno' => $this->contactno,
            'address' => $this->address,
            'created_at' => $this->created_at
        ];
    }
}
?>
