<?php
require_once __DIR__ . '/BaseModel.php';

class FavouriteModel extends BaseModel
{
    public function __construct()
    {
        if (!isset($_SESSION['favourites'])) {
            $_SESSION['favourites'] = [];
        }
    }

    public function add($productId)
    {
        if (!in_array($productId, $_SESSION['favourites'])) {
            $_SESSION['favourites'][] = $productId;
        }
    }

    public function remove($productId)
    {
        $_SESSION['favourites'] = array_filter($_SESSION['favourites'], function ($id) use ($productId) {
            return $id != $productId;
        });
    }

    public function getAll()
    {
        return $_SESSION['favourites'];
    }
}
