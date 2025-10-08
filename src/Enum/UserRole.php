<?php
// src/Enum/UserRole.php
namespace App\Enum;

enum UserRole: string
{
    case Admin = 'Admin';
    case User = 'User';
}
