<?php
require_once __DIR__ . '/../models/FavouritesModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class FavouriteApiController
{
    private $favouriteModel;
    private $productModel;

    public function __construct()
    {
        $this->favouriteModel = new FavouriteModel();
        $this->productModel = new ProductModel();
    }

    public function addToFavourites()
    {
        $productId = $_POST['product_id'] ?? null;

        if ($productId) {
            $this->favouriteModel->add($productId);
            echo json_encode(['status' => 'success', 'message' => 'Added to favourites']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Product ID missing']);
        }
    }

    public function removeFavourite($id)
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $userId = $_SESSION['user_id'];

        $stmt = $this->pdo->prepare("DELETE FROM favourites WHERE user_id = :user_id AND product_id = :product_id");
        $success = $stmt->execute([
            ':user_id' => $userId,
            ':product_id' => $id
        ]);

        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Product removed from favourites.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to remove product.']);
        }
    }


    public static function getFavourites()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['favourites']) || empty($_SESSION['favourites'])) {
            echo json_encode(['status' => 'success', 'favourites' => []]);
            return;
        }

        $favourites = $_SESSION['favourites'];
        $productModel = new ProductModel();
        $favouriteProducts = [];

        foreach ($favourites as $productId) {
            $product = $productModel->getProductById($productId);
            if ($product) {
                $favouriteProducts[] = $product;
            }
        }

        echo json_encode(['status' => 'success', 'favourites' => $favouriteProducts]);
    }
}
