<?php 
namespace App\Enums;

enum PermissionsEnum: string 
{ 
    case ApprovedVendor = 'ApprovedVendor';
    case SellProducts = 'SellProducts';
    case BuyProducts = 'BuyProducts';
}